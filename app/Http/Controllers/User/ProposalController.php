<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NeedDispatch;
use App\Models\NeedProposal;
use App\Models\Notification;
use App\Models\ServiceRequest;
use App\Services\Matching\NeedDispatcher;
use App\Support\ContactMasker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Réponse de l'artisan à une demande publiée qu'il a reçue (cahier des charges §2.4) :
 * proposition de prix, proposition de visite (puis devis), ou refus.
 */
class ProposalController extends Controller
{
    public const DECLINE_REASONS = [
        'Pas disponible à cette date',
        'Trop loin de ma zone',
        'Hors de mes compétences',
        'Budget trop bas',
    ];

    public function store(Request $request, ServiceRequest $serviceRequest, NeedDispatcher $dispatcher): RedirectResponse
    {
        // L'artisan saisit l'heure de visite à l'heure de Kinshasa ; l'application stocke en UTC.
        if ($request->filled('visit_at') && is_string($request->input('visit_at'))) {
            try {
                $request->merge(['visit_at' => Carbon::parse($request->input('visit_at'), config('matching.timezone'))->utc()->toDateTimeString()]);
            } catch (\Throwable) {
                // Laissé tel quel : la règle "date" renverra l'erreur.
            }
        }

        $validated = $request->validate([
            'type'              => ['required', 'in:price,visit'],
            'price'             => ['nullable', 'required_if:type,price', 'numeric', 'min:1', 'max:1000000'],
            'intervention_date' => ['nullable', 'required_if:type,price', 'date', 'after_or_equal:today', 'before_or_equal:' . now()->addMonths(6)->toDateString()],
            'visit_at'          => ['nullable', 'required_if:type,visit', 'date', 'after:now', 'before_or_equal:' . now()->addDays(60)->toDateString()],
            'study_fee'         => ['nullable', 'required_if:type,visit', 'numeric', 'min:0', 'max:10000'],
            'message'           => ['nullable', 'string', 'max:500'],
        ], [
            'price.required_if'             => 'Indiquez votre prix.',
            'price.min'                     => 'Le prix doit être d\'au moins 1 $.',
            'intervention_date.required_if' => 'Indiquez la date d\'intervention.',
            'intervention_date.after_or_equal' => 'La date d\'intervention ne peut pas être dans le passé.',
            'visit_at.required_if'          => 'Indiquez la date et l\'heure de la visite.',
            'visit_at.after'                => 'La visite doit être prévue dans le futur.',
            'visit_at.before_or_equal'      => 'La visite doit avoir lieu dans les 60 prochains jours.',
            'study_fee.required_if'         => 'Indiquez les frais d\'étude (0 si la visite est gratuite).',
            'message.max'                   => 'Le message ne doit pas dépasser 500 caractères.',
        ]);

        $artisan = Auth::user();
        $isPrice = $validated['type'] === NeedProposal::TYPE_PRICE;

        DB::transaction(function () use ($serviceRequest, $artisan, $validated, $isPrice) {
            $dispatch = $this->lockAnswerableDispatch($serviceRequest, $artisan->id);

            NeedProposal::create([
                'service_request_id' => $serviceRequest->id,
                'artisan_id'         => $artisan->id,
                'need_dispatch_id'   => $dispatch->id,
                'type'               => $validated['type'],
                'status'             => NeedProposal::STATUS_PENDING,
                'price'              => $isPrice ? $validated['price'] : null,
                'intervention_date'  => $isPrice ? $validated['intervention_date'] : null,
                'visit_at'           => $isPrice ? null : $validated['visit_at'],
                'study_fee'          => $isPrice ? null : $validated['study_fee'],
                // Pas de numéro en clair tant que le client n'a pas retenu l'artisan.
                'message'            => ContactMasker::mask($validated['message'] ?? null),
            ]);

            $dispatch->update(['status' => NeedDispatch::STATUS_PROPOSED, 'responded_at' => now()]);

            Notification::create([
                'user_id'      => $serviceRequest->user_id,
                'type'         => 'need_proposal',
                'related_type' => 'request',
                'related_id'   => $serviceRequest->id,
                'title'        => $isPrice ? 'Nouvelle proposition' : 'Proposition de visite',
                'message'      => $isPrice
                    ? "{$artisan->name} propose {$this->money($validated['price'])} pour « {$serviceRequest->requested_service_name} »."
                    : "{$artisan->name} propose une visite pour établir un devis pour « {$serviceRequest->requested_service_name} ».",
                'action_url'   => route('user.service-requests.show', $serviceRequest),
                'is_read'      => false,
            ]);
        });

        // 5 propositions : plus aucune vague (cahier des charges §2.3).
        $dispatcher->stopIfEnoughProposals($serviceRequest);

        return redirect()->route('user.artisan.opportunities.show', $serviceRequest)
            ->with('success', $isPrice ? 'Votre proposition a été envoyée au client.' : 'Votre proposition de visite a été envoyée au client.');
    }

