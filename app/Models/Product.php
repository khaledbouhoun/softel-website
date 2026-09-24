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

    public function getLocalized(string $field, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $value = $this->{$field} ?? [];

        return $value[$locale] ?? $value['en'] ?? '';
    }

    /**
     * Generate the localized pre-filled WhatsApp contact message.
     */
    public function getWhatsAppMessage(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $title = $this->getLocalized('title', $locale);

        $template = __('site.products_whatsapp_message', ['product' => $title], $locale);

        if ($template === 'site.products_whatsapp_message' || empty($template)) {
            $template = __('site.products_whatsapp_message', ['product' => $title], 'en');
        }

        return $template;
    }

    /**
     * Generate the direct WhatsApp contact URL with encoded message.
     */
    public function getWhatsAppUrl(?string $locale = null): string
    {
        $phone = '213558925355';
        $message = $this->getWhatsAppMessage($locale);

        return 'https://wa.me/'.$phone.'?text='.urlencode($message);
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
