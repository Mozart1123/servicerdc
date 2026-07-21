<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-subscription-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks expiring subscriptions and sends reminders to users.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Processing subscription reminders...");

        $now = Carbon::now();
        $today = $now->copy()->startOfDay();

        // 1. Process active subscriptions that are about to expire or expire today
        $activeSubscriptions = Subscription::where('status', 'active')
            ->whereNotNull('ends_at')
            ->get();

        foreach ($activeSubscriptions as $subscription) {
            $endsAt = Carbon::parse($subscription->ends_at)->startOfDay();
            
            if ($endsAt->lt($today)) {
                $subscription->status = 'expired';
                $subscription->save();
                
                // If it's a recruiter premium subscription expiring, remove 'is_urgent' from all active job offers
                if ($subscription->plan && $subscription->plan->slug === 'recruiter-premium') {
                    \App\Models\JobOffer::where('employer_id', $subscription->user_id)
                                        ->where('is_urgent', true)
                                        ->update(['is_urgent' => false]);
                }

                $this->notifyUser($subscription, 'Abonnement expiré', 'Votre abonnement a expiré. Renouvelez-le pour continuer à profiter de vos avantages Premium.');
                Log::info("Subscription {$subscription->id} expired.");
                continue;
            }

            // Calculate days left
            $daysLeft = $today->diffInDays($endsAt, false); // false for negative if past

            // Notify on exactly 7, 3, 1, and 0 days
            if (in_array($daysLeft, [7, 3, 1])) {
                $this->notifyUser($subscription, "Votre abonnement expire dans {$daysLeft} jour(s)", "N'oubliez pas de renouveler votre abonnement avant son expiration.");
            } elseif ($daysLeft == 0) {
                $this->notifyUser($subscription, 'Votre abonnement expire aujourd\'hui', 'Veuillez renouveler votre abonnement dès aujourd\'hui pour ne pas perdre vos avantages.');
            }
        }

        $this->info("Subscription reminders processed successfully.");
    }

    protected function notifyUser(Subscription $subscription, string $title, string $message)
    {
        // To avoid spamming, we could check if a notification was already sent today for this type.
        // For simplicity in this implementation, we just create it.
        // In a real production system, you'd track the last reminder sent in the subscription table or use caching.
        
        $alreadySentToday = Notification::where('user_id', $subscription->user_id)
            ->where('title', $title)
            ->whereDate('created_at', Carbon::today())
            ->exists();

        if ($alreadySentToday) {
            return;
        }

        Notification::create([
            'user_id' => $subscription->user_id,
            'type' => 'subscription_reminder',
            'related_type' => 'subscription',
            'related_id' => $subscription->id,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
            'action_url' => '/user/subscription',
        ]);
        
        // Note: You would also dispatch an Email/Mailable here if configured.
    }
}
