<script setup lang="ts">
import {
    Head,
    Link,
} from '@inertiajs/vue3';

import {
    ArrowRight,
    Camera,
    Cpu,
    Layers3,
    MessageCircle,
    Palette,
    Printer,
    ShieldCheck,
    SignpostBig,
    Sparkles,
} from '@lucide/vue';

import type {
    Component,
} from 'vue';

import PromoSlot
    from '@/components/Public/Promotions/PromoSlot.vue';

import PublicLayout
    from '@/layouts/Public/PublicLayout.vue';

interface Media {
    id: number;
    title: string | null;
    alt_text: string | null;
    width: number | null;
    height: number | null;
    url: string;
}

interface ContentItem {
    title?: string;
    label?: string;
    text?: string;
    icon?: string;
}

interface PageSection {
    id: number;
    section_key: string;
    section_type: string;
    title: string | null;
    subtitle: string | null;
    body: string | null;

    content: {
        items?: ContentItem[];
    };

    settings: {
        cta_label?: string;
        cta_url?: string;
    };

    media: Media | null;
    support_media: Media[];
}

interface WebsitePromotion {
    id: number;
    name: string;
    placement: string;
    platform: string | null;
    image_url: string;
    alt_text: string | null;
    link_url: string | null;
    cta_label: string | null;
    open_in_new_tab: boolean;
}

interface PublicPage {
    slug: string;
    name: string;
    title: string;
    eyebrow: string | null;
    summary: string | null;
    meta_title: string | null;
    meta_description: string | null;
    canonical_url: string | null;
    sections: PageSection[];
}

const props =
    defineProps<{
        page: PublicPage;

        promotions: {
            inline: WebsitePromotion[];
        };
    }>();

const iconMap:
    Record<
        string,
        Component
    > = {
    palette:
        Palette,

    printer:
        Printer,

    camera:
        Camera,

    cpu:
        Cpu,

    layers:
        Layers3,

    message:
        MessageCircle,

    shield:
        ShieldCheck,

    sign:
        SignpostBig,

    sparkles:
        Sparkles,
};

const iconFor =
    (
        key:
            string | undefined,
    ): Component =>
        key
        &&
        iconMap[
            key
        ]
            ? iconMap[
                key
            ]
            : Sparkles;

const itemsFor =
    (
        section:
            PageSection,
    ): ContentItem[] =>
        section
            .content
            ?.items
        ?? [];

const promotionAnchor =
    props.page.slug ===
    'sobre-nosotros'
        ? 'values'
        : 'services';

const showPromotionAfter =
    (
        section:
            PageSection,
    ): boolean =>
        section.section_key ===
        promotionAnchor
        &&
        props.promotions.inline.length >
        0;
</script>

