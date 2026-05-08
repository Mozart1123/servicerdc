<?php

namespace App\Services;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class SystemActivityService
{
    /**
     * Log a system activity.
     *
     * @param string $type AUTH, JOB, SERV, PAY, SEC
     * @param string $message
     * @param string $level info, warning, error, critical
     * @param array $context
     * @return SystemLog
     */
    public static function log(string $type, string $message, string $level = 'info', array $context = []): SystemLog
    {
        $log = SystemLog::create([
            'type' => strtoupper($type),
            'level' => strtolower($level),
            'message' => $message,
            'context' => $context,
            'user_id' => Auth::id(),
            'ip_address' => Request::ip(),
        ]);

        self::checkAlertThresholds($log);

        return $log;
    }

    protected static function checkAlertThresholds(SystemLog $log): void
    {
        // 1. Immediate Alert for Critical errors
        if ($log->level === 'critical' || ($log->level === 'error' && $log->type === 'SEC')) {
            \App\Models\SystemAlert::create([
                'code' => 'SEC-' . strtoupper(uniqid()),
                'title' => 'Incident de Sécurité Critique',
                'description' => $log->message,
                'level' => 'critical',
                'data' => $log->toArray(),
            ]);
            return;
        }

        // 2. Brute Force Detection (Simple pattern)
        if ($log->type === 'AUTH' && $log->level === 'error') {
            $recentFailures = SystemLog::where('type', 'AUTH')
                ->where('level', 'error')
                ->where('ip_address', $log->ip_address)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->count();

            if ($recentFailures >= 5) {
                // Trigger Brute Force Alert if not already active for this IP
                $exists = \App\Models\SystemAlert::where('code', 'SEC-BRUTE-' . $log->ip_address)
                    ->where('is_resolved', false)
                    ->exists();

                if (!$exists) {
                    \App\Models\SystemAlert::create([
                        'code' => 'SEC-BRUTE-' . $log->ip_address,
                        'title' => 'Tentative de Brute Force Détectée',
                        'description' => "5+ échecs de connexion détectés pour l'IP {$log->ip_address} au cours des 5 dernières minutes.",
                        'level' => 'critical',
                        'data' => ['ip' => $log->ip_address, 'count' => $recentFailures],
                    ]);
                }
            }
        }
    }
}
