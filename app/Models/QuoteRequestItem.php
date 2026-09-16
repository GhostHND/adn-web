<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_request_id',
        'catalog_product_id',
        'product_code_snapshot',
        'product_name_snapshot',
        'quantity',
        'unit_snapshot',
        'quote_mode_snapshot',
        'reference_price_snapshot',
        'notes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'reference_price_snapshot' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(
            QuoteRequest::class
        );
    }

    public function catalogProduct(): BelongsTo
    {
        return $this->belongsTo(
            CatalogProduct::class
        );
    }

    public function values(): HasMany
    {
        return $this
            ->hasMany(
                QuoteRequestValue::class
            )
            ->orderBy('sort_order');
    }

    public function files(): HasMany
    {
        return $this->hasMany(
            QuoteRequestFile::class
        );
    }
}