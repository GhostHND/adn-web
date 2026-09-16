<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Models\CatalogProduct;
use App\Models\QuoteRequest;
use App\Models\QuoteRequestFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AppQuoteRequestController extends Controller
{
    public function show(
        string $requestNumber
    ): JsonResponse {
        $quote =
            $this->findQuote(
                $requestNumber
            );

        $quote->load([
            'items' =>
                fn ($query) =>
                    $query
                        ->orderBy(
                            'sort_order'
                        )
                        ->orderBy(
                            'id'
                        ),

            'items.values' =>
                fn ($query) =>
                    $query
                        ->orderBy(
                            'sort_order'
                        )
                        ->orderBy(
                            'id'
                        ),

            'files' =>
                fn ($query) =>
                    $query
                        ->orderBy(
                            'id'
                        ),
        ]);

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

        $items =
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
                                    function (
                                        $value
                                    ): array {
                                        $json =
                                            is_array(
                                                $value
                                                    ->value_json
                                            )
                                                ? $value
                                                    ->value_json
                                                : [];

                                        return [
                                            'id' =>
                                                $value->id,

                                            'field_key' =>
                                                $value
                                                    ->field_key,

                                            'label' =>
                                                $value
                                                    ->label_snapshot,

                                            'value' =>
                                                $value
                                                    ->value_text,

                                            'unit' =>
                                                $value
                                                    ->unit_snapshot,

                                            'field_type' =>
                                                $json[
                                                    'field_type'
                                                ]
                                                ?? null,
                                        ];
                                    }
                                )
                                ->values()
                                ->all();

                        return [
                            'id' =>
                                $item->id,

                            'product_id' =>
                                $item
                                    ->catalog_product_id,

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

                            'quantity_display' =>
                                $this
                                    ->decimalDisplay(
                                        $item
                                            ->quantity
                                    ),

                            'unit' =>
                                $item
                                    ->unit_snapshot,

                            'quote_mode' =>
                                $item
                                    ->quote_mode_snapshot,

                            'values' =>
                                $values,
                        ];
                    }
                )
                ->values()
                ->all();

        $files =
            $quote
                ->files
                ->map(
                    fn (
                        QuoteRequestFile $file
                    ): array => [
                        'id' =>
                            $file->id,

                        'quote_request_item_id' =>
                            $file
                                ->quote_request_item_id,

                        'original_name' =>
                            $file
                                ->original_name,

                        'mime_type' =>
                            $file
                                ->mime_type,

                        'size' =>
                            $file->size,

                        'is_image' =>
                            str_starts_with(
                                strtolower(
                                    (string)
                                    $file->mime_type
                                ),
                                'image/'
                            ),
                    ]
                )
                ->values()
                ->all();

        return response()->json([
            'ok' =>
                true,

            'request' => [
                'id' =>
                    $quote->id,

                'request_number' =>
                    $quote
                        ->request_number,

                'status' =>
                    $quote->status,

                'client_name' =>
                    $quote
                        ->client_name,

                'company' =>
                    $quote
                        ->company,

                'phone' =>
                    $quote->phone,

                'whatsapp' =>
                    $quote
                        ->whatsapp,

                'email' =>
                    $quote->email,

                'notes' =>
                    $quote->notes,

                'source' =>
                    $quote->source,

                'privacy_consent' =>
                    (bool) $quote
                        ->privacy_consent,

                'created_at' =>
                    $quote
                        ->created_at
                        ?->toIso8601String(),

                'items' =>
                    $items,

                'files' =>
                    $files,
            ],
        ]);
    }

    public function previewFile(
        string $requestNumber,
        int $fileId
    ): StreamedResponse {
        return $this
            ->fileResponse(
                $requestNumber,
                $fileId,
                'inline'
            );
    }

    public function downloadFile(
        string $requestNumber,
        int $fileId
    ): StreamedResponse {
        return $this
            ->fileResponse(
                $requestNumber,
                $fileId,
                'attachment'
            );
    }

    private function findQuote(
        string $requestNumber
    ): QuoteRequest {
        abort_unless(
            preg_match(
                '/^SOL-WEB-\d{6,}$/',
                $requestNumber
            ) ===
                1,
            404
        );

        return QuoteRequest::query()
            ->where(
                'request_number',
                $requestNumber
            )
            ->firstOrFail();
    }

    private function fileResponse(
        string $requestNumber,
        int $fileId,
        string $disposition
    ): StreamedResponse {
        $quote =
            $this->findQuote(
                $requestNumber
            );

        $file =
            QuoteRequestFile::query()
                ->where(
                    'id',
                    $fileId
                )
                ->where(
                    'quote_request_id',
                    $quote->id
                )
                ->firstOrFail();

        $disk =
            Storage::disk(
                'local'
            );

        abort_unless(
            $disk->exists(
                $file->path
            ),
            404
        );

        $originalName =
            trim(
                (string) $file
                    ->original_name
            );

        if (
            $originalName ===
            ''
        ) {
            $originalName =
                'archivo-'
                . $file->id;
        }

        $fallbackName =
            preg_replace(
                '/[^A-Za-z0-9._-]+/',
                '-',
                $originalName
            )
            ?: (
                'archivo-'
                . $file->id
            );

        $contentDisposition =
            HeaderUtils::makeDisposition(
                $disposition,
                $originalName,
                $fallbackName
            );

        $headers = [
            'Content-Type' =>
                $file->mime_type
                ?: 'application/octet-stream',

            'Content-Disposition' =>
                $contentDisposition,

            'Cache-Control' =>
                'private, no-store, no-cache, must-revalidate',

            'Pragma' =>
                'no-cache',

            'X-Content-Type-Options' =>
                'nosniff',
        ];

        if (
            (int) $file->size >
            0
        ) {
            $headers[
                'Content-Length'
            ] =
                (string) $file->size;
        }

        return response()->stream(
            function () use (
                $disk,
                $file
            ): void {
                $stream =
                    $disk->readStream(
                        $file->path
                    );

                abort_if(
                    $stream ===
                        false,
                    404
                );

                try {
                    fpassthru(
                        $stream
                    );
                } finally {
                    if (
                        is_resource(
                            $stream
                        )
                    ) {
                        fclose(
                            $stream
                        );
                    }
                }
            },
            200,
            $headers
        );
    }

    private function decimalDisplay(
        mixed $value
    ): string {
        $formatted =
            number_format(
                (float) $value,
                4,
                '.',
                ''
            );

        $formatted =
            rtrim(
                $formatted,
                '0'
            );

        $formatted =
            rtrim(
                $formatted,
                '.'
            );

        return $formatted ===
            ''
                ? '0'
                : $formatted;
    }
}