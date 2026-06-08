<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    // ─── Service Request Notifications ─────────────────────────────────────────

    /**
     * Notify artisan that a client viewed their service.
     */
    public static function serviceViewed(int $artisanId, string $clientName, string $serviceTitle, int $serviceId): void
    {
        self::create(
            userId:    $artisanId,
            title:     'Service consulté',
            message:   "Le client {$clientName} a consulté votre service \"{$serviceTitle}\".",
            type:      'service_viewed',
            relatedId: $serviceId,
        );
    }

    /**
     * Notify artisan that a client sent them a service request.
     */
    public static function serviceRequested(
        int    $artisanId,
        string $clientName,
        string $clientContact,
        string $serviceTitle,
        int    $requestId
    ): void {
        self::create(
            userId:    $artisanId,
            title:     'Nouvelle demande de service',
            message:   "{$clientName} ({$clientContact}) a envoyé une demande pour votre service \"{$serviceTitle}\".",
            type:      'service_request',
            relatedId: $requestId,
        );
    }

    /**
     * Notify client that their service request was accepted.
     */
    public static function serviceRequestAccepted(int $clientId, string $serviceTitle, int $requestId): void
    {
        self::create(
            userId:    $clientId,
            title:     'Demande acceptée',
            message:   "Votre demande pour le service \"{$serviceTitle}\" a été acceptée par l'artisan.",
            type:      'service_request_accepted',
            relatedId: $requestId,
        );
    }

    /**
     * Notify client that their service request was rejected.
     */
    public static function serviceRequestRejected(int $clientId, string $serviceTitle, int $requestId): void
    {
        self::create(
            userId:    $clientId,
            title:     'Demande rejetée',
            message:   "Votre demande pour le service \"{$serviceTitle}\" a été rejetée par l'artisan.",
            type:      'service_request_rejected',
            relatedId: $requestId,
        );
    }

    // ─── Job Application Notifications ────────────────────────────────────────

    /**
     * Notify recruiter that a client applied to their job.
     */
    public static function jobApplicationReceived(
        int    $recruiterId,
        string $applicantName,
        string $jobTitle,
        int    $applicationId
    ): void {
        self::create(
            userId:    $recruiterId,
            title:     'Nouvelle candidature reçue',
            message:   "{$applicantName} a postulé pour votre offre d'emploi \"{$jobTitle}\".",
            type:      'job_application',
            relatedId: $applicationId,
        );
    }

    /**
     * Notify client that their job application was approved.
     */
    public static function jobApplicationApproved(int $clientId, string $jobTitle, int $applicationId): void
    {
        self::create(
            userId:    $clientId,
            title:     'Candidature approuvée ✅',
            message:   "Félicitations ! Votre candidature pour \"{$jobTitle}\" a été approuvée par le recruteur.",
            type:      'job_application_approved',
            relatedId: $applicationId,
        );
    }

    /**
     * Notify client that their job application was rejected.
     */
    public static function jobApplicationRejected(int $clientId, string $jobTitle, int $applicationId): void
    {
        self::create(
            userId:    $clientId,
            title:     'Candidature rejetée',
            message:   "Votre candidature pour \"{$jobTitle}\" n'a pas été retenue cette fois.",
            type:      'job_application_rejected',
            relatedId: $applicationId,
        );
    }

    // ─── Internal ─────────────────────────────────────────────────────────────

    private static function create(
        int    $userId,
        string $title,
        string $message,
        string $type,
        int    $relatedId = 0,
    ): void {
        Notification::create([
            'user_id'    => $userId,
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'related_id' => $relatedId,
            'is_read'    => false,
        ]);
    }
}
