<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'position',
    'short_description',
    'specialties',
    'education',
    'experience',
    'activities',
    'bio',
    'photo_path',
    'email',
    'phone',
    'linkedin_url',
    'is_partner',
    'sort_order',
    'is_active',
])]
class TeamMember extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'specialties' => 'array',
        'education' => 'array',
        'experience' => 'array',
        'activities' => 'array',
        'is_partner' => 'boolean',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
