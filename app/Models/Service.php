<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'icon', 'title', 'summary', 'description', 'sort_order'];

    protected $casts = [
        'title' => 'array',       // ['en' => '...', 'fr' => '...', 'ar' => '...']
        'summary' => 'array',
        'description' => 'array',
    ];

    public function getLocalized(string $field): string
    {
        $value = $this->{$field} ?? [];

        return $value[app()->getLocale()] ?? $value['en'] ?? '';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
