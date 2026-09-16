<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CatalogProductField extends Model
{
    use HasFactory;

    protected $fillable = [
        'catalog_product_id',
        'field_key',
        'label',
        'field_type',
        'placeholder',
        'help_text',
        'unit',
        'required',
        'min_value',
        'max_value',
        'step',
        'default_value',
        'validation_rules',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'required' => 'boolean',
            'min_value' => 'decimal:4',
            'max_value' => 'decimal:4',
            'step' => 'decimal:4',
            'validation_rules' => 'array',
            'sort_order' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            CatalogProduct::class,
            'catalog_product_id'
        );
    }

    public function options(): HasMany
    {
        return $this
            ->hasMany(
                CatalogProductOption::class
            )
            ->orderBy('sort_order');
    }
}