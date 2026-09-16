<?php

namespace App\Services;

use App\Models\WebsiteAd;
use Illuminate\Support\Facades\Storage;

class WebsiteAdService
{
    public function placement(
        string $placement,
        int $limit = 6
    ): array {
        return WebsiteAd::query()
            ->visible()
            ->placement(
                $placement
            )
            ->orderBy(
                'sort_order'
            )
            ->orderBy(
                'id'
            )
            ->limit(
                $limit
            )
            ->get()
            ->map(
                fn (
                    WebsiteAd $ad
                ): array =>
                    $this->serialize(
                        $ad
                    )
            )
            ->values()
            ->all();
    }

    public function serialize(
        WebsiteAd $ad
    ): array {
        return [
            'id' =>
                $ad->id,

            'name' =>
                $ad->name,

            'placement' =>
                $ad->placement,

            'platform' =>
                $ad->platform,

            'image_url' =>
                Storage::disk(
                    'public'
                )->url(
                    $ad->image_path
                ),

            'alt_text' =>
                $ad->alt_text,

            'link_url' =>
                $ad->link_url,

            'cta_label' =>
                $ad->cta_label,

            'open_in_new_tab' =>
                (bool) $ad
                    ->open_in_new_tab,
        ];
    }
}