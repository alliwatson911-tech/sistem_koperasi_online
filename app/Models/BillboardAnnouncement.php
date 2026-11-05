<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillboardAnnouncement extends Model
{
    use HasFactory;

    protected $fillable = [
        'tab_title',
        'badge_text',
        'badge_color',
        'meta_text',
        'title',
        'content',
        'status_text',
        'link_url',
        'link_text',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function getBadgeClassAttribute(): string
    {
        return match ($this->badge_color) {
            'warning' => 'badge-warning',
            'success' => 'badge-success',
            'info' => 'badge-info',
            'danger' => 'badge-danger',
            'primary' => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
            default => 'badge-gray',
        };
    }
}

