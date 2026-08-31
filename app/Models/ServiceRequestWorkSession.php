<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequestWorkSession extends Model
{
    protected $fillable = [
        'service_request_id',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Duration of this session in seconds. If still active (no ended_at),
     * counts up to now() — so an in-progress session's duration keeps
     * growing every time this is called, which is exactly what powers the
     * live-ticking display.
     */
    public function durationInSeconds(): int
    {
        $end = $this->ended_at ?? now();
        return (int) max(0, $this->started_at->diffInSeconds($end));
    }
}
