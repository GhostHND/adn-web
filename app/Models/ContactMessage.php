<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const STATUS_NEW =
        'new';

    public const STATUS_REVIEWING =
        'reviewing';

    public const STATUS_CONTACTED =
        'contacted';

    public const STATUS_CLOSED =
        'closed';

    public const STATUS_SPAM =
        'spam';

    public const SYNC_PENDING =
        'pending';

    public const SYNC_SYNCED =
        'synced';

    public const SYNC_FAILED =
        'failed';

    protected $fillable = [
        'message_number',
        'public_token',
        'status',

        'name',
        'company',
        'phone',
        'email',
        'preferred_contact',
        'subject',
        'message',

        'source',
        'privacy_consent',
        'ip_address',
        'user_agent',

        'app_sync_status',
        'app_lead_id',
        'sync_attempts',
        'last_sync_at',
        'sync_error',

        'read_at',
        'replied_at',
    ];

    protected function casts(): array
    {
        return [
            'privacy_consent' =>
                'boolean',

            'sync_attempts' =>
                'integer',

            'last_sync_at' =>
                'datetime',

            'read_at' =>
                'datetime',

            'replied_at' =>
                'datetime',
        ];
    }
}