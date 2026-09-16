<?php

namespace App\Services;

use App\Models\ContactMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContactMessageService
{
    public function create(
        array $data,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): ContactMessage {
        return DB::transaction(
            function () use (
                $data,
                $ipAddress,
                $userAgent
            ): ContactMessage {
                $message =
                    ContactMessage::create([
                        'public_token' =>
                            hash(
                                'sha256',
                                Str::uuid()
                                ->toString()
                                .
                                Str::random(
                                    64
                                )
                            ),

                        'status' =>
                            ContactMessage::STATUS_NEW,

                        'name' =>
                            $data[
                                'name'
                            ],

                        'company' =>
                            $this
                                ->nullableString(
                                    $data[
                                        'company'
                                    ]
                                    ?? null
                                ),

                        'phone' =>
                            $this
                                ->nullableString(
                                    $data[
                                        'phone'
                                    ]
                                    ?? null
                                ),

                        'email' =>
                            $this
                                ->nullableString(
                                    $data[
                                        'email'
                                    ]
                                    ?? null
                                ),

                        'preferred_contact' =>
                            $this
                                ->nullableString(
                                    $data[
                                        'preferred_contact'
                                    ]
                                    ?? null
                                ),

                        'subject' =>
                            $data[
                                'subject'
                            ],

                        'message' =>
                            $data[
                                'message'
                            ],

                        'source' =>
                            'website',

                        'privacy_consent' =>
                            true,

                        'ip_address' =>
                            $ipAddress,

                        'user_agent' =>
                            $userAgent,

                        'app_sync_status' =>
                            ContactMessage::SYNC_PENDING,
                    ]);

                $message->update([
                    'message_number' =>
                        sprintf(
                            'CON-WEB-%06d',
                            $message->id
                        ),
                ]);

                return $message
                    ->fresh();
            }
        );
    }

    private function nullableString(
        mixed $value
    ): ?string {
        $value =
            trim(
                (string) (
                    $value
                    ?? ''
                )
            );

        return $value ===
            ''
                ? null
                : $value;
    }
}