<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequestValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_request_item_id',
        'field_key',
        'label_snapshot',
        'value_text',
        'value_json',
        'unit_snapshot',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'value_json' => 'array',
            'sort_order' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(
            QuoteRequestItem::class,
            'quote_request_item_id'
        );
    }
}