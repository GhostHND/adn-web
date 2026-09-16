<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'contact_messages',
            function (
                Blueprint $table
            ): void {
                $table->id();

                $table
                    ->string(
                        'message_number',
                        40
                    )
                    ->nullable()
                    ->unique();

                $table
                    ->string(
                        'public_token',
                        64
                    )
                    ->unique();

                $table
                    ->string(
                        'status',
                        30
                    )
                    ->default(
                        'new'
                    )
                    ->index();

                $table
                    ->string(
                        'name',
                        160
                    );

                $table
                    ->string(
                        'company',
                        190
                    )
                    ->nullable();

                $table
                    ->string(
                        'phone',
                        40
                    )
                    ->nullable();

                $table
                    ->string(
                        'email',
                        190
                    )
                    ->nullable();

                $table
                    ->string(
                        'preferred_contact',
                        30
                    )
                    ->nullable();

                $table
                    ->string(
                        'subject',
                        190
                    );

                $table
                    ->text(
                        'message'
                    );

                $table
                    ->string(
                        'source',
                        50
                    )
                    ->default(
                        'website'
                    );

                $table
                    ->boolean(
                        'privacy_consent'
                    )
                    ->default(
                        false
                    );

                $table
                    ->string(
                        'ip_address',
                        45
                    )
                    ->nullable();

                $table
                    ->text(
                        'user_agent'
                    )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | FUTURA INTEGRACIÓN CON ADN APP
                |--------------------------------------------------------------------------
                |
                | Estos campos no realizan ninguna conexión SQL con ADN APP.
                | Únicamente dejan preparado el mensaje para una futura
                | sincronización mediante la integración HMAC.
                |
                */

                $table
                    ->string(
                        'app_sync_status',
                        30
                    )
                    ->default(
                        'pending'
                    )
                    ->index();

                $table
                    ->unsignedBigInteger(
                        'app_lead_id'
                    )
                    ->nullable();

                $table
                    ->unsignedInteger(
                        'sync_attempts'
                    )
                    ->default(
                        0
                    );

                $table
                    ->timestamp(
                        'last_sync_at'
                    )
                    ->nullable();

                $table
                    ->text(
                        'sync_error'
                    )
                    ->nullable();

                $table
                    ->timestamp(
                        'read_at'
                    )
                    ->nullable();

                $table
                    ->timestamp(
                        'replied_at'
                    )
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'status',
                    'created_at',
                ]);

                $table->index([
                    'email',
                    'created_at',
                ]);

                $table->index([
                    'phone',
                    'created_at',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'contact_messages'
        );
    }
};