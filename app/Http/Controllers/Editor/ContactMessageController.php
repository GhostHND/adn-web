<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\ContactLeadIntegrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ContactMessageController extends Controller
{
    public function __construct(
        private readonly ContactLeadIntegrationService $contactLeadIntegrationService
    ) {
    }

    public function index(
        Request $request
    ): Response {
        $filters =
            $request->validate([
                'search' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'status' => [
                    'nullable',

                    Rule::in([
                        ContactMessage::STATUS_NEW,
                        ContactMessage::STATUS_REVIEWING,
                        ContactMessage::STATUS_CONTACTED,
                        ContactMessage::STATUS_CLOSED,
                        ContactMessage::STATUS_SPAM,
                    ]),
                ],

                'sync' => [
                    'nullable',

                    Rule::in([
                        ContactMessage::SYNC_PENDING,
                        ContactMessage::SYNC_SYNCED,
                        ContactMessage::SYNC_FAILED,
                    ]),
                ],
            ]);

        $search =
            trim(
                (string) (
                    $filters[
                        'search'
                    ]
                    ?? ''
                )
            );

        $status =
            (string) (
                $filters[
                    'status'
                ]
                ?? ''
            );

        $sync =
            (string) (
                $filters[
                    'sync'
                ]
                ?? ''
            );

        $messages =
            ContactMessage::query()
                ->when(
                    $search !==
                    '',
                    function (
                        $query
                    ) use (
                        $search
                    ): void {
                        $query->where(
                            function (
                                $inner
                            ) use (
                                $search
                            ): void {
                                $inner
                                    ->where(
                                        'message_number',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'company',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'phone',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'email',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'subject',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'message',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                    }
                )
                ->when(
                    $status !==
                    '',
                    fn (
                        $query
                    ) =>
                        $query->where(
                            'status',
                            $status
                        )
                )
                ->when(
                    $sync !==
                    '',
                    fn (
                        $query
                    ) =>
                        $query->where(
                            'app_sync_status',
                            $sync
                        )
                )
                ->latest(
                    'id'
                )
                ->paginate(
                    25
                )
                ->withQueryString();

        $messages->through(
            fn (
                ContactMessage $message
            ): array => [
                'id' =>
                    $message->id,

                'message_number' =>
                    $message
                        ->message_number,

                'name' =>
                    $message->name,

                'company' =>
                    $message
                        ->company,

                'phone' =>
                    $message->phone,

                'email' =>
                    $message->email,

                'subject' =>
                    $message
                        ->subject,

                'status' =>
                    $message
                        ->status,

                'app_sync_status' =>
                    $message
                        ->app_sync_status,

                'app_lead_id' =>
                    $message
                        ->app_lead_id,

                'read_at' =>
                    $message
                        ->read_at
                        ?->format(
                            'd/m/Y H:i'
                        ),

                'created_at' =>
                    $message
                        ->created_at
                        ?->format(
                            'd/m/Y H:i'
                        ),
            ]
        );

        return Inertia::render(
            'Editor/Contact/Messages/Index',
            [
                'messages' =>
                    $messages,

                'filters' => [
                    'search' =>
                        $search,

                    'status' =>
                        $status,

                    'sync' =>
                        $sync,
                ],

                'stats' => [
                    'total' =>
                        ContactMessage::query()
                            ->count(),

                    'new' =>
                        ContactMessage::query()
                            ->where(
                                'status',
                                ContactMessage::STATUS_NEW
                            )
                            ->count(),

                    'pending_sync' =>
                        ContactMessage::query()
                            ->where(
                                'app_sync_status',
                                ContactMessage::SYNC_PENDING
                            )
                            ->count(),

                    'failed_sync' =>
                        ContactMessage::query()
                            ->where(
                                'app_sync_status',
                                ContactMessage::SYNC_FAILED
                            )
                            ->count(),
                ],
            ]
        );
    }

    public function show(
        ContactMessage $contactMessage
    ): Response {
        if (
            !$contactMessage
                ->read_at
        ) {
            $contactMessage->update([
                'read_at' =>
                    now(),
            ]);
        }

        $contactMessage->refresh();

        return Inertia::render(
            'Editor/Contact/Messages/Show',
            [
                'message' =>
                    $this->serializeMessage(
                        $contactMessage
                    ),
            ]
        );
    }

    public function updateStatus(
        Request $request,
        ContactMessage $contactMessage
    ): RedirectResponse {
        $validated =
            $request->validate([
                'status' => [
                    'required',

                    Rule::in([
                        ContactMessage::STATUS_NEW,
                        ContactMessage::STATUS_REVIEWING,
                        ContactMessage::STATUS_CONTACTED,
                        ContactMessage::STATUS_CLOSED,
                        ContactMessage::STATUS_SPAM,
                    ]),
                ],
            ]);

        $updates = [
            'status' =>
                $validated[
                    'status'
                ],

            'read_at' =>
                $contactMessage
                    ->read_at
                ??
                now(),
        ];

        if (
            $validated[
                'status'
            ]
            ===
            ContactMessage::STATUS_CONTACTED
            &&
            !$contactMessage
                ->replied_at
        ) {
            $updates[
                'replied_at'
            ] =
                now();
        }

        $contactMessage->update(
            $updates
        );

        return back()
            ->with(
                'success',
                'Estado del mensaje actualizado correctamente.'
            );
    }

    public function retrySync(
        ContactMessage $contactMessage
    ): RedirectResponse {
        $success =
            $this
                ->contactLeadIntegrationService
                ->sync(
                    $contactMessage
                );

        if (
            !$success
        ) {
            return back()
                ->with(
                    'error',
                    'No fue posible sincronizar el mensaje con ADN APP. Revisa el detalle del error.'
                );
        }

        return back()
            ->with(
                'success',
                'Mensaje sincronizado correctamente con ADN APP.'
            );
    }

    private function serializeMessage(
        ContactMessage $message
    ): array {
        return [
            'id' =>
                $message->id,

            'message_number' =>
                $message
                    ->message_number,

            'status' =>
                $message->status,

            'name' =>
                $message->name,

            'company' =>
                $message->company,

            'phone' =>
                $message->phone,

            'email' =>
                $message->email,

            'preferred_contact' =>
                $message
                    ->preferred_contact,

            'subject' =>
                $message->subject,

            'message' =>
                $message->message,

            'source' =>
                $message->source,

            'ip_address' =>
                $message
                    ->ip_address,

            'user_agent' =>
                $message
                    ->user_agent,

            'app_sync_status' =>
                $message
                    ->app_sync_status,

            'app_lead_id' =>
                $message
                    ->app_lead_id,

            'sync_attempts' =>
                $message
                    ->sync_attempts,

            'sync_error' =>
                $message
                    ->sync_error,

            'last_sync_at' =>
                $message
                    ->last_sync_at
                    ?->format(
                        'd/m/Y H:i:s'
                    ),

            'read_at' =>
                $message
                    ->read_at
                    ?->format(
                        'd/m/Y H:i'
                    ),

            'replied_at' =>
                $message
                    ->replied_at
                    ?->format(
                        'd/m/Y H:i'
                    ),

            'created_at' =>
                $message
                    ->created_at
                    ?->format(
                        'd/m/Y H:i:s'
                    ),
        ];
    }
}