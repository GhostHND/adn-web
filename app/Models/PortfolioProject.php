<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfolioProject extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const STATUS_DRAFT =
        'draft';

    public const STATUS_PUBLISHED =
        'published';

    protected $fillable = [
        'code',
        'slug',
        'title',
        'client_name',
        'excerpt',
        'description',
        'category_id',
        'service_id',
        'catalog_product_id',
        'main_media_id',
        'project_date',
        'status',
        'featured',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'project_date' =>
                'date',

            'featured' =>
                'boolean',

            'sort_order' =>
                'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            PortfolioCategory::class,
            'category_id'
        );
    }

    public function mainMedia(): BelongsTo
    {
        return $this->belongsTo(
            Media::class,
            'main_media_id'
        );
    }

    public function catalogProduct(): BelongsTo
    {
        return $this->belongsTo(
            CatalogProduct::class,
            'catalog_product_id'
        );
    }

    public function images(): HasMany
    {
        return $this
            ->hasMany(
                PortfolioImage::class,
                'portfolio_project_id'
            )
            ->orderBy(
                'sort_order'
            )
            ->orderBy(
                'id'
            );
    }

    public function scopePublished(
        Builder $query
    ): Builder {
        return $query
            ->where(
                'status',
                self::STATUS_PUBLISHED
            );
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}