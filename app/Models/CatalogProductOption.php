<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatalogProductOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalog_product_field_id',
        'value',
        'label',
        'extra_price',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'extra_price' => 'decimal:2',
            'sort_order' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(
            CatalogProductField::class,
            'catalog_product_field_id'
        );
    }
}