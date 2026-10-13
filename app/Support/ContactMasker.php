<?php

namespace App\Support;

use App\Models\ServiceRequest;
use App\Models\User;

/**
 * Masque les numéros de téléphone échangés entre un client et un artisan tant
 * qu'aucune proposition n'est acceptée entre eux : les échanges passent par la
 * messagerie ProConnect (cahier des charges §2.5, "numéros masqués").
 * Le masquage se fait à l'enregistrement : le numéro n'est jamais stocké.
 */
class ContactMasker
{
    public const PLACEHOLDER = '[numéro masqué]';

    /**
     * Au moins 9 chiffres, éventuellement séparés par des espaces, points, tirets ou
     * parenthèses, avec un préfixe + ou 00 facultatif : +243 81 234 5678, 0812345678,
     * 081-234-5678, 00243812345678, wa.me/243812345678… Les dates (8 chiffres) et les
     * montants usuels ne sont pas touchés.
     */
    private const PHONE_PATTERN = '/(?<!\d)(?:\+|00)?\(?\d(?:[\s.\-()]*\d){8,}(?!\d)/u';

    /** Statuts où le client a retenu l'artisan : les coordonnées peuvent alors circuler. */
    private const CONFIRMED_STATUSES = ['accepted', 'in_progress', 'awaiting_validation', 'completed'];

    public static function mask(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return $text;
        }

        return preg_replace(self::PHONE_PATTERN, self::PLACEHOLDER, $text) ?? $text;
    }

    public static function containsPhone(?string $text): bool
    {
        return $text !== null && preg_match(self::PHONE_PATTERN, $text) === 1;
    }

    /** Masque si les deux personnes sont un client et un artisan sans mission acceptée entre eux. */
    public static function maskBetween(?string $text, User $sender, User $recipient): ?string
    {
        return self::shouldMaskBetween($sender, $recipient) ? self::mask($text) : $text;
    }

    public static function shouldMaskBetween(User $a, User $b): bool
    {
        $pair = [$a->user_type, $b->user_type];
        sort($pair);
        if ($pair !== [User::TYPE_ARTISAN, User::TYPE_CLIENT]) {
            return false; // recrutement, administration… : non concernés
        }

        [$client, $artisan] = $a->user_type === User::TYPE_CLIENT ? [$a, $b] : [$b, $a];

        return !ServiceRequest::where('user_id', $client->id)
            ->where('artisan_id', $artisan->id)
            ->whereIn('status', self::CONFIRMED_STATUSES)
            ->exists();
    }
}
