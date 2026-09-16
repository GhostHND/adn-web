<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AppCatalogProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_catalog_item_id',
        'item_code',
        'name',
        'description',
        'item_type',
        'category',
        'pricing_method',
        'measurement_unit',
        'active',
        'source_created_at',
        'source_updated_at',
        'source_deleted_at',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'app_catalog_item_id' =>
                'integer',

            'active' =>
                'boolean',

            'source_created_at' =>
                'datetime',

            'source_updated_at' =>
                'datetime',

            'source_deleted_at' =>
                'datetime',

            'synced_at' =>
                'datetime',
        ];
    }

    public function webProduct(): HasOne
    {
        return $this->hasOne(
            CatalogProduct::class,
            'app_catalog_product_id'
        );
    }
}