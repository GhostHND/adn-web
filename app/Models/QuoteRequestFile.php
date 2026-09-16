<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRequestFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_request_id',
        'quote_request_item_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(
            QuoteRequest::class
        );
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(
            QuoteRequestItem::class,
            'quote_request_item_id'
        );
    }
}