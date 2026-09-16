<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicSite\StoreContactMessageRequest;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Services\ContactLeadIntegrationService;
use App\Services\ContactMessageService;
use App\Services\MediaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ContactController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService,
        private readonly ContactMessageService $contactMessageService,
        private readonly ContactLeadIntegrationService $contactLeadIntegrationService
    ) {
    }

    public function index(): Response
    {
        $page =
            Page::query()
                ->where(
                    'slug',
                    'contacto'
                )
                ->where(
                    'status',
                    'published'
                )
                ->where(
                    function (
                        Builder $query
                    ): void {
                        $query
                            ->whereNull(
                                'published_at'
                            )
                            ->orWhere(
                                'published_at',
                                '<=',
                                now()
                            );
                    }
                )
                ->with([
                    'sections' =>
                        fn (
                            $query
                        ) =>
                            $query
                                ->where(
                                    'active',
                                    true
                                )
                                ->orderBy(
                                    'sort_order'
                                )
                                ->orderBy(
                                    'id'
                                ),

                    'sections.media',

                    'sections.supportMedia',
                ])
                ->firstOrFail();

        $startedAt =
            now()->timestamp;

        return Inertia::render(
            'Public/Contact/Index',
            [
                'page' =>
                    $this
                        ->serializePage(
                            $page
                        ),

                'contact' =>
                    $this
                        ->settingsGroup(
                            'contact'
                        ),

                'social' =>
                    $this
                        ->settingsGroup(
                            'social'
                        ),

                'spamProtection' => [
                    'startedAt' =>
                        $startedAt,

                    'token' =>
                        hash_hmac(
                            'sha256',
                            (string)
                            $startedAt,
                            (string) config(
                                'app.key'
                            )
                        ),
                ],
            ]
        );
    }

    public function store(
        StoreContactMessageRequest $request
    ): RedirectResponse {
        $message =
            $this
                ->contactMessageService
                ->create(
                    $request->validated(),
                    $request->ip(),
                    $request->userAgent()
                );

        /*
        |--------------------------------------------------------------------------
        | SINCRONIZACIÓN CON ADN APP
        |--------------------------------------------------------------------------
        |
        | El mensaje ya existe en ADN Web antes de llegar aquí.
        | Una caída temporal de ADN APP nunca elimina la solicitud del cliente.
        |
        */

        try {
            $this
                ->contactLeadIntegrationService
                ->sync(
                    $message
                );
        } catch (
            Throwable $exception
        ) {
            report(
                $exception
            );
        }

        return redirect()
            ->route(
                'public.contact'
            )
            ->with(
                'success',
                sprintf(
                    'Mensaje %s enviado correctamente. Gracias por escribirnos; revisaremos tu solicitud para continuar contigo.',
                    $message
                        ->message_number
                )
            );
    }

    private function settingsGroup(
        string $group
    ): array {
        return DB::table(
            'settings'
        )
            ->where(
                'group',
                $group
            )
            ->where(
                'is_public',
                true
            )
            ->orderBy(
                'sort_order'
            )
            ->orderBy(
                'id'
            )
            ->get([
                'key',
                'value',
            ])
            ->mapWithKeys(
                fn (
                    object $setting
                ): array => [
                    $setting->key =>
                        $setting->value,
                ]
            )
            ->all();
    }

    private function serializePage(
        Page $page
    ): array {
        return [
            'slug' =>
                $page->slug,

            'name' =>
                $page->name,

            'title' =>
                $page->title,

            'eyebrow' =>
                $page->eyebrow,

            'summary' =>
                $page->summary,

            'meta_title' =>
                $page->meta_title,

            'meta_description' =>
                $page
                    ->meta_description,

            'canonical_url' =>
                $page
                    ->canonical_url,

            'sections' =>
                $page
                    ->sections
                    ->map(
                        fn (
                            PageSection $section
                        ): array => [
                            'id' =>
                                $section->id,

                            'section_key' =>
                                $section
                                    ->section_key,

                            'section_type' =>
                                $section
                                    ->section_type,

                            'title' =>
                                $section->title,

                            'subtitle' =>
                                $section
                                    ->subtitle,

                            'body' =>
                                $section->body,

                            'content' =>
                                $section
                                    ->content
                                ?? [],

                            'settings' =>
                                $section
                                    ->settings
                                ?? [],

                            'media' =>
                                $section->media
                                &&
                                $section
                                    ->media
                                    ->is_public
                                    ? $this
                                        ->serializeMedia(
                                            $section
                                                ->media
                                        )
                                    : null,

                            'support_media' =>
                                $section
                                    ->supportMedia
                                    ->filter(
                                        fn (
                                            Media $media
                                        ): bool =>
                                            (bool)
                                            $media
                                                ->is_public
                                    )
                                    ->map(
                                        fn (
                                            Media $media
                                        ): array =>
                                            $this
                                                ->serializeMedia(
                                                    $media
                                                )
                                    )
                                    ->values()
                                    ->all(),
                        ]
                    )
                    ->values()
                    ->all(),
        ];
    }

    private function serializeMedia(
        Media $media
    ): array {
        return [
            'id' =>
                $media->id,

            'title' =>
                $media->title,

            'alt_text' =>
                $media
                    ->alt_text,

            'width' =>
                $media->width,

            'height' =>
                $media->height,

            'url' =>
                $this
                    ->mediaService
                    ->url(
                        $media
                    ),
        ];
    }
}