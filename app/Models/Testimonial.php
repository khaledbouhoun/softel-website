<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = ['author_name', 'author_role', 'avatar', 'quote', 'sort_order'];

    protected $casts = [
        'quote' => 'array',
    ];

    public function getLocalized(string $field): string
    {
        $value = $this->{$field} ?? [];

        return $value[app()->getLocale()] ?? $value['en'] ?? '';
    }
}
