<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait AuditLogger
{
    /**
     * Log a system activity.
     *
     * @param string $action The key action (created, updated, deleted, etc)
     * @param string|null $description A human-readable description
     * @param Model|null $model The associated model
     * @param array $payload Additional metadata or diffs
     * @param string $severity info|warning|danger
     */
    protected function auditLog(
        string $action,
        ?string $description = null,
        ?Model $model = null,
        array $payload = [],
        string $severity = 'info'
    ): void {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $description ?? $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->getKey() : null,
            'payload' => $payload,
            'severity' => $severity,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'session_id' => session()->getId(),
        ]);
    }
}
