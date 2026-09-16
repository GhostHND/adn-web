<script setup lang="ts">
import {
    Head,
    Link,
} from '@inertiajs/vue3';

import {
    ArrowRight,
    Camera,
    Check,
    Cpu,
    ImageIcon,
    Layers3,
    MessageCircle,
    Palette,
    Printer,
    ShieldCheck,
    SignpostBig,
    Sparkles,
} from '@lucide/vue';

import {
    computed,
} from 'vue';

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

interface HomePage {
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

interface Product {
    id: number;
    code: string;
    slug: string;
    name: string;
    short_description: string | null;
    featured: boolean;
    price_visible: boolean;
    price_from: boolean;
    reference_price: string | null;
    image: Media | null;

    category: {
        id: number;
        name: string;
        slug: string;
    } | null;
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

const props =
    defineProps<{
        page: HomePage;
        products: Product[];

        promotions: {
            siteWide: WebsitePromotion[];
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

    sign:
        SignpostBig,

    sparkles:
        Sparkles,

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
};

const iconFor =
    (
        key:
            string | undefined,
    ): Component => {
        if (
            key
            &&
            iconMap[
                key
            ]
        ) {
            return iconMap[
                key
            ];
        }

        return Sparkles;
    };

const sectionByKey =
    (
        key:
            string,
    ): PageSection | null => {
        return props.page.sections.find(
            (
                section,
            ) =>
                section.section_key ===
                key,
        )
        ?? null;
    };

const hero =
    computed(
        () =>
            sectionByKey(
                'hero',
            ),
    );

const services =
    computed(
        () =>
            sectionByKey(
                'services',
            ),
    );

const catalog =
    computed(
        () =>
            sectionByKey(
                'catalog',
            ),
    );

const portfolio =
    computed(
        () =>
            sectionByKey(
                'portfolio',
            ),
    );

const cta =
    computed(
        () =>
            sectionByKey(
                'cta',
            ),
    );

const serviceItems =
    computed(
        (): ContentItem[] =>
            services.value
                ?.content
                ?.items
            ?? [],
    );

const portfolioImages =
    computed(
        (): Media[] => {
            if (
                !portfolio.value
            ) {
                return [];
            }

            const images:
                Media[] =
                [];

            const usedIds =
                new Set<number>();

            if (
                portfolio.value.media
            ) {
                images.push(
                    portfolio.value.media,
                );

                usedIds.add(
                    portfolio.value.media.id,
                );
            }

            portfolio.value.support_media.forEach(
                (
                    media,
                ) => {
                    if (
                        usedIds.has(
                            media.id,
                        )
                    ) {
                        return;
                    }

                    usedIds.add(
                        media.id,
                    );

                    images.push(
                        media,
                    );
                },
            );

            return images.slice(
                0,
                5,
            );
        },
    );

const formatPrice =
    (
        value:
            string | null,
    ): string => {
        if (
            value ===
            null
        ) {
            return '';
        }

        return new Intl.NumberFormat(
            'es-HN',
            {
                style:
                    'currency',

                currency:
                    'HNL',
            },
        ).format(
            Number(
                value,
            ),
        );
    };
</script>

<template>
    <Head
        :title="
            page.meta_title
            ?? 'ADN Publicidad'
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
        <!-- ====================================================== -->
        <!-- HERO -->
        <!-- ====================================================== -->

        <section
            v-if="
                hero
            "
            class="relative overflow-hidden bg-[#101516] text-white"
        >
            <div
                class="pointer-events-none absolute -left-32 -top-32 h-[430px] w-[430px] rounded-full bg-[#0FA7B4]/10 blur-3xl"
            />

            <div
                class="pointer-events-none absolute -right-20 bottom-0 h-[380px] w-[380px] rounded-full bg-[#E84657]/10 blur-3xl"
            />

            <div
                class="relative mx-auto grid max-w-[1500px] gap-10 px-5 py-16 sm:px-7 sm:py-20 lg:min-h-[620px] lg:grid-cols-[1.03fr_.97fr] lg:items-center xl:px-10"
            >
                <!-- Hero texto -->

                <div
                    class="max-w-3xl"
                >
                    <div
                        v-if="
                            hero.subtitle
                        "
                        class="inline-flex items-center gap-2 rounded-full border border-[#0FA7B4]/25 bg-[#0FA7B4]/10 px-3 py-2 text-[9px] font-black uppercase tracking-[0.15em] text-[#35c4ce] sm:text-[10px]"
                    >
                        <Sparkles
                            class="h-3.5 w-3.5"
                        />

                        {{
                            hero.subtitle
                        }}
                    </div>

                    <h1
                        class="mt-6 text-[clamp(2.8rem,8vw,5.3rem)] font-black leading-[.91] tracking-[-0.055em]"
                    >
                        {{
                            hero.title
                            ?? page.title
                        }}
                    </h1>

                    <p
                        v-if="
                            hero.body
                        "
                        class="mt-6 max-w-2xl text-sm leading-7 text-white/50 sm:text-base lg:text-lg"
                    >
                        {{
                            hero.body
                        }}
                    </p>

                    <div
                        class="mt-8 flex flex-col gap-3 sm:flex-row"
                    >
                        <Link
                            v-if="
                                hero.settings?.cta_label
                                &&
                                hero.settings?.cta_url
                            "
                            :href="
                                hero.settings.cta_url
                            "
                            class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-[#0FA7B4] px-6 text-sm font-black text-white shadow-[0_12px_30px_rgba(15,167,180,.18)] transition duration-300 hover:-translate-y-0.5 hover:bg-[#1199a5]"
                        >
                            {{
                                hero.settings.cta_label
                            }}

                            <ArrowRight
                                class="h-4 w-4"
                            />
                        </Link>

                        <Link
                            href="/sobre-nosotros"
                            class="inline-flex min-h-12 items-center justify-center rounded-xl border border-white/[0.09] bg-white/[0.035] px-6 text-sm font-black text-white/65 transition hover:bg-white/[0.06] hover:text-white"
                        >
                            Conocer ADN
                        </Link>
                    </div>

                    <div
                        class="mt-9 grid max-w-xl grid-cols-3 gap-3"
                    >
                        <div
                            class="rounded-2xl border border-white/[0.06] bg-white/[0.025] px-3 py-4 sm:px-4"
                        >
                            <p
                                class="text-[8px] font-black uppercase tracking-[0.12em] text-[#35c4ce]"
                            >
                                Diseño
                            </p>

                            <p
                                class="mt-1 text-[10px] font-bold leading-4 text-white/35 sm:text-xs"
                            >
                                Ideas que comunican
                            </p>
                        </div>

                        <div
                            class="rounded-2xl border border-white/[0.06] bg-white/[0.025] px-3 py-4 sm:px-4"
                        >
                            <p
                                class="text-[8px] font-black uppercase tracking-[0.12em] text-[#ED7E24]"
                            >
                                Producción
                            </p>

                            <p
                                class="mt-1 text-[10px] font-bold leading-4 text-white/35 sm:text-xs"
                            >
                                Resultados reales
                            </p>
                        </div>

                        <div
                            class="rounded-2xl border border-white/[0.06] bg-white/[0.025] px-3 py-4 sm:px-4"
                        >
                            <p
                                class="text-[8px] font-black uppercase tracking-[0.12em] text-[#E84657]"
                            >
                                Tecnología
                            </p>

                            <p
                                class="mt-1 text-[10px] font-bold leading-4 text-white/35 sm:text-xs"
                            >
                                Nuevas soluciones
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ================================================== -->
                <!-- HERO VISUAL -->
                <!-- La estructura SIEMPRE conserva los mismos 3 cuadros -->
                <!-- ================================================== -->

                <div
                    class="mx-auto w-full max-w-[620px]"
                >
                    <div
                        class="grid grid-cols-2 gap-3"
                    >
                        <!-- CUADRO PRINCIPAL -->

                        <div
                            class="relative col-span-2 aspect-[16/9] overflow-hidden rounded-[28px] border border-white/[0.08] bg-[#122326] shadow-[0_32px_80px_rgba(0,0,0,.25)]"
                        >
                            <!-- Imagen principal como fondo -->

                            <img
                                v-if="
                                    hero.media
                                "
                                :src="
                                    hero.media.url
                                "
                                :alt="
                                    hero.media.alt_text
                                    ?? hero.title
                                    ?? 'ADN Publicidad'
                                "
                                class="absolute inset-0 h-full w-full object-cover"
                            >

                            <!-- Fondo fallback -->

                            <div
                                v-else
                                class="absolute inset-0 bg-[linear-gradient(135deg,#123033_0%,#111718_55%,#24191d_100%)]"
                            />

                            <!-- Oscurecimiento de la imagen -->

                            <div
                                class="absolute inset-0 bg-[linear-gradient(180deg,rgba(6,13,14,.08)_0%,rgba(6,13,14,.28)_42%,rgba(6,13,14,.86)_100%)]"
                            />

                            <!-- Tinte ADN -->

                            <div
                                class="absolute inset-0 bg-[linear-gradient(120deg,rgba(15,167,180,.12)_0%,transparent_45%,rgba(232,70,87,.07)_100%)]"
                            />

                            <!-- Contenido que SIEMPRE permanece -->

                            <div
                                class="absolute inset-x-0 bottom-0 z-10 p-6 sm:p-7 lg:p-8"
                            >
                                <p
                                    class="text-[9px] font-black uppercase tracking-[0.16em] text-[#35c4ce] sm:text-[10px]"
                                >
                                    ADN Publicidad
                                </p>

                                <p
                                    class="mt-3 max-w-[430px] text-xl font-black leading-[1.08] tracking-[-0.035em] text-white sm:text-2xl lg:text-[1.65rem]"
                                >
                                    Tu idea también puede convertirse
                                    en algo que destaque.
                                </p>
                            </div>
                        </div>

                        <!-- CUADRO DE APOYO 1 -->

                        <div
                            class="relative aspect-square overflow-hidden rounded-[22px] border border-white/[0.08] bg-[#10292b]"
                        >
                            <img
                                v-if="
                                    hero.support_media[0]
                                "
                                :src="
                                    hero.support_media[0].url
                                "
                                :alt="
                                    hero.support_media[0].alt_text
                                    ?? 'Trabajo realizado por ADN Publicidad'
                                "
                                class="absolute inset-0 h-full w-full object-cover transition duration-500 hover:scale-[1.03]"
                            >

                            <div
                                v-else
                                class="absolute inset-0 bg-[linear-gradient(145deg,#123437_0%,#102526_100%)]"
                            />

                            <div
                                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/10 to-transparent"
                            />
                        </div>

                        <!-- CUADRO DE APOYO 2 -->

                        <div
                            class="relative aspect-square overflow-hidden rounded-[22px] border border-white/[0.08] bg-[#2a2117]"
                        >
                            <img
                                v-if="
                                    hero.support_media[1]
                                "
                                :src="
                                    hero.support_media[1].url
                                "
                                :alt="
                                    hero.support_media[1].alt_text
                                    ?? 'Trabajo realizado por ADN Publicidad'
                                "
                                class="absolute inset-0 h-full w-full object-cover transition duration-500 hover:scale-[1.03]"
                            >

                            <div
                                v-else
                                class="absolute inset-0 bg-[linear-gradient(145deg,#322518_0%,#241d16_100%)]"
                            />

                            <div
                                class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/10 to-transparent"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ====================================================== -->
        <!-- SERVICIOS -->
        <!-- ====================================================== -->

        <section
            v-if="
                services
            "
            class="bg-[#f3f5f5] py-16 sm:py-20 lg:py-24"
        >
            <div
                class="mx-auto max-w-[1500px] px-5 sm:px-7 xl:px-10"
            >
                <div
                    class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end"
                >
                    <div
                        class="max-w-3xl"
                    >
                        <p
                            v-if="
                                services.subtitle
                            "
                            class="text-[10px] font-black uppercase tracking-[0.16em] text-[#ED7E24]"
                        >
                            {{
                                services.subtitle
                            }}
                        </p>

                        <h2
                            class="mt-3 text-3xl font-black leading-[1.02] tracking-[-0.045em] text-[#1D1D1B] sm:text-4xl lg:text-5xl"
                        >
                            {{
                                services.title
                            }}
                        </h2>

                        <p
                            v-if="
                                services.body
                            "
                            class="mt-4 max-w-2xl text-sm leading-7 text-black/42 sm:text-base"
                        >
                            {{
                                services.body
                            }}
                        </p>
                    </div>

                    <Link
                        v-if="
                            services.settings?.cta_label
                            &&
                            services.settings?.cta_url
                        "
                        :href="
                            services.settings.cta_url
                        "
                        class="inline-flex min-h-11 shrink-0 items-center gap-2 self-start rounded-xl border border-black/[0.08] bg-white px-5 text-xs font-black text-black/50 shadow-sm transition hover:border-[#0FA7B4]/25 hover:text-[#0FA7B4] lg:self-auto"
                    >
                        {{
                            services.settings.cta_label
                        }}

                        <ArrowRight
                            class="h-4 w-4"
                        />
                    </Link>
                </div>

                <div
                    v-if="
                        services.media
                        ||
                        services.support_media.length
                    "
                    class="mt-9 grid gap-3 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <div
                        v-if="
                            services.media
                        "
                        class="overflow-hidden rounded-[26px] bg-white sm:col-span-2"
                    >
                        <img
                            :src="
                                services.media.url
                            "
                            :alt="
                                services.media.alt_text
                                ?? services.title
                                ?? ''
                            "
                            class="aspect-[16/9] h-full w-full object-cover"
                        >
                    </div>

                    <div
                        v-for="
                            media in services.support_media.slice(
                                0,
                                2,
                            )
                        "
                        :key="
                            media.id
                        "
                        class="overflow-hidden rounded-[26px] bg-white"
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
                    class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <article
                        v-for="
                            (
                                item,
                                index
                            ) in serviceItems
                        "
                        :key="
                            `${item.title}-${index}`
                        "
                        class="group rounded-[24px] border border-black/[0.07] bg-white p-6 shadow-[0_10px_35px_rgba(0,0,0,.025)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_48px_rgba(0,0,0,.065)]"
                    >
                        <span
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#0FA7B4]/10 text-[#0FA7B4] transition duration-300 group-hover:bg-[#0FA7B4] group-hover:text-white"
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
                            class="mt-5 text-[9px] font-black uppercase tracking-[0.12em] text-[#E84657]"
                        >
                            {{
                                item.label
                            }}
                        </p>

                        <h3
                            class="mt-2 text-lg font-black tracking-[-0.025em] text-[#1D1D1B]"
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

        <!-- ====================================================== -->
        <!-- CATÁLOGO -->
        <!-- ====================================================== -->

        <section
            v-if="
                catalog
            "
            class="bg-white py-16 sm:py-20 lg:py-24"
        >
            <div
                class="mx-auto max-w-[1500px] px-5 sm:px-7 xl:px-10"
            >
                <div
                    class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end"
                >
                    <div
                        class="max-w-3xl"
                    >
                        <p
                            v-if="
                                catalog.subtitle
                            "
                            class="text-[10px] font-black uppercase tracking-[0.16em] text-[#E84657]"
                        >
                            {{
                                catalog.subtitle
                            }}
                        </p>

                        <h2
                            class="mt-3 text-3xl font-black leading-[1.02] tracking-[-0.045em] text-[#1D1D1B] sm:text-4xl lg:text-5xl"
                        >
                            {{
                                catalog.title
                            }}
                        </h2>

                        <p
                            v-if="
                                catalog.body
                            "
                            class="mt-4 max-w-2xl text-sm leading-7 text-black/42"
                        >
                            {{
                                catalog.body
                            }}
                        </p>
                    </div>

                    <Link
                        :href="
                            catalog.settings?.cta_url
                            ?? '/catalogo'
                        "
                        class="inline-flex min-h-11 shrink-0 items-center gap-2 self-start rounded-xl bg-[#101516] px-5 text-xs font-black text-white transition hover:bg-[#0FA7B4] lg:self-auto"
                    >
                        {{
                            catalog.settings?.cta_label
                            ?? 'Ver catálogo'
                        }}

                        <ArrowRight
                            class="h-4 w-4"
                        />
                    </Link>
                </div>

                <div
                    v-if="
                        products.length
                    "
                    class="mt-9 grid gap-5 sm:grid-cols-2 lg:grid-cols-3"
                >
                    <Link
                        v-for="
                            product in products
                        "
                        :key="
                            product.id
                        "
                        :href="
                            `/catalogo/${product.slug}`
                        "
                        class="group overflow-hidden rounded-[24px] border border-black/[0.07] bg-white shadow-[0_10px_35px_rgba(0,0,0,.035)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_50px_rgba(0,0,0,.08)]"
                    >
                        <div
                            class="relative aspect-[4/3] overflow-hidden bg-[#eef1f1]"
                        >
                            <img
                                v-if="
                                    product.image
                                "
                                :src="
                                    product.image.url
                                "
                                :alt="
                                    product.image.alt_text
                                    ?? product.name
                                "
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]"
                            >

                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center"
                            >
                                <ImageIcon
                                    class="h-10 w-10 text-black/10"
                                />
                            </div>

                            <span
                                v-if="
                                    product.featured
                                "
                                class="absolute left-4 top-4 rounded-full bg-[#F5C000] px-3 py-1.5 text-[8px] font-black uppercase tracking-[0.1em] text-[#1D1D1B]"
                            >
                                Destacado
                            </span>
                        </div>

                        <div
                            class="p-5"
                        >
                            <p
                                class="text-[9px] font-black uppercase tracking-[0.12em] text-[#E84657]"
                            >
                                {{
                                    product.category?.name
                                    ?? product.code
                                }}
                            </p>

                            <h3
                                class="mt-2 text-lg font-black tracking-[-0.025em] text-[#1D1D1B]"
                            >
                                {{
                                    product.name
                                }}
                            </h3>

                            <p
                                v-if="
                                    product.short_description
                                "
                                class="mt-2 line-clamp-2 min-h-10 text-xs leading-5 text-black/40"
                            >
                                {{
                                    product.short_description
                                }}
                            </p>

                            <div
                                class="mt-5 flex items-end justify-between gap-4"
                            >
                                <div>
                                    <template
                                        v-if="
                                            product.price_visible
                                            &&
                                            product.reference_price
                                        "
                                    >
                                        <p
                                            v-if="
                                                product.price_from
                                            "
                                            class="text-[9px] font-black uppercase text-black/25"
                                        >
                                            Desde
                                        </p>

                                        <p
                                            class="text-base font-black text-[#0FA7B4]"
                                        >
                                            {{
                                                formatPrice(
                                                    product.reference_price,
                                                )
                                            }}
                                        </p>
                                    </template>

                                    <p
                                        v-else
                                        class="text-[9px] font-black uppercase tracking-[0.08em] text-black/25"
                                    >
                                        Solicitar cotización
                                    </p>
                                </div>

                                <span
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#101516] text-white transition group-hover:bg-[#0FA7B4]"
                                >
                                    <ArrowRight
                                        class="h-4 w-4"
                                    />
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <div
                    v-else
                    class="mt-9 rounded-[26px] border border-dashed border-black/[0.08] bg-[#f7f9f9] px-6 py-12 text-center"
                >
                    <p
                        class="text-sm font-black text-black/45"
                    >
                        Estamos preparando nuestros productos para mostrarlos aquí.
                    </p>

                    <Link
                        href="/catalogo"
                        class="mt-4 inline-flex items-center gap-2 text-xs font-black text-[#0FA7B4]"
                    >
                        Explorar catálogo

                        <ArrowRight
                            class="h-4 w-4"
                        />
                    </Link>
                </div>
            </div>
        </section>

        <!-- ====================================================== -->
        <!-- PROMOTION -->
        <!-- ====================================================== -->

        <section
            v-if="
                promotions.siteWide.length
            "
            class="bg-[#f3f5f5] px-5 py-10 sm:px-7 sm:py-14 xl:px-10"
        >
            <div
                class="mx-auto max-w-[970px]"
            >
                <div
                    class="mb-3 flex items-center justify-between gap-4 px-1"
                >
                    <div
                        class="flex items-center gap-2"
                    >
                        <span
                            class="h-1.5 w-1.5 rounded-full bg-[#0FA7B4]"
                        />

                        <p
                            class="text-[8px] font-black uppercase tracking-[0.16em] text-black/25 sm:text-[9px]"
                        >
                            Conecta con ADN
                        </p>
                    </div>

                    <p
                        class="text-[8px] font-bold uppercase tracking-[0.1em] text-black/15"
                    >
                        Redes · Campañas · Novedades
                    </p>
                </div>

                <PromoSlot
                    :ads="
                        promotions.siteWide
                    "
                    aspect-class="aspect-[970/250]"
                    :interval-ms="
                        9000
                    "
                />
            </div>
        </section>

        <!-- ====================================================== -->
        <!-- MUESTRA VISUAL -->
        <!-- ====================================================== -->

        <section
            v-if="
                portfolio
            "
            class="bg-white py-16 sm:py-20 lg:py-24"
        >
            <div
                class="mx-auto max-w-[1500px] px-5 sm:px-7 xl:px-10"
            >
                <div
                    class="grid gap-10 lg:grid-cols-[.72fr_1.28fr] lg:items-center"
                >
                    <div>
                        <p
                            v-if="
                                portfolio.subtitle
                            "
                            class="text-[10px] font-black uppercase tracking-[0.16em] text-[#ED7E24]"
                        >
                            {{
                                portfolio.subtitle
                            }}
                        </p>

                        <h2
                            class="mt-3 text-3xl font-black leading-[1.02] tracking-[-0.045em] text-[#1D1D1B] sm:text-4xl lg:text-5xl"
                        >
                            {{
                                portfolio.title
                            }}
                        </h2>

                        <p
                            v-if="
                                portfolio.body
                            "
                            class="mt-5 max-w-xl text-sm leading-7 text-black/44 sm:text-base"
                        >
                            {{
                                portfolio.body
                            }}
                        </p>

                        <div
                            class="mt-7 flex items-start gap-3 rounded-2xl border border-[#0FA7B4]/15 bg-[#0FA7B4]/[0.045] p-4"
                        >
                            <span
                                class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#0FA7B4]/10 text-[#0FA7B4]"
                            >
                                <Check
                                    class="h-4 w-4"
                                />
                            </span>

                            <p
                                class="text-xs leading-6 text-black/42"
                            >
                                Estas imágenes son únicamente una muestra visual.
                                Cada proyecto se desarrolla según las necesidades del cliente.
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="
                            portfolioImages.length
                        "
                        class="grid min-w-0 grid-cols-2 gap-3 sm:grid-cols-3"
                    >
                        <div
                            v-if="
                                portfolioImages[0]
                            "
                            class="col-span-2 row-span-2 overflow-hidden rounded-[26px] bg-[#eef1f1]"
                        >
                            <img
                                :src="
                                    portfolioImages[0].url
                                "
                                :alt="
                                    portfolioImages[0].alt_text
                                    ?? 'Trabajo realizado por ADN Publicidad'
                                "
                                class="aspect-[4/3] h-full w-full object-cover"
                            >
                        </div>

                        <div
                            v-for="
                                media in portfolioImages.slice(
                                    1,
                                    5,
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
                                    ?? 'Trabajo realizado por ADN Publicidad'
                                "
                                class="aspect-square h-full w-full object-cover"
                            >
                        </div>
                    </div>

                    <div
                        v-else
                        class="grid grid-cols-2 gap-3 sm:grid-cols-3"
                    >
                        <div
                            class="col-span-2 aspect-[4/3] rounded-[26px] bg-[#eef1f1]"
                        />

                        <div
                            class="aspect-square rounded-[20px] bg-[#f3f5f5]"
                        />

                        <div
                            class="aspect-square rounded-[20px] bg-[#f3f5f5]"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- ====================================================== -->
        <!-- CTA -->
        <!-- ====================================================== -->

        <section
            v-if="
                cta
            "
            class="bg-[#f3f5f5] px-5 py-16 sm:px-7 sm:py-20 xl:px-10"
        >
            <div
                class="relative mx-auto max-w-[1380px] overflow-hidden rounded-[30px] bg-[#101516] px-6 py-10 text-white sm:px-10 sm:py-12 lg:px-14 lg:py-14"
            >
                <div
                    class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-[#0FA7B4]/15 blur-3xl"
                />

                <div
                    class="absolute -bottom-28 left-1/3 h-64 w-64 rounded-full bg-[#E84657]/10 blur-3xl"
                />

                <div
                    class="relative flex flex-col justify-between gap-8 lg:flex-row lg:items-end"
                >
                    <div
                        class="max-w-3xl"
                    >
                        <p
                            v-if="
                                cta.subtitle
                            "
                            class="text-[10px] font-black uppercase tracking-[0.16em] text-[#35c4ce]"
                        >
                            {{
                                cta.subtitle
                            }}
                        </p>

                        <h2
                            class="mt-3 text-3xl font-black leading-[1.02] tracking-[-0.045em] sm:text-4xl lg:text-5xl"
                        >
                            {{
                                cta.title
                            }}
                        </h2>

                        <p
                            v-if="
                                cta.body
                            "
                            class="mt-4 max-w-2xl text-sm leading-7 text-white/45 sm:text-base"
                        >
                            {{
                                cta.body
                            }}
                        </p>
                    </div>

                    <Link
                        v-if="
                            cta.settings?.cta_label
                            &&
                            cta.settings?.cta_url
                        "
                        :href="
                            cta.settings.cta_url
                        "
                        class="inline-flex min-h-12 shrink-0 items-center justify-center gap-2 rounded-xl bg-[#0FA7B4] px-6 text-sm font-black text-white shadow-[0_12px_30px_rgba(15,167,180,.18)] transition hover:-translate-y-0.5"
                    >
                        {{
                            cta.settings.cta_label
                        }}

                        <ArrowRight
                            class="h-4 w-4"
                        />
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>