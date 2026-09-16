<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\WebsiteAd;
use App\Services\MediaService;
use App\Services\WebsiteAdService;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService,
        private readonly WebsiteAdService $websiteAdService
    ) {
    }

    public function about(): Response
    {
        return $this->renderPage(
            'sobre-nosotros',
            WebsiteAd::PLACEMENT_ABOUT_INLINE
        );
    }

    public function services(): Response
    {
        return $this->renderPage(
            'servicios',
            WebsiteAd::PLACEMENT_SERVICES_INLINE
        );
    }

    private function renderPage(
        string $slug,
        string $promotionPlacement
    ): Response {
        $page =
            Page::query()
                ->where(
                    'slug',
                    $slug
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

        return Inertia::render(
            'Public/Pages/Show',
            [
                'page' =>
                    $this
                        ->serializePage(
                            $page
                        ),

                'promotions' => [
                    'inline' =>
                        $this
                            ->websiteAdService
                            ->placement(
                                $promotionPlacement
                            ),
                ],
            ]
        );
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
                $page->meta_description,

            'canonical_url' =>
                $page->canonical_url,

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
                                    ? $this
                                        ->serializeMedia(
                                            $section
                                                ->media
                                        )
                                    : null,

                            'support_media' =>
                                $section
                                    ->supportMedia
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
                $media->alt_text,

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