    public function decline(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $validated = $request->validate(['reason' => ['nullable', 'string', 'max:255']]);

        DB::transaction(function () use ($serviceRequest, $validated) {
            $dispatch = $this->lockAnswerableDispatch($serviceRequest, Auth::id());
            $dispatch->update([
                'status'         => NeedDispatch::STATUS_DECLINED,
                'responded_at'   => now(),
                'decline_reason' => $validated['reason'] ?? null,
            ]);
        });

        return redirect()->route('user.artisan.opportunities.index')
            ->with('info', 'Demande refusée. Merci d\'avoir répondu : votre réactivité est prise en compte.');
    }

    /** Devis après la visite : main d'œuvre, matériaux, délai, étapes de paiement. */
    public function sendQuote(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $validated = $request->validate([
            'quote_labor'             => ['required', 'numeric', 'min:0', 'max:1000000'],
            'quote_materials'         => ['required', 'numeric', 'min:0', 'max:1000000'],
            'quote_duration'          => ['required', 'string', 'max:100'],
            'quote_message'           => ['nullable', 'string', 'max:500'],
            'steps'                   => ['required', 'array', 'min:1', 'max:4'],
            'steps.*.label'           => ['required', 'string', 'max:60'],
            'steps.*.percent'         => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'quote_labor.required'     => 'Indiquez le coût de la main d\'œuvre.',
            'quote_materials.required' => 'Indiquez le coût des matériaux (0 si aucun).',
            'quote_duration.required'  => 'Indiquez le délai de réalisation.',
            'steps.required'           => 'Ajoutez au moins une étape de paiement.',
            'steps.max'                => 'Quatre étapes de paiement au maximum.',
            'steps.*.label.required'   => 'Chaque étape de paiement doit avoir un nom.',
            'steps.*.percent.required' => 'Chaque étape de paiement doit avoir un pourcentage.',
        ]);

        $steps = array_values(array_map(fn ($s) => ['label' => trim($s['label']), 'percent' => (int) $s['percent']], $validated['steps']));
        if (array_sum(array_column($steps, 'percent')) !== 100) {
            throw ValidationException::withMessages(['steps' => 'Les étapes de paiement doivent totaliser 100 %.']);
        }
        if ((float) $validated['quote_labor'] + (float) $validated['quote_materials'] <= 0) {
            throw ValidationException::withMessages(['quote_labor' => 'Le montant total du devis doit être supérieur à 0.']);
        }

        DB::transaction(function () use ($serviceRequest, $validated, $steps) {
            $proposal = NeedProposal::where('service_request_id', $serviceRequest->id)
                ->where('artisan_id', Auth::id())
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless($serviceRequest->status === ServiceRequest::STATUS_PENDING && $proposal->canSendQuote(), 403,
                'Le devis ne peut être envoyé qu\'une fois, après acceptation de la visite par le client.');

            $proposal->update([
                'quote_labor'         => $validated['quote_labor'],
                'quote_materials'     => $validated['quote_materials'],
                'quote_duration'      => $validated['quote_duration'],
                'quote_payment_steps' => $steps,
                'quote_message'       => ContactMasker::mask($validated['quote_message'] ?? null),
                'quote_sent_at'       => now(),
                'price'               => (float) $validated['quote_labor'] + (float) $validated['quote_materials'],
            ]);

            Notification::create([
                'user_id'      => $serviceRequest->user_id,
                'type'         => 'need_quote',
                'related_type' => 'request',
                'related_id'   => $serviceRequest->id,
                'title'        => 'Devis reçu',
                'message'      => Auth::user()->name . " vous a envoyé son devis : {$this->money($proposal->price)}.",
                'action_url'   => route('user.service-requests.show', $serviceRequest),
                'is_read'      => false,
            ]);
        });

        return redirect()->route('user.artisan.opportunities.show', $serviceRequest)
            ->with('success', 'Votre devis a été envoyé au client.');
    }

    /**
     * L'envoi de cet artisan, verrouillé : il doit lui avoir été adressé, attendre
     * encore sa réponse, et la demande doit être ouverte (double clic, deux onglets…).
     */
    private function lockAnswerableDispatch(ServiceRequest $need, int $artisanId): NeedDispatch
    {
        $dispatch = NeedDispatch::where('service_request_id', $need->id)
            ->where('artisan_id', $artisanId)
            ->lockForUpdate()
            ->first();

        abort_unless($dispatch, 404);

        if ($need->fresh()->status !== ServiceRequest::STATUS_PENDING || $dispatch->status === NeedDispatch::STATUS_CLOSED) {
            throw ValidationException::withMessages(['response' => 'Cette demande n\'est plus disponible.']);
        }
        if (!$dispatch->isAwaitingResponse()) {
            throw ValidationException::withMessages(['response' => 'Vous avez déjà répondu à cette demande.']);
        }
        if ($dispatch->deadline_at->isPast()) {
            throw ValidationException::withMessages(['response' => 'Le délai de réponse est dépassé : la demande a été transmise à d\'autres artisans.']);
        }

        return $dispatch;
    }

    private function money(float|string|null $amount): string
    {
        return '$' . number_format((float) $amount, 0, ',', ' ');
    }
}
