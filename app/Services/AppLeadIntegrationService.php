<?php

namespace App\Services;

use App\Models\CatalogProduct;
use App\Models\IntegrationLog;
use App\Models\QuoteRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class AppLeadIntegrationService
{
    public function sync(
        QuoteRequest $quote
    ): bool {
        if (
            $quote->app_sync_status ===
                'synced'
            &&
            $quote->app_lead_id
        ) {
            return true;
        }

        $quote->load([
            'items' => fn ($query) =>
                $query
                    ->orderBy(
                        'sort_order'
                    )
                    ->orderBy(
                        'id'
                    ),

            'items.values' => fn ($query) =>
                $query
                    ->orderBy(
                        'sort_order'
                    )
                    ->orderBy(
                        'id'
                    ),

            'items.files' => fn ($query) =>
                $query
                    ->orderBy(
                        'id'
                    ),
        ]);

        $payload =
            $this->buildPayload(
                $quote
            );

        $idempotencyKey =
            'adn-web:'
            . $quote->request_number
            . ':website-lead:v1';

        $log =
            IntegrationLog::query()
                ->firstOrNew([
                    'idempotency_key' =>
                        $idempotencyKey,
                ]);

        $log->fill([
            'quote_request_id' =>
                $quote->id,

            'direction' =>
                'outbound',

            'event' =>
                'website_lead.create',

            'status' =>
                'pending',

            'request_payload' =>
                $payload,

            'response_payload' =>
                null,

            'http_status' =>
                null,

            'error_message' =>
                null,

            'attempted_at' =>
                now(),

            'completed_at' =>
                null,
        ]);

        $log->save();

        $quote->update([
            'app_sync_status' =>
                'pending',

            'sync_attempts' =>
                (
                    (int) $quote
                        ->sync_attempts
                )
                +
                1,

            'sync_error' =>
                null,
        ]);

        $appUrl =
            trim(
                (string) config(
                    'adn_integration.app_url',
                    ''
                )
            );

        $endpoint =
            trim(
                (string) config(
                    'adn_integration.endpoint',
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

        try {
            if (
                $appUrl ===
                    ''
                ||
                $endpoint ===
                    ''
                ||
                $secret ===
                    ''
            ) {
                throw new RuntimeException(
                    'La integración con ADN APP no está configurada.'
                );
            }

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
                (string) now()
                    ->timestamp;

            $signaturePayload =
                $timestamp
                . "\n"
                . $idempotencyKey
                . "\n"
                . $json;

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
                . '/'
                . ltrim(
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
                    'body' =>
                        Str::limit(
                            $response->body(),
                            5000
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
                $message =
                    $responsePayload[
                        'message'
                    ]
                    ?? (
                        'ADN APP respondió HTTP '
                        . $response->status()
                    );

                throw new RuntimeException(
                    (string) $message
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

            $quote->update([
                'app_sync_status' =>
                    'synced',

                'app_lead_id' =>
                    (int) $leadId,

                'app_client_id' =>
                    !empty(
                        $lead[
                            'client_id'
                        ]
                    )
                        ? (int) $lead[
                            'client_id'
                        ]
                        : null,

                'last_sync_at' =>
                    now(),

                'sync_error' =>
                    null,
            ]);

            $log->update([
                'status' =>
                    'success',

                'response_payload' =>
                    $responsePayload,

                'http_status' =>
                    $response->status(),

                'error_message' =>
                    null,

                'completed_at' =>
                    now(),
            ]);

            return true;
        } catch (
            Throwable $exception
        ) {
            $quote->update([
                'app_sync_status' =>
                    'failed',

                'last_sync_at' =>
                    now(),

                'sync_error' =>
                    Str::limit(
                        $exception->getMessage(),
                        2000
                    ),
            ]);

            $log->update([
                'status' =>
                    'failed',

                'error_message' =>
                    Str::limit(
                        $exception->getMessage(),
                        2000
                    ),

                'completed_at' =>
                    now(),
            ]);

            report(
                $exception
            );

            return false;
        }
    }

    private function buildPayload(
        QuoteRequest $quote
    ): array {
        $productIds =
            $quote
                ->items
                ->pluck(
                    'catalog_product_id'
                )
                ->filter()
                ->map(
                    fn (
                        $id
                    ): int =>
                        (int) $id
                )
                ->unique()
                ->values();

        $appReferences =
            CatalogProduct::query()
                ->whereIn(
                    'id',
                    $productIds
                )
                ->pluck(
                    'app_reference_code',
                    'id'
                );

        return [
            'request_number' =>
                $quote->request_number,

            'client_name' =>
                $quote->client_name,

            'company' =>
                $quote->company,

            'phone' =>
                $quote->phone,

            'whatsapp' =>
                $quote->whatsapp,

            'email' =>
                $quote->email,

            'notes' =>
                $quote->notes,

            'client_ip' =>
                $quote->ip_address,

            'client_user_agent' =>
                $quote->user_agent,

            'submitted_at' =>
                $quote->created_at
                    ?->toIso8601String(),

            'items' =>
                $quote
                    ->items
                    ->map(
                        function (
                            $item
                        ) use (
                            $appReferences
                        ): array {
                            $values =
                                $item
                                    ->values
                                    ->filter(
                                        function (
                                            $value
                                        ): bool {
                                            $json =
                                                $value
                                                    ->value_json;

                                            if (
                                                !is_array(
                                                    $json
                                                )
                                            ) {
                                                return true;
                                            }

                                            return (
                                                $json[
                                                    'field_type'
                                                ]
                                                ?? null
                                            ) !==
                                                'file';
                                        }
                                    )
                                    ->map(
                                        fn (
                                            $value
                                        ): array => [
                                            'label' =>
                                                $value
                                                    ->label_snapshot,

                                            'value' =>
                                                $value
                                                    ->value_text,

                                            'unit' =>
                                                $value
                                                    ->unit_snapshot,
                                        ]
                                    )
                                    ->values()
                                    ->all();

                            $files =
                                $item
                                    ->files
                                    ->map(
                                        fn (
                                            $file
                                        ): array => [
                                            'original_name' =>
                                                $file
                                                    ->original_name,

                                            'mime_type' =>
                                                $file
                                                    ->mime_type,

                                            'size' =>
                                                $file
                                                    ->size,
                                        ]
                                    )
                                    ->values()
                                    ->all();

                            return [
                                'product_code' =>
                                    $item
                                        ->product_code_snapshot,

                                'app_reference_code' =>
                                    $appReferences->get(
                                        $item
                                            ->catalog_product_id
                                    ),

                                'product_name' =>
                                    $item
                                        ->product_name_snapshot,

                                'quantity' =>
                                    $item->quantity,

                                'unit' =>
                                    $item
                                        ->unit_snapshot,

                                'quote_mode' =>
                                    $item
                                        ->quote_mode_snapshot,

                                'values' =>
                                    $values,

                                'files' =>
                                    $files,
                            ];
                        }
                    )
                    ->values()
                    ->all(),
        ];
    }
}