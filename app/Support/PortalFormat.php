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

    /** Avatars come from Google and are rendered in the browser: https only. */
    public static function avatarUrl(mixed $url): ?string
    {
        return is_string($url) && preg_match('#^https://#i', $url) === 1 ? $url : null;
    }

    public static function initials(?string $name): string
    {
        $parts = preg_split('/\s+/u', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $letters = array_map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)), array_slice($parts, 0, 2));

        return implode('', $letters) ?: '?';
    }

    public static function fileSize(mixed $bytes): string
    {
        $bytes = (int) $bytes;

        return match (true) {
            $bytes >= 1048576 => number_format($bytes / 1048576, 1).' MB',
            $bytes >= 1024 => number_format($bytes / 1024, 0).' KB',
            default => $bytes.' B',
        };
    }

    public static function range(?string $start, ?string $end): string
    {
        if (! $start) {
            return '';
        }

        return self::dateTime($start).($end ? ' - '.self::time($end) : '');
    }
}
