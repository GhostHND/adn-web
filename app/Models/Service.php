<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'slug',
        'name',
        'short_description',
        'description',
        'icon',
        'main_media_id',
        'status',
        'featured',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function mainMedia(): BelongsTo
    {
        return $this->belongsTo(
            Media::class,
            'main_media_id'
        );
    }

    public function features(): HasMany
    {
        return $this
            ->hasMany(
                ServiceFeature::class
            )
            ->orderBy('sort_order');
    }

    public function images(): HasMany
    {
        return $this
            ->hasMany(
                ServiceImage::class
            )
            ->orderBy('sort_order');
    }

    public function portfolioProjects(): HasMany
    {
        return $this->hasMany(
            PortfolioProject::class
        );
    }
}