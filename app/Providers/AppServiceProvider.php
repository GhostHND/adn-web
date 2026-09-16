<?php

namespace App\Services;

use App\Models\ContactMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ContactLeadIntegrationService
{
    public function sync(
        ContactMessage $message
    ): bool {
        if (
            $message
                ->app_sync_status
            ===
            ContactMessage::SYNC_SYNCED
            &&
            $message
                ->app_lead_id
        ) {
            return true;
        }

        if (
            !$message
                ->message_number
        ) {
            $message->update([
                'app_sync_status' =>
                    ContactMessage::SYNC_FAILED,

                'sync_error' =>
                    'El mensaje no tiene número público de referencia.',

                'last_sync_at' =>
                    now(),
            ]);

            return false;
        }

        $message->update([
            'app_sync_status' =>
                ContactMessage::SYNC_PENDING,

            'sync_attempts' =>
                (
                    (int)
                    $message
                        ->sync_attempts
                )
                +
                1,

            'sync_error' =>
                null,
        ]);

        try {
            $appUrl =
                trim(
                    (string) config(
                        'adn_integration.app_url',
                        ''
                    )
                );

            $secret =
                trim(
                    (string) config(
                        'adn_integration.secret',
                        ''
                    )
                );

            $endpoint =
                trim(
                    (string) config(
                        'adn_integration.contact_endpoint',
                        'integrations/adn-web/contact-leads'
                    )
                );

            if (
                $endpoint ===
                ''
            ) {
                $endpoint =
                    'integrations/adn-web/contact-leads';
            }

            if (
                $appUrl ===
                ''
                ||
                $secret ===
                ''
            ) {
                throw new RuntimeException(
                    'La integración con ADN APP no está configurada.'
                );
            }

            $payload =
                $this->buildPayload(
                    $message
                );

            $json =
                json_encode(
                    $payload,
                    JSON_UNESCAPED_UNICODE
                    |
                    JSON_UNESCAPED_SLASHES
                    |
                    JSON_THROW_ON_ERROR
                );

            $timestamp =
                (string)
                now()->timestamp;

            $idempotencyKey =
                'adn-web:'
                .
                $message
                    ->message_number
                .
                ':contact-lead:v1';

            $signaturePayload =
                $timestamp
                .
                "\n"
                .
                $idempotencyKey
                .
                "\n"
                .
                $json;

            $signature =
                hash_hmac(
                    'sha256',
                    $signaturePayload,
                    $secret
                );

            $url =
                rtrim(
                    $appUrl,
                    '/'
                )
                .
                '/'
                .
                ltrim(
                    $endpoint,
                    '/'
                );

            $response =
                Http::acceptJson()
                    ->timeout(
                        max(
                            1,
                            (int) config(
                                'adn_integration.timeout',
                                5
                            )
                        )
                    )
                    ->connectTimeout(
                        max(
                            1,
                            (int) config(
                                'adn_integration.connect_timeout',
                                2
                            )
                        )
                    )
                    ->withHeaders([
                        'X-ADN-Timestamp' =>
                            $timestamp,

                        'X-ADN-Idempotency-Key' =>
                            $idempotencyKey,

                        'X-ADN-Signature' =>
                            $signature,
                    ])
                    ->withBody(
                        $json,
                        'application/json'
                    )
                    ->post(
                        $url
                    );

            $responsePayload =
                $response->json();

            if (
                !is_array(
                    $responsePayload
                )
            ) {
                $responsePayload = [
                    'message' =>
                        Str::limit(
                            $response->body(),
                            2000
                        ),
                ];
            }

            if (
                !$response->successful()
                ||
                (
                    $responsePayload[
                        'ok'
                    ]
                    ?? false
                ) !==
                true
            ) {
                throw new RuntimeException(
                    (string) (
                        $responsePayload[
                            'message'
                        ]
                        ??
                        (
                            'ADN APP respondió HTTP '
                            .
                            $response->status()
                        )
                    )
                );
            }

            $lead =
                $responsePayload[
                    'lead'
                ]
                ?? null;

            $leadId =
                is_array(
                    $lead
                )
                    ? (
                        $lead[
                            'id'
                        ]
                        ?? null
                    )
                    : null;

            if (
                !$leadId
            ) {
                throw new RuntimeException(
                    'ADN APP no devolvió el identificador de la solicitud.'
                );
            }

            $message->update([
                'app_sync_status' =>
                    ContactMessage::SYNC_SYNCED,

                'app_lead_id' =>
                    (int)
                    $leadId,

                'last_sync_at' =>
                    now(),

                'sync_error' =>
                    null,
            ]);

            return true;
        } catch (
            Throwable $exception
        ) {
            $message->update([
                'app_sync_status' =>
                    ContactMessage::SYNC_FAILED,

                'last_sync_at' =>
                    now(),

                'sync_error' =>
                    Str::limit(
                        $exception
                            ->getMessage(),
                        2000
                    ),
            ]);

            report(
                $exception
            );

            return false;
        }
    }

    private function buildPayload(
        ContactMessage $message
    ): array {
        return [
            'source_reference' =>
                $message
                    ->message_number,

            'name' =>
                $message->name,

            'business_name' =>
                $message->company,

            'phone' =>
                $message->phone,

            'email' =>
                $message->email,

            'service_interest' =>
                $message->subject,

            'message' =>
                $message->message,

            'preferred_contact' =>
                $message
                    ->preferred_contact,

            'client_ip' =>
                $message
                    ->ip_address,

            'client_user_agent' =>
                $message
                    ->user_agent
                    ? Str::limit(
                        $message
                            ->user_agent,
                        1000,
                        ''
                    )
                    : null,
        ];
    }
}