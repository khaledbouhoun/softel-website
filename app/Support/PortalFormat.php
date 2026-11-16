<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Display helpers for CRM ISO 8601 values. The CRM sends Africa/Algiers offsets; they are shown as received.
 */
final class PortalFormat
{
    public static function dateTime(?string $iso): string
    {
        return $iso ? Carbon::parse($iso)->locale(app()->getLocale())->translatedFormat('D j M Y, H:i') : '';
    }

    public static function date(?string $iso): string
    {
        return $iso ? Carbon::parse($iso)->locale(app()->getLocale())->translatedFormat('D j M Y') : '';
    }

    public static function time(?string $iso): string
    {
        return $iso ? Carbon::parse($iso)->format('H:i') : '';
    }

    /**
     * Only plain http(s) links coming from the CRM are rendered as hrefs.
     */
    public static function safeUrl(mixed $url): ?string
    {
        return is_string($url) && preg_match('#^https?://#i', $url) === 1 ? $url : null;
    }

    public static function range(?string $start, ?string $end): string
    {
        if (! $start) {
            return '';
        }

        return self::dateTime($start).($end ? ' - '.self::time($end) : '');
    }
}
