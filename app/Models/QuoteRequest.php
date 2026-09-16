<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'public_token',
        'status',
        'client_name',
        'phone',
        'whatsapp',
        'email',
        'company',
        'notes',
        'source',
        'privacy_consent',
        'ip_address',
        'user_agent',
        'app_sync_status',
        'app_lead_id',
        'app_client_id',
        'sync_attempts',
        'last_sync_at',
        'sync_error',
    ];

    protected function casts(): array
    {
        return [
            'privacy_consent' => 'boolean',
            'app_lead_id' => 'integer',
            'app_client_id' => 'integer',
            'sync_attempts' => 'integer',
            'last_sync_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this
            ->hasMany(
                QuoteRequestItem::class
            )
            ->orderBy('sort_order');
    }

    public function files(): HasMany
    {
        return $this->hasMany(
            QuoteRequestFile::class
        );
    }

    public function integrationLogs(): HasMany
    {
        return $this
            ->hasMany(
                IntegrationLog::class
            )
            ->latest();
    }
}