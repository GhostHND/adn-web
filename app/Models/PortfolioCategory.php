<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfolioCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' =>
                'integer',

            'active' =>
                'boolean',
        ];
    }

    public function projects(): HasMany
    {
        return $this
            ->hasMany(
                PortfolioProject::class,
                'category_id'
            )
            ->orderBy(
                'sort_order'
            )
            ->orderByDesc(
                'project_date'
            );
    }

    public function scopeActive(
        Builder $query
    ): Builder {
        return $query
            ->where(
                'active',
                true
            );
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}