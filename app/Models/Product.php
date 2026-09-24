<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'image', 'title', 'summary', 'description', 'tech_stack', 'sort_order'];

    protected $casts = [
        'title' => 'array',
        'summary' => 'array',
        'description' => 'array',
        'tech_stack' => 'array', // e.g. ['Laravel', 'PostgreSQL', 'Firebase']
    ];

    public function getLocalized(string $field): string
    {
        $value = $this->{$field} ?? [];

        return $value[app()->getLocale()] ?? $value['en'] ?? '';
    }

    /**
     * Get the localized tech stack for the given or current locale with English fallback.
     *
     * @return array<int, string>
     */
    public function getLocalizedTechStack(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $raw = $this->tech_stack;

        if (! is_array($raw)) {
            return [];
        }

        // Multilingual structure: ['en' => [...], 'fr' => [...], 'ar' => [...]]
        if (isset($raw[$locale]) && is_array($raw[$locale]) && ! empty($raw[$locale])) {
            return array_values($raw[$locale]);
        }

        if (isset($raw['en']) && is_array($raw['en']) && ! empty($raw['en'])) {
            return array_values($raw['en']);
        }

        // Legacy flat list fallback: ['Purchasing', 'Sales', ...]
        if (array_is_list($raw)) {
            return $raw;
        }

        return [];
    }

    /**
     * Accessor for localized tech stack.
     *
     * @return array<int, string>
     */
    public function getLocalizedTechStackAttribute(): array
    {
        return $this->getLocalizedTechStack();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
