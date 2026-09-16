<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogProduct extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'app_catalog_product_id',
        'code',
        'app_reference_code',
        'category_id',
        'slug',
        'name',
        'short_description',
        'description',
        'features',
        'main_media_id',
        'price_visible',
        'price_from',
        'reference_price',
        'price_note',
        'quote_mode',
        'measurement_unit',
        'status',
        'featured',
        'show_on_home',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'app_catalog_product_id' =>
                'integer',

            'features' =>
                'array',

            'price_visible' =>
                'boolean',

            'price_from' =>
                'boolean',

            'reference_price' =>
                'decimal:2',

            'featured' =>
                'boolean',

            'show_on_home' =>
                'boolean',

            'sort_order' =>
                'integer',
        ];
    }

    public function appProduct(): BelongsTo
    {
        return $this->belongsTo(
            AppCatalogProduct::class,
            'app_catalog_product_id'
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            CatalogCategory::class,
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

    public function images(): HasMany
    {
        return $this
            ->hasMany(
                CatalogProductImage::class
            )
            ->orderBy(
                'sort_order'
            );
    }

    public function fields(): HasMany
    {
        return $this
            ->hasMany(
                CatalogProductField::class
            )
            ->orderBy(
                'sort_order'
            );
    }

    public function portfolioProjects(): HasMany
    {
        return $this->hasMany(
            PortfolioProject::class
        );
    }

    public function quoteRequestItems(): HasMany
    {
        return $this->hasMany(
            QuoteRequestItem::class
        );
    }
}