<template>
    <Head
        :title="
            page.meta_title
            ?? `${page.title} | ADN Publicidad`
        "
    >
        <meta
            v-if="
                page.meta_description
            "
            head-key="description"
            name="description"
            :content="
                page.meta_description
            "
        >

        <link
            v-if="
                page.canonical_url
            "
            head-key="canonical"
            rel="canonical"
            :href="
                page.canonical_url
            "
        >
    </Head>

    <PublicLayout>
        <template
            v-for="
                section in page.sections
            "
            :key="
                section.id
            "
        >
            <!-- HERO -->

            <section
                v-if="
                    section.section_type ===
                    'hero'
                "
                class="relative overflow-hidden bg-[#101516] text-white"
            >
                <div
                    class="absolute -left-28 -top-28 h-96 w-96 rounded-full bg-[#0FA7B4]/10 blur-3xl"
                />

                <div
                    class="absolute -right-24 bottom-0 h-80 w-80 rounded-full bg-[#E84657]/10 blur-3xl"
                />

                <div
                    class="relative mx-auto grid max-w-[1500px] gap-10 px-5 py-16 sm:px-7 sm:py-20 lg:grid-cols-[1fr_.82fr] lg:items-center xl:px-10"
                >
                    <div
                        class="max-w-4xl"
                    >
                        <div
                            v-if="
                                section.subtitle
                            "
                            class="inline-flex items-center gap-2 rounded-full border border-[#0FA7B4]/25 bg-[#0FA7B4]/10 px-3 py-2 text-[10px] font-black uppercase tracking-[0.14em] text-[#35c4ce]"
                        >
                            <Sparkles
                                class="h-3.5 w-3.5"
                            />

                            {{
                                section.subtitle
                            }}
                        </div>

                        <h1
                            class="mt-6 text-4xl font-black leading-[.98] tracking-[-0.045em] sm:text-5xl lg:text-6xl"
                        >
                            {{
                                section.title
                                ?? page.title
                            }}
                        </h1>

                        <p
                            v-if="
                                section.body
                            "
                            class="mt-5 max-w-3xl text-base leading-7 text-white/50"
                        >
                            {{
                                section.body
                            }}
                        </p>

                        <Link
                            v-if="
                                section.settings?.cta_label
                                &&
                                section.settings?.cta_url
                            "
                            :href="
                                section.settings.cta_url
                            "
                            class="mt-8 inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-[#0FA7B4] px-6 text-sm font-black text-white transition hover:-translate-y-0.5"
                        >
                            {{
                                section.settings.cta_label
                            }}

                            <ArrowRight
                                class="h-4 w-4"
                            />
                        </Link>
                    </div>

                    <div
                        v-if="
                            section.media
                        "
                        class="relative"
                    >
                        <div
                            class="overflow-hidden rounded-[28px] border border-white/[0.08] bg-white/[0.03] p-2 shadow-2xl"
                        >
                            <img
                                :src="
                                    section.media.url
                                "
                                :alt="
                                    section.media.alt_text
                                    ?? section.title
                                    ?? ''
                                "
                                class="aspect-[4/3] w-full rounded-[22px] object-cover"
                            >
                        </div>

                        <div
                            v-if="
                                section.support_media.length
                            "
                            class="absolute -bottom-5 -left-5 hidden w-36 overflow-hidden rounded-2xl border-4 border-[#101516] shadow-xl sm:block"
                        >
                            <img
                                :src="
                                    section.support_media[0].url
                                "
                                :alt="
                                    section.support_media[0].alt_text
                                    ?? ''
                                "
                                class="aspect-square w-full object-cover"
                            >
                        </div>
                    </div>
                </div>
            </section>

            <!-- HISTORIA -->

            <section
                v-else-if="
                    section.section_type ===
                    'story'
                "
                class="bg-white py-16 sm:py-20"
            >
                <div
                    class="mx-auto grid max-w-[1500px] gap-10 px-5 sm:px-7 lg:grid-cols-[.86fr_1.14fr] lg:items-center xl:px-10"
                >
                    <div>
                        <p
                            v-if="
                                section.subtitle
                            "
                            class="text-[10px] font-black uppercase tracking-[0.15em] text-[#ED7E24]"
                        >
                            {{
                                section.subtitle
                            }}
                        </p>

                        <h2
                            class="mt-3 text-3xl font-black tracking-[-0.04em] text-[#1D1D1B] sm:text-4xl"
                        >
                            {{
                                section.title
                            }}
                        </h2>

                        <p
                            v-if="
                                section.body
                            "
                            class="mt-5 whitespace-pre-line text-sm leading-7 text-black/48 sm:text-base"
                        >
                            {{
                                section.body
                            }}
                        </p>

                        <div
                            v-if="
                                itemsFor(
                                    section,
                                ).length
                            "
                            class="mt-7 grid gap-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2"
                        >
                            <article
                                v-for="
                                    (
                                        item,
                                        index
                                    ) in itemsFor(
                                        section,
                                    )
                                "
                                :key="
                                    index
                                "
                                class="rounded-2xl border border-black/[0.07] bg-[#f7f9f9] p-5"
                            >
                                <component
                                    :is="
                                        iconFor(
                                            item.icon,
                                        )
                                    "
                                    class="h-5 w-5 text-[#0FA7B4]"
                                />

                                <h3
                                    class="mt-4 font-black text-[#1D1D1B]"
                                >
                                    {{
                                        item.title
                                    }}
                                </h3>

                                <p
                                    class="mt-2 text-xs leading-6 text-black/42"
                                >
                                    {{
                                        item.text
                                    }}
                                </p>
                            </article>
                        </div>
                    </div>

                    <div
                        v-if="
                            section.media
                            ||
                            section.support_media.length
                        "
                        class="grid grid-cols-2 gap-3"
                    >
                        <div
                            v-if="
                                section.media
                            "
                            class="col-span-2 overflow-hidden rounded-[26px] bg-[#eef1f1]"
                        >
                            <img
                                :src="
                                    section.media.url
                                "
                                :alt="
                                    section.media.alt_text
                                    ?? ''
                                "
                                class="aspect-[16/9] w-full object-cover"
                            >
                        </div>

                        <div
                            v-for="
                                media in section.support_media.slice(
                                    0,
                                    2,
                                )
                            "
                            :key="
                                media.id
                            "
                            class="overflow-hidden rounded-[20px] bg-[#eef1f1]"
                        >
                            <img
                                :src="
                                    media.url
                                "
                                :alt="
                                    media.alt_text
                                    ?? ''
                                "
                                class="aspect-[4/3] w-full object-cover"
                            >
                        </div>
                    </div>
                </div>
            </section>

            <!-- VALORES / SERVICIOS -->

            <section
                v-else-if="
                    [
                        'values',
                        'services_grid',
                    ].includes(
                        section.section_type,
                    )
                "
                class="bg-[#f3f5f5] py-16 sm:py-20"
            >
                <div
                    class="mx-auto max-w-[1500px] px-5 sm:px-7 xl:px-10"
                >
                    <div
                        class="max-w-3xl"
                    >
                        <p
                            v-if="
                                section.subtitle
                            "
                            class="text-[10px] font-black uppercase tracking-[0.15em] text-[#E84657]"
                        >
                            {{
                                section.subtitle
                            }}
                        </p>

                        <h2
                            class="mt-3 text-3xl font-black tracking-[-0.04em] text-[#1D1D1B] sm:text-4xl"
                        >
                            {{
                                section.title
                            }}
                        </h2>

                        <p
                            v-if="
                                section.body
                            "
                            class="mt-4 text-sm leading-7 text-black/42"
                        >
                            {{
                                section.body
                            }}
                        </p>
                    </div>

                    <!-- Apoyo visual -->

                    <div
                        v-if="
                            section.media
                            ||
                            section.support_media.length
                        "
                        class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4"
                    >
                        <div
                            v-if="
                                section.media
                            "
                            class="overflow-hidden rounded-[22px] bg-white sm:col-span-2"
                        >
                            <img
                                :src="
                                    section.media.url
                                "
                                :alt="
                                    section.media.alt_text
                                    ?? ''
                                "
                                class="aspect-[16/9] h-full w-full object-cover"
                            >
                        </div>

                        <div
                            v-for="
                                media in section.support_media.slice(
                                    0,
                                    2,
                                )
                            "
                            :key="
                                media.id
                            "
                            class="overflow-hidden rounded-[22px] bg-white"
                        >
                            <img
                                :src="
                                    media.url
                                "
                                :alt="
                                    media.alt_text
                                    ?? ''
                                "
                                class="aspect-square h-full w-full object-cover"
                            >
                        </div>
                    </div>

                    <div
                        class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3"
                        :class="
                            section.section_type ===
                            'values'
                                ? 'xl:grid-cols-4'
                                : ''
                        "
                    >
                        <article
                            v-for="
                                (
                                    item,
                                    index
                                ) in itemsFor(
                                    section,
                                )
                            "
                            :key="
                                index
                            "
                            class="group rounded-[24px] border border-black/[0.07] bg-white p-6 shadow-[0_10px_35px_rgba(0,0,0,.025)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_18px_45px_rgba(0,0,0,.06)]"
                        >
                            <span
                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#0FA7B4]/10 text-[#0FA7B4]"
                            >
                                <component
                                    :is="
                                        iconFor(
                                            item.icon,
                                        )
                                    "
                                    class="h-5 w-5"
                                />
                            </span>

                            <p
                                v-if="
                                    item.label
                                "
                                class="mt-5 text-[9px] font-black uppercase tracking-[0.12em] text-[#ED7E24]"
                            >
                                {{
                                    item.label
                                }}
                            </p>

                            <h3
                                class="mt-2 text-lg font-black text-[#1D1D1B]"
                            >
                                {{
                                    item.title
                                }}
                            </h3>

                            <p
                                class="mt-3 text-sm leading-6 text-black/42"
                            >
                                {{
                                    item.text
                                }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <!-- PROCESO -->

            <section
                v-else-if="
                    section.section_type ===
                    'process'
                "
                class="bg-white py-16 sm:py-20"
            >
                <div
                    class="mx-auto max-w-[1500px] px-5 sm:px-7 xl:px-10"
                >
                    <p
                        v-if="
                            section.subtitle
                        "
                        class="text-[10px] font-black uppercase tracking-[0.15em] text-[#ED7E24]"
                    >
                        {{
                            section.subtitle
                        }}
                    </p>

                    <h2
                        class="mt-3 max-w-3xl text-3xl font-black tracking-[-0.04em] text-[#1D1D1B] sm:text-4xl"
                    >
                        {{
                            section.title
                        }}
                    </h2>

                    <div
                        class="mt-9 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
                    >
                        <article
                            v-for="
                                (
                                    item,
                                    index
                                ) in itemsFor(
                                    section,
                                )
                            "
                            :key="
                                index
                            "
                            class="relative overflow-hidden rounded-[22px] border border-black/[0.07] bg-white p-6"
                        >
                            <span
                                class="absolute right-4 top-1 text-6xl font-black text-black/[0.035]"
                            >
                                {{
                                    item.label
                                }}
                            </span>

                            <p
                                class="relative text-[10px] font-black text-[#0FA7B4]"
                            >
                                {{
                                    item.label
                                }}
                            </p>

                            <h3
                                class="relative mt-5 text-lg font-black text-[#1D1D1B]"
                            >
                                {{
                                    item.title
                                }}
                            </h3>

                            <p
                                class="relative mt-3 text-sm leading-6 text-black/42"
                            >
                                {{
                                    item.text
                                }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <!-- CTA -->

            <section
                v-else-if="
                    section.section_type ===
                    'cta'
                "
                class="bg-[#f3f5f5] px-5 py-16 sm:px-7 sm:py-20 xl:px-10"
            >
                <div
                    class="relative mx-auto max-w-[1380px] overflow-hidden rounded-[30px] bg-[#101516] px-6 py-10 text-white sm:px-10 lg:px-14"
                >
                    <div
                        class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-[#0FA7B4]/15 blur-3xl"
                    />

                    <div
                        class="relative flex flex-col justify-between gap-8 lg:flex-row lg:items-end"
                    >
                        <div
                            class="max-w-3xl"
                        >
                            <p
                                v-if="
                                    section.subtitle
                                "
                                class="text-[10px] font-black uppercase tracking-[0.16em] text-[#35c4ce]"
                            >
                                {{
                                    section.subtitle
                                }}
                            </p>

                            <h2
                                class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl"
                            >
                                {{
                                    section.title
                                }}
                            </h2>

                            <p
                                v-if="
                                    section.body
                                "
                                class="mt-4 text-sm leading-7 text-white/45"
                            >
                                {{
                                    section.body
                                }}
                            </p>
                        </div>

                        <Link
                            v-if="
                                section.settings?.cta_label
                                &&
                                section.settings?.cta_url
                            "
                            :href="
                                section.settings.cta_url
                            "
                            class="inline-flex min-h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#0FA7B4] px-6 text-sm font-black text-white"
                        >
                            {{
                                section.settings.cta_label
                            }}

                            <ArrowRight
                                class="h-4 w-4"
                            />
                        </Link>
                    </div>
                </div>
            </section>

            <!-- PROMOTION -->

            <section
                v-if="
                    showPromotionAfter(
                        section,
                    )
                "
                class="bg-white px-5 py-10 sm:px-7 sm:py-12 xl:px-10"
            >
                <div
                    class="mx-auto max-w-[970px]"
                >
                    <div
                        class="mb-3 flex items-center justify-between px-1"
                    >
                        <p
                            class="text-[8px] font-black uppercase tracking-[0.15em] text-black/25"
                        >
                            Conecta con ADN
                        </p>

                        <span
                            class="text-[8px] font-bold text-black/15"
                        >
                            Redes · Novedades
                        </span>
                    </div>

                    <PromoSlot
                        :ads="
                            promotions.inline
                        "
                        aspect-class="aspect-[970/250]"
                        :interval-ms="
                            9000
                        "
                    />
                </div>
            </section>
        </template>
    </PublicLayout>
</template>