<?php

namespace App\Support;

/**
 * Maps a CRM portal request (and its appointment) to one customer-facing state key.
 * Labels live in the translation files as site.portal_state_{key}.
 */
final class PortalState
{
    /**
     * @param  array<string, mixed>  $request
     */
    public static function forRequest(array $request): string
    {
        $appointment = $request['appointment'] ?? null;

        if (is_array($appointment) && isset($appointment['status'])) {
            return match ($appointment['status']) {
                'held' => 'awaiting',
                'confirmed' => 'scheduled',
                'proposed' => 'proposed',
                'rejected' => 'rejected',
                'expired' => 'expired',
                'cancelled' => 'cancelled',
                'completed' => 'completed',
                'no_show' => 'no_show',
                default => 'received',
            };
        }

        return match ($request['status'] ?? 'pending') {
            'scheduled' => 'scheduled',
            'handled' => 'handled',
            'rejected' => 'rejected',
            'cancelled' => 'cancelled',
            default => 'received',
        };
    }

    public static function tone(string $state): string
    {
        return match ($state) {
            'scheduled', 'completed', 'handled', 'active' => 'success',
            'awaiting', 'proposed', 'received', 'upcoming' => 'warning',
            'rejected', 'expired', 'no_show', 'revoked' => 'danger',
            default => 'neutral',
        };
    }
}
