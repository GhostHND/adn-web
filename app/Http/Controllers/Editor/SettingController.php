<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\UpdateContactSettingsRequest;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        $contact =
            Setting::query()
                ->where(
                    'group',
                    'contact'
                )
                ->pluck(
                    'value',
                    'key'
                );

        $social =
            Setting::query()
                ->where(
                    'group',
                    'social'
                )
                ->pluck(
                    'value',
                    'key'
                );

        $recentMessages =
            ContactMessage::query()
                ->latest(
                    'id'
                )
                ->limit(
                    6
                )
                ->get()
                ->map(
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

                        'subject' =>
                            $message
                                ->subject,

                        'status' =>
                            $message
                                ->status,

                        'app_sync_status' =>
                            $message
                                ->app_sync_status,

                        'created_at' =>
                            $message
                                ->created_at
                                ?->format(
                                    'd/m/Y H:i'
                                ),
                    ]
                )
                ->values();

        return Inertia::render(
            'Editor/Contact/Index',
            [
                'settings' => [
                    'contact' => [
                        'phone' =>
                            $contact->get(
                                'phone'
                            ),

                        'whatsapp' =>
                            $contact->get(
                                'whatsapp'
                            ),

                        'email' =>
                            $contact->get(
                                'email'
                            ),

                        'address' =>
                            $contact->get(
                                'address'
                            ),

                        'business_hours' =>
                            $contact->get(
                                'business_hours'
                            ),

                        'map_url' =>
                            $contact->get(
                                'map_url'
                            ),
                    ],

                    'social' => [
                        'facebook' =>
                            $social->get(
                                'facebook'
                            ),

                        'instagram' =>
                            $social->get(
                                'instagram'
                            ),

                        'tiktok' =>
                            $social->get(
                                'tiktok'
                            ),
                    ],
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

                    'synced' =>
                        ContactMessage::query()
                            ->where(
                                'app_sync_status',
                                ContactMessage::SYNC_SYNCED
                            )
                            ->count(),
                ],

                'recentMessages' =>
                    $recentMessages,
            ]
        );
    }

    public function update(
        UpdateContactSettingsRequest $request
    ): RedirectResponse {
        $data =
            $request->validated();

        $settings = [
            [
                'group' =>
                    'contact',

                'key' =>
                    'phone',

                'value' =>
                    $data[
                        'phone'
                    ]
                    ?? null,

                'type' =>
                    'text',

                'sort_order' =>
                    10,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'whatsapp',

                'value' =>
                    $data[
                        'whatsapp'
                    ]
                    ?? null,

                'type' =>
                    'text',

                'sort_order' =>
                    20,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'email',

                'value' =>
                    $data[
                        'email'
                    ]
                    ?? null,

                'type' =>
                    'email',

                'sort_order' =>
                    30,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'address',

                'value' =>
                    $data[
                        'address'
                    ]
                    ?? null,

                'type' =>
                    'textarea',

                'sort_order' =>
                    40,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'business_hours',

                'value' =>
                    $data[
                        'business_hours'
                    ]
                    ?? null,

                'type' =>
                    'textarea',

                'sort_order' =>
                    50,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'map_url',

                'value' =>
                    $data[
                        'map_url'
                    ]
                    ?? null,

                'type' =>
                    'url',

                'sort_order' =>
                    60,
            ],

            [
                'group' =>
                    'social',

                'key' =>
                    'facebook',

                'value' =>
                    $data[
                        'facebook'
                    ]
                    ?? null,

                'type' =>
                    'url',

                'sort_order' =>
                    10,
            ],

            [
                'group' =>
                    'social',

                'key' =>
                    'instagram',

                'value' =>
                    $data[
                        'instagram'
                    ]
                    ?? null,

                'type' =>
                    'url',

                'sort_order' =>
                    20,
            ],

            [
                'group' =>
                    'social',

                'key' =>
                    'tiktok',

                'value' =>
                    $data[
                        'tiktok'
                    ]
                    ?? null,

                'type' =>
                    'url',

                'sort_order' =>
                    30,
            ],
        ];

        DB::transaction(
            function () use (
                $settings
            ): void {
                foreach (
                    $settings
                    as
                    $setting
                ) {
                    Setting::query()
                        ->updateOrCreate(
                            [
                                'group' =>
                                    $setting[
                                        'group'
                                    ],

                                'key' =>
                                    $setting[
                                        'key'
                                    ],
                            ],
                            [
                                'value' =>
                                    $setting[
                                        'value'
                                    ],

                                'type' =>
                                    $setting[
                                        'type'
                                    ],

                                'is_public' =>
                                    true,

                                'sort_order' =>
                                    $setting[
                                        'sort_order'
                                    ],
                            ]
                        );
                }
            }
        );

        return back()
            ->with(
                'success',
                'Información de contacto actualizada correctamente.'
            );
    }
}