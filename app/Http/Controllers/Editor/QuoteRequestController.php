<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\UpdateQuoteRequestStatusRequest;
use App\Models\QuoteRequest;
use App\Models\QuoteRequestFile;
use App\Models\QuoteRequestItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuoteRequestController extends Controller
{
    public function index(
        Request $request
    ): Response {
        $search =
            trim(
                (string) $request->query(
                    'search',
                    ''
                )
            );

        $status =
            trim(
                (string) $request->query(
                    'status',
                    ''
                )
            );

        $syncStatus =
            trim(
                (string) $request->query(
                    'sync',
                    ''
                )
            );

        $query =
            QuoteRequest::query()
                ->with([
                    'items' => fn ($query) =>
                        $query
                            ->orderBy(
                                'sort_order'
                            )
                            ->orderBy(
                                'id'
                            ),
                ])
                ->when(
                    $search !== '',
                    function (
                        Builder $query
                    ) use (
                        $search
                    ): void {
                        $query->where(
                            function (
                                Builder $inner
                            ) use (
                                $search
                            ): void {
                                $inner
                                    ->where(
                                        'request_number',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'client_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'phone',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'whatsapp',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'email',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'company',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhereHas(
                                        'items',
                                        fn (
                                            Builder $itemQuery
                                        ) =>
                                            $itemQuery
                                                ->where(
                                                    'product_name_snapshot',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                                ->orWhere(
                                                    'product_code_snapshot',
                                                    'like',
                                                    "%{$search}%"
                                                )
                                    );
                            }
                        );
                    }
                )
                ->when(
                    $status !== '',
                    fn (
                        Builder $query
                    ) =>
                        $query->where(
                            'status',
                            $status
                        )
                )
                ->when(
                    $syncStatus !== '',
                    fn (
                        Builder $query
                    ) =>
                        $query->where(
                            'app_sync_status',
                            $syncStatus
                        )
                )
                ->latest(
                    'id'
                );

        $requests =
            $query
                ->paginate(
                    15
                )
                ->withQueryString();

        $requests->through(
            fn (
                QuoteRequest $quoteRequest
            ): array =>
                $this->serializeListItem(
                    $quoteRequest
                )
        );

        return Inertia::render(
            'Editor/Quotes/Index',
            [
                'requests' =>
                    $requests,

                'summary' => [
                    'total' =>
                        QuoteRequest::query()
                            ->count(),

                    'new' =>
                        QuoteRequest::query()
                            ->where(
                                'status',
                                'new'
                            )
                            ->count(),

                    'reviewing' =>
                        QuoteRequest::query()
                            ->where(
                                'status',
                                'reviewing'
                            )
                            ->count(),

                    'closed' =>
                        QuoteRequest::query()
                            ->where(
                                'status',
                                'closed'
                            )
                            ->count(),

                    'pending_sync' =>
                        QuoteRequest::query()
                            ->where(
                                'app_sync_status',
                                'pending'
                            )
                            ->count(),
                ],

                'filters' => [
                    'search' =>
                        $search,

                    'status' =>
                        $status,

                    'sync' =>
                        $syncStatus,
                ],
            ]
        );
    }

    public function show(
        QuoteRequest $quoteRequest
    ): Response {
        $quoteRequest->load([
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

            'integrationLogs' => fn ($query) =>
                $query
                    ->latest(
                        'id'
                    )
                    ->limit(
                        10
                    ),
        ]);

        return Inertia::render(
            'Editor/Quotes/Show',
            [
                'quote' =>
                    $this->serializeDetail(
                        $quoteRequest
                    ),

                'statusOptions' => [
                    [
                        'value' =>
                            'new',

                        'label' =>
                            'Nueva',
                    ],
                    [
                        'value' =>
                            'reviewing',

                        'label' =>
                            'En revisión',
                    ],
                    [
                        'value' =>
                            'contacted',

                        'label' =>
                            'Cliente contactado',
                    ],
                    [
                        'value' =>
                            'quoted',

                        'label' =>
                            'Cotizada',
                    ],
                    [
                        'value' =>
                            'closed',

                        'label' =>
                            'Cerrada',
                    ],
                    [
                        'value' =>
                            'cancelled',

                        'label' =>
                            'Cancelada',
                    ],
                ],
            ]
        );
    }

    public function updateStatus(
        UpdateQuoteRequestStatusRequest $request,
        QuoteRequest $quoteRequest
    ): RedirectResponse {
        $quoteRequest->update([
            'status' =>
                $request->validated(
                    'status'
                ),
        ]);

        return back()
            ->with(
                'success',
                'Estado de la cotización actualizado.'
            );
    }

    public function previewFile(
        QuoteRequest $quoteRequest,
        QuoteRequestFile $file
    ): BinaryFileResponse {
        $this->ensureFileBelongsToQuote(
            $quoteRequest,
            $file
        );

        abort_unless(
            Storage::disk(
                'local'
            )->exists(
                $file->path
            ),
            404
        );

        return response()->file(
            Storage::disk(
                'local'
            )->path(
                $file->path
            ),
            [
                'Content-Type' =>
                    $file->mime_type
                    ?: 'application/octet-stream',

                'Cache-Control' =>
                    'private, no-store, max-age=0',
            ]
        );
    }

    public function downloadFile(
        QuoteRequest $quoteRequest,
        QuoteRequestFile $file
    ): StreamedResponse {
        $this->ensureFileBelongsToQuote(
            $quoteRequest,
            $file
        );

        abort_unless(
            Storage::disk(
                'local'
            )->exists(
                $file->path
            ),
            404
        );

        return Storage::disk(
            'local'
        )->download(
            $file->path,
            $file->original_name
        );
    }

    private function ensureFileBelongsToQuote(
        QuoteRequest $quoteRequest,
        QuoteRequestFile $file
    ): void {
        abort_unless(
            (int) $file->quote_request_id ===
                (int) $quoteRequest->id,
            404
        );
    }

    private function serializeListItem(
        QuoteRequest $quoteRequest
    ): array {
        $firstItem =
            $quoteRequest
                ->items
                ->first();

        return [
            'id' =>
                $quoteRequest->id,

            'request_number' =>
                $quoteRequest->request_number,

            'status' =>
                $quoteRequest->status,

            'client_name' =>
                $quoteRequest->client_name,

            'phone' =>
                $quoteRequest->phone,

            'whatsapp' =>
                $quoteRequest->whatsapp,

            'email' =>
                $quoteRequest->email,

            'company' =>
                $quoteRequest->company,

            'source' =>
                $quoteRequest->source,

            'app_sync_status' =>
                $quoteRequest->app_sync_status,

            'created_at' =>
                $quoteRequest->created_at
                    ?->toIso8601String(),

            'product' =>
                $firstItem
                    ? [
                        'name' =>
                            $firstItem
                                ->product_name_snapshot,

                        'code' =>
                            $firstItem
                                ->product_code_snapshot,

                        'quantity' =>
                            $firstItem
                                ->quantity,
                    ]
                    : null,

            'items_count' =>
                $quoteRequest
                    ->items
                    ->count(),
        ];
    }

    private function serializeDetail(
        QuoteRequest $quoteRequest
    ): array {
        return [
            'id' =>
                $quoteRequest->id,

            'request_number' =>
                $quoteRequest->request_number,

            'public_token' =>
                $quoteRequest->public_token,

            'status' =>
                $quoteRequest->status,

            'client_name' =>
                $quoteRequest->client_name,

            'phone' =>
                $quoteRequest->phone,

            'whatsapp' =>
                $quoteRequest->whatsapp,

            'email' =>
                $quoteRequest->email,

            'company' =>
                $quoteRequest->company,

            'notes' =>
                $quoteRequest->notes,

            'source' =>
                $quoteRequest->source,

            'privacy_consent' =>
                (bool) $quoteRequest
                    ->privacy_consent,

            'ip_address' =>
                $quoteRequest->ip_address,

            'user_agent' =>
                $quoteRequest->user_agent,

            'app_sync_status' =>
                $quoteRequest->app_sync_status,

            'app_lead_id' =>
                $quoteRequest->app_lead_id,

            'app_client_id' =>
                $quoteRequest->app_client_id,

            'sync_attempts' =>
                $quoteRequest->sync_attempts,

            'last_sync_at' =>
                $quoteRequest->last_sync_at
                    ?->toIso8601String(),

            'sync_error' =>
                $quoteRequest->sync_error,

            'created_at' =>
                $quoteRequest->created_at
                    ?->toIso8601String(),

            'updated_at' =>
                $quoteRequest->updated_at
                    ?->toIso8601String(),

            'items' =>
                $quoteRequest
                    ->items
                    ->map(
                        fn (
                            QuoteRequestItem $item
                        ): array => [
                            'id' =>
                                $item->id,

                            'product_code' =>
                                $item
                                    ->product_code_snapshot,

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

                            'reference_price' =>
                                $item
                                    ->reference_price_snapshot,

                            'notes' =>
                                $item->notes,

                            'values' =>
                                $item
                                    ->values
                                    ->map(
                                        fn (
                                            $value
                                        ): array => [
                                            'id' =>
                                                $value->id,

                                            'field_key' =>
                                                $value
                                                    ->field_key,

                                            'label' =>
                                                $value
                                                    ->label_snapshot,

                                            'value_text' =>
                                                $value
                                                    ->value_text,

                                            'value_json' =>
                                                $value
                                                    ->value_json,

                                            'unit' =>
                                                $value
                                                    ->unit_snapshot,
                                        ]
                                    )
                                    ->values(),

                            'files' =>
                                $item
                                    ->files
                                    ->map(
                                        fn (
                                            QuoteRequestFile $file
                                        ): array => [
                                            'id' =>
                                                $file->id,

                                            'original_name' =>
                                                $file
                                                    ->original_name,

                                            'mime_type' =>
                                                $file
                                                    ->mime_type,

                                            'size' =>
                                                $file->size,

                                            'preview_url' =>
                                                route(
                                                    'editor.quotes.files.preview',
                                                    [
                                                        'quoteRequest' =>
                                                            $quoteRequest->id,

                                                        'file' =>
                                                            $file->id,
                                                    ]
                                                ),

                                            'download_url' =>
                                                route(
                                                    'editor.quotes.files.download',
                                                    [
                                                        'quoteRequest' =>
                                                            $quoteRequest->id,

                                                        'file' =>
                                                            $file->id,
                                                    ]
                                                ),
                                        ]
                                    )
                                    ->values(),
                        ]
                    )
                    ->values(),

            'integration_logs' =>
                $quoteRequest
                    ->integrationLogs
                    ->map(
                        fn (
                            $log
                        ): array => [
                            'id' =>
                                $log->id,

                            'direction' =>
                                $log->direction,

                            'event' =>
                                $log->event,

                            'status' =>
                                $log->status,

                            'http_status' =>
                                $log->http_status,

                            'error_message' =>
                                $log->error_message,

                            'attempted_at' =>
                                $log->attempted_at
                                    ?->toIso8601String(),

                            'completed_at' =>
                                $log->completed_at
                                    ?->toIso8601String(),
                        ]
                    )
                    ->values(),
        ];
    }
}