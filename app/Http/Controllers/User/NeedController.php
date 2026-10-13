<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Commune;
use App\Models\Notification;
use App\Models\ServiceRequest;
use App\Models\ServiceType;
use App\Services\Matching\NeedDispatcher;
use App\Services\Matching\NeedLifecycle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * "Décrire mon besoin" : le client publie une demande sans artisan désigné,
 * qui sera ensuite envoyée par le matching aux artisans pertinents.
 */
class NeedController extends Controller
{
    /** Demandes publiées encore ouvertes autorisées en même temps (anti-spam). */
    public const MAX_OPEN_NEEDS = 5;

    public function create(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->denyIfNotClient()) {
            return $redirect;
        }

        $user = Auth::user();
        $categories = Category::orderBy('name')->get(['id', 'name', 'icon', 'image']);
        $serviceTypes = ServiceType::orderBy('title')->get(['id', 'category_id', 'title'])->groupBy('category_id')
            ->map(fn ($types) => $types->map->only('id', 'title')->values());

        return view('user.needs.create', [
            'categories'        => $categories,
            'serviceTypes'      => $serviceTypes,
            'communes'          => Commune::active()->get(['id', 'name']),
            'preselectCategory' => $categories->firstWhere('id', (int) $request->query('category'))?->id,
            'defaultCommuneId'  => $user->commune_id,
            'defaultQuartier'   => $user->quartier,
            'maxPhotos'         => ServiceRequest::MAX_PHOTOS,
        ]);
    }

    public function store(Request $request, NeedDispatcher $dispatcher): RedirectResponse
    {
        if ($redirect = $this->denyIfNotClient()) {
            return $redirect;
        }

        $user = Auth::user();

        $openNeeds = ServiceRequest::where('user_id', $user->id)
            ->where('mode', ServiceRequest::MODE_OPEN)
            ->where('status', ServiceRequest::STATUS_PENDING)
            ->count();
        if ($openNeeds >= self::MAX_OPEN_NEEDS) {
            return back()->withInput()->with('error', 'Vous avez déjà ' . self::MAX_OPEN_NEEDS . ' demandes publiées en cours. Clôturez-en une avant d\'en publier une nouvelle.');
        }

        $validated = $request->validate(array_merge([
            'category_id'     => ['required', 'integer', Rule::exists('categories', 'id')],
            'service_type_id' => ['nullable', 'integer', Rule::exists('service_types', 'id')->where('category_id', $request->integer('category_id'))],
            'photos'          => ['nullable', 'array', 'max:' . ServiceRequest::MAX_PHOTOS],
            'photos.*'        => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'commune_id'      => ['required', 'integer', Rule::exists('communes', 'id')->where('is_active', true)],
        ], self::editableRules()), self::MESSAGES);

        $category = Category::findOrFail($validated['category_id']);
        $serviceType = isset($validated['service_type_id']) ? ServiceType::find($validated['service_type_id']) : null;
        $commune = Commune::findOrFail($validated['commune_id']);
        $precision = trim((string) ($validated['precision'] ?? ''));

        [$budgetMin, $budgetMax] = self::budgetBounds($validated);

        $storedPhotos = [];
        try {
            $need = DB::transaction(function () use ($request, $user, $validated, $category, $serviceType, $commune, $precision, $budgetMin, $budgetMax, &$storedPhotos) {
                $need = ServiceRequest::create([
                    'user_id'                => $user->id,
                    'artisan_id'             => null,
                    'service_id'             => null,
                    'mode'                   => ServiceRequest::MODE_OPEN,
                    'status'                 => ServiceRequest::STATUS_PENDING,
                    'category_id'            => $category->id,
                    'service_type_id'        => $serviceType?->id,
                    'category_needed'        => $category->name,
                    'requested_service_name' => $precision !== '' ? $precision : ($serviceType?->title ?? $category->name),
                    'description'            => $validated['description'],
                    'city'                   => $commune->city,
                    'commune_id'             => $commune->id,
                    'quartier'               => $validated['quartier'] ?? null,
                    'location'               => implode(', ', array_filter([$validated['quartier'] ?? null, $commune->name])),
                    'timing'                 => $validated['timing'],
                    'preferred_date'         => $validated['timing'] === 'date' ? $validated['preferred_date'] : null,
                    'urgency'                => self::urgencyFor($validated['timing']),
                    'budget_type'            => $validated['budget_type'],
                    'budget_min'             => $budgetMin,
                    'budget_max'             => $budgetMax,
                    'phone'                  => $user->phone,
                    'email'                  => $user->email,
                    'expires_at'             => now()->addDays(ServiceRequest::OPEN_DAYS),
                ]);

                foreach ($request->file('photos', []) as $photo) {
                    // Nom aléatoire généré par Laravel : jamais le nom fourni par le client.
                    $storedPhotos[] = $photo->store('service-requests/' . $need->id, 'public');
                }
                if ($storedPhotos) {
                    $need->update(['photos' => $storedPhotos]);
                }

                return $need;
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($storedPhotos);
            \Log::error('Publication besoin client échouée: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la publication. Veuillez réessayer.');
        }

        // Pré-remplit le profil pour les prochaines demandes, sans écraser un choix existant.
        if (!$user->commune_id) {
            $user->forceFill(['commune_id' => $commune->id, 'quartier' => $user->quartier ?: ($validated['quartier'] ?? null)])->save();
        }

        // Première vague d'artisans. En cas d'échec, la demande reste publiée :
        // la tâche planifiée (proconnect:advance-need-waves) la reprendra.
        try {
            $dispatcher->start($need);
        } catch (\Throwable $e) {
            \Log::error("Matching : premier envoi de la demande #{$need->id} échoué : " . $e->getMessage());
        }

        return redirect()->route('user.service-requests.show', $need)
            ->with('success', 'Votre demande est publiée ! Nous la transmettons aux artisans les plus adaptés près de chez vous.');
    }

    /** Suivi en direct de la demande publiée (rafraîchi par la page de détail). */
    public function status(ServiceRequest $serviceRequest): JsonResponse
    {
        abort_unless($serviceRequest->user_id === Auth::id() && $serviceRequest->isOpenNeed(), 403);

        return response()->json(self::progress($serviceRequest));
    }

    /** @return array{contacted:int, proposals:int, widened:bool, searching:bool, exhausted:bool, status:string} */
    public static function progress(ServiceRequest $need): array
    {
        return [
            'contacted' => $need->dispatches()->count(),
            'proposals' => $need->proposals()->whereIn('status', \App\Models\NeedProposal::ACTIVE_STATUSES)->count(),
            'widened'   => $need->matching_widened_at !== null,
            'searching' => $need->status === ServiceRequest::STATUS_PENDING && $need->wave_deadline_at !== null,
            'exhausted' => $need->matching_exhausted_at !== null,
            'status'    => $need->status,
        ];
    }

    /** Modification possible tant qu'aucune proposition n'est en lice (§2.6). */
    public function edit(ServiceRequest $serviceRequest, NeedLifecycle $lifecycle): View|RedirectResponse
    {
        $this->authorizeOwner($serviceRequest);
        if (!$lifecycle->canEdit($serviceRequest)) {
            return redirect()->route('user.service-requests.show', $serviceRequest)
                ->with('error', 'Cette demande ne peut plus être modifiée : des artisans vous ont déjà fait une proposition.');
        }

        $serviceRequest->load(['category:id,name', 'serviceType:id,title', 'commune:id,name']);

        return view('user.needs.edit', ['need' => $serviceRequest]);
    }

    public function update(Request $request, ServiceRequest $serviceRequest, NeedLifecycle $lifecycle): RedirectResponse
    {
        $this->authorizeOwner($serviceRequest);
        $validated = $request->validate(self::editableRules(), self::MESSAGES);

        $wasStale = false;
        DB::transaction(function () use ($serviceRequest, $validated, $lifecycle, &$wasStale) {
            $need = ServiceRequest::whereKey($serviceRequest->id)->lockForUpdate()->firstOrFail();
            // Revérifié sous verrou : une proposition a pu arriver pendant la saisie.
            if (!$lifecycle->canEdit($need)) {
                throw ValidationException::withMessages(['need' => 'Un artisan vient de vous faire une proposition : la demande ne peut plus être modifiée.']);
            }
            $wasStale = $lifecycle->isStale($need);

            $need->loadMissing(['category', 'serviceType', 'commune']);
            $precision = trim((string) ($validated['precision'] ?? ''));
            [$budgetMin, $budgetMax] = self::budgetBounds($validated);

            $need->update([
                'requested_service_name' => $precision !== '' ? $precision : ($need->serviceType?->title ?? $need->category?->name ?? $need->requested_service_name),
                'description'            => $validated['description'],
                'quartier'               => $validated['quartier'] ?? null,
                'location'               => implode(', ', array_filter([$validated['quartier'] ?? null, $need->commune?->name])),
                'timing'                 => $validated['timing'],
                'preferred_date'         => $validated['timing'] === 'date' ? $validated['preferred_date'] : null,
                'urgency'                => self::urgencyFor($validated['timing']),
                'budget_type'            => $validated['budget_type'],
                'budget_min'             => $budgetMin,
                'budget_max'             => $budgetMax,
            ]);

            // Les artisans qui doivent encore répondre voient la demande à jour.
            foreach ($need->dispatches()->awaitingResponse()->pluck('artisan_id') as $artisanId) {
                Notification::create([
                    'user_id'      => $artisanId,
                    'type'         => 'need_updated',
                    'related_type' => 'request',
                    'related_id'   => $need->id,
                    'title'        => 'Demande modifiée',
                    'message'      => 'Le client a modifié sa demande « ' . $need->requested_service_name . ' ». Consultez-la avant de répondre.',
                    'action_url'   => route('user.artisan.opportunities.show', $need),
                    'is_read'      => false,
                ]);
            }
        });

        // Demande sans suite : la nouvelle version part aussitôt vers d'autres artisans.
        if ($wasStale) {
            $lifecycle->relaunch($serviceRequest);
        }

        return redirect()->route('user.service-requests.show', $serviceRequest)
            ->with('success', $wasStale ? 'Demande modifiée et relancée auprès d\'autres artisans.' : 'Demande modifiée.');
    }

    public function relaunch(ServiceRequest $serviceRequest, NeedLifecycle $lifecycle): RedirectResponse
    {
        $this->authorizeOwner($serviceRequest);
        if (!$lifecycle->canRelaunch($serviceRequest)) {
            return back()->with('error', 'Cette demande ne peut pas être relancée pour le moment.');
        }
        if ($serviceRequest->status === ServiceRequest::STATUS_EXPIRED) {
            $openNeeds = ServiceRequest::where('user_id', $serviceRequest->user_id)->where('mode', ServiceRequest::MODE_OPEN)
                ->where('status', ServiceRequest::STATUS_PENDING)->count();
            if ($openNeeds >= self::MAX_OPEN_NEEDS) {
                return back()->with('error', 'Vous avez déjà ' . self::MAX_OPEN_NEEDS . ' demandes publiées en cours. Clôturez-en une avant d\'en relancer une autre.');
            }
        }

        $lifecycle->relaunch($serviceRequest);

        return redirect()->route('user.service-requests.show', $serviceRequest)
            ->with('success', 'Demande relancée pour ' . ServiceRequest::OPEN_DAYS . ' jours : nous contactons d\'autres artisans.');
    }

    private function authorizeOwner(ServiceRequest $need): void
    {
        abort_unless($need->user_id === Auth::id() && $need->isOpenNeed(), 403);
    }

    /** Règles des champs modifiables après publication (partagées avec la modification). */
    private static function editableRules(): array
    {
        return [
            'precision'      => ['nullable', 'string', 'max:120'],
            'description'    => ['required', 'string', 'min:15', 'max:2000'],
            'quartier'       => ['nullable', 'string', 'max:120'],
            'timing'         => ['required', Rule::in(array_keys(ServiceRequest::TIMINGS))],
            'preferred_date' => ['nullable', 'required_if:timing,date', 'date', 'after_or_equal:today', 'before_or_equal:' . now()->addMonths(6)->toDateString()],
            'budget_type'    => ['required', Rule::in(['none', 'amount', 'range'])],
            'budget_amount'  => ['nullable', 'required_if:budget_type,amount', 'numeric', 'min:1', 'max:1000000'],
            'budget_min'     => ['nullable', 'required_if:budget_type,range', 'numeric', 'min:0', 'max:1000000'],
            'budget_max'     => ['nullable', 'required_if:budget_type,range', 'numeric', 'max:1000000', 'gt:budget_min'],
        ];
    }

    private const MESSAGES = [
        'category_id.required'           => 'Choisissez le type de service dont vous avez besoin.',
        'description.required'           => 'Décrivez votre besoin.',
        'description.min'                => 'Décrivez votre besoin en au moins 15 caractères.',
        'photos.max'                     => 'Vous pouvez joindre au maximum ' . ServiceRequest::MAX_PHOTOS . ' photos.',
        'photos.*.image'                 => 'Chaque fichier doit être une image (JPG, PNG ou WEBP).',
        'photos.*.mimes'                 => 'Chaque fichier doit être une image (JPG, PNG ou WEBP).',
        'photos.*.max'                   => 'Chaque photo doit faire moins de 5 Mo.',
        'commune_id.required'            => 'Indiquez la commune où le service doit être réalisé.',
        'preferred_date.required_if'     => 'Choisissez la date souhaitée.',
        'preferred_date.after_or_equal'  => 'La date ne peut pas être dans le passé.',
        'preferred_date.before_or_equal' => 'La date doit être dans les 6 prochains mois.',
        'budget_amount.required_if'      => 'Indiquez votre budget.',
        'budget_min.required_if'         => 'Indiquez le budget minimum.',
        'budget_max.required_if'         => 'Indiquez le budget maximum.',
        'budget_max.gt'                  => 'Le budget maximum doit être supérieur au minimum.',
    ];

    /** @return array{0: mixed, 1: mixed} budget_min, budget_max */
    private static function budgetBounds(array $validated): array
    {
        return match ($validated['budget_type']) {
            'amount' => [$validated['budget_amount'], $validated['budget_amount']],
            'range'  => [$validated['budget_min'], $validated['budget_max']],
            default  => [null, null],
        };
    }

    private static function urgencyFor(string $timing): string
    {
        return match ($timing) { 'urgent' => 'urgent', 'this_week' => 'high', default => 'medium' };
    }

    /** Seuls les comptes client publient un besoin. */
    private function denyIfNotClient(): ?RedirectResponse
    {
        if (Auth::user()->isClient()) {
            return null;
        }

        return redirect()->route('user.dashboard')
            ->with('error', 'La publication d\'un besoin est réservée aux comptes client.');
    }
}
