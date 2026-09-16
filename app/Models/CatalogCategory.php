<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code',
        'slug',
        'name',
        'description',
        'parent_id',
        'media_id',
        'status',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            CatalogCategory::class,
            'parent_id'
        );
    }

    public function children(): HasMany
    {
        return $this
            ->hasMany(
                CatalogCategory::class,
                'parent_id'
            )
            ->orderBy('sort_order');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(
            Media::class
        );
    }

    public function products(): HasMany
    {
        return $this
            ->hasMany(
                CatalogProduct::class,
                'category_id'
            )
            ->orderBy('sort_order');
    }
}