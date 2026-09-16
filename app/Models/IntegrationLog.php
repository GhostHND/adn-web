<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_request_id',
        'direction',
        'event',
        'status',
        'idempotency_key',
        'request_payload',
        'response_payload',
        'http_status',
        'error_message',
        'attempted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'response_payload' => 'array',
            'http_status' => 'integer',
            'attempted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(
            QuoteRequest::class
        );
    }
}