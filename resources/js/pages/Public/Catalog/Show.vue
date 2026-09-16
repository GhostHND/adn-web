<script setup lang="ts">
import {
    Head,
    Link,
} from '@inertiajs/vue3';

import {
    ArrowLeft,
    ArrowRight,
    Check,
    ChevronRight,
    ImageIcon,
    ShoppingBag,
} from '@lucide/vue';

import {
    computed,
    ref,
} from 'vue';

import QuoteModal
    from '@/components/Public/Catalog/QuoteModal.vue';

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

interface FieldOption {
    id: number;
    label: string;
    value: string;
    extra_price: string | null;
}

interface ProductField {
    id: number;
    field_key: string;
    label: string;
    field_type: string;
    placeholder: string | null;
    help_text: string | null;
    unit: string | null;
    required: boolean;
    min_value: string | null;
    max_value: string | null;
    step: string | null;
    default_value: string | null;
    options: FieldOption[];
}

interface Product {
    id: number;
    code: string;
    slug: string;
    name: string;
    short_description: string | null;
    description: string | null;
    features: string[];
    featured: boolean;
    price_visible: boolean;
    price_from: boolean;
    reference_price: string | null;
    price_note: string | null;
    quote_mode: string;
    measurement_unit: string | null;
    meta_title: string | null;
    meta_description: string | null;

    category: {
        id: number;
        name: string;
        slug: string;
    } | null;

    main_media: Media | null;

    images: {
        id: number;
        media_id: number;
        alt_text: string | null;
        media: Media;
    }[];

    fields: ProductField[];
}

interface WebsiteAd {
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

interface Promotions {
    rectangle: WebsiteAd[];
}

const props =
    defineProps<{
        product: Product;
        ads: Promotions;
    }>();

const quoteModalOpen =
    ref(
        false,
    );

const activeImage =
    ref<Media | null>(
        props.product.main_media
        ?? props.product.images[0]?.media
        ?? null,
    );

const allImages =
    computed(
        () => {
            const images:
                Media[] =
                [];

            const ids =
                new Set<number>();

            if (
                props.product.main_media
            ) {
                images.push(
                    props.product.main_media,
                );

                ids.add(
                    props.product.main_media.id,
                );
            }

            props.product.images.forEach(
                (
                    image,
                ) => {
                    if (
                        ids.has(
                            image.media.id,
                        )
                    ) {
                        return;
                    }

                    ids.add(
                        image.media.id,
                    );

                    images.push(
                        image.media,
                    );
                },
            );

            return images;
        },
    );

const formatPrice =
    (
        value:
            string | null,
    ) => {
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
            product.meta_title
            ?? `${product.name} | ADN Publicidad`
        "
    >
        <meta
            v-if="product.meta_description"
            head-key="description"
            name="description"
            :content="product.meta_description"
        >
    </Head>

    <PublicLayout>
        <!-- Breadcrumb -->

        <section
            class="border-b border-black/[0.06] bg-white"
        >
            <div
                class="mx-auto flex max-w-[1500px] items-center gap-2 overflow-x-auto px-5 py-4 text-xs font-bold text-black/30 sm:px-7 xl:px-10"
            >
                <Link
                    href="/catalogo"
                    class="shrink-0 transition hover:text-[#0FA7B4]"
                >
                    Catálogo
                </Link>

                <ChevronRight
                    class="h-3.5 w-3.5 shrink-0"
                />

                <Link
                    v-if="product.category"
                    :href="`/catalogo?categoria=${encodeURIComponent(product.category.slug)}`"
                    class="shrink-0 transition hover:text-[#0FA7B4]"
                >
                    {{
                        product.category.name
                    }}
                </Link>

                <ChevronRight
                    v-if="product.category"
                    class="h-3.5 w-3.5 shrink-0"
                />

                <span
                    class="truncate text-black/60"
                >
                    {{
                        product.name
                    }}
                </span>
            </div>
        </section>

        <!-- Ficha -->

        <section
            class="mx-auto max-w-[1500px] px-5 py-8 sm:px-7 lg:py-12 xl:px-10"
        >
            <Link
                href="/catalogo"
                class="mb-7 inline-flex items-center gap-2 text-xs font-black text-black/35 transition hover:text-[#0FA7B4]"
            >
                <ArrowLeft
                    class="h-4 w-4"
                />

                Volver al catálogo
            </Link>

            <div
                class="grid min-w-0 gap-8 lg:grid-cols-[minmax(0,1.05fr)_minmax(380px,.95fr)] lg:gap-10 xl:gap-14"
            >
                <!-- Fotografías -->

                <div
                    class="min-w-0"
                >
                    <div
                        class="relative overflow-hidden rounded-[24px] border border-black/[0.07] bg-[#eef1f1] shadow-[0_20px_60px_rgba(22,34,35,.06)] sm:rounded-[28px]"
                    >
                        <img
                            v-if="activeImage"
                            :src="
                                activeImage.url
                            "
                            :alt="
                                activeImage.alt_text
                                ?? product.name
                            "
                            class="aspect-[4/3] w-full object-contain"
                        >

                        <div
                            v-else
                            class="flex aspect-[4/3] items-center justify-center"
                        >
                            <ImageIcon
                                class="h-14 w-14 text-black/10"
                            />
                        </div>

                        <span
                            v-if="product.featured"
                            class="absolute left-4 top-4 rounded-full bg-[#F5C000] px-3 py-2 text-[8px] font-black uppercase tracking-[0.1em] shadow-lg sm:left-5 sm:top-5 sm:text-[9px]"
                        >
                            Destacado
                        </span>
                    </div>

                    <div
                        v-if="
                            allImages.length >
                            1
                        "
                        class="mt-3 flex gap-3 overflow-x-auto pb-2 sm:grid sm:grid-cols-5 sm:overflow-visible"
                    >
                        <button
                            v-for="
                                image in allImages
                            "
                            :key="
                                image.id
                            "
                            type="button"
                            class="w-[78px] shrink-0 overflow-hidden rounded-xl border bg-[#eef1f1] transition duration-300 sm:w-auto"
                            :class="
                                activeImage?.id ===
                                image.id
                                    ? 'border-[#0FA7B4] ring-2 ring-[#0FA7B4]/15'
                                    : 'border-black/[0.07] hover:-translate-y-0.5 hover:border-[#0FA7B4]/40'
                            "
                            @click="
                                activeImage =
                                    image
                            "
                        >
                            <img
                                :src="
                                    image.url
                                "
                                :alt="
                                    image.alt_text
                                    ?? product.name
                                "
                                class="aspect-square h-full w-full object-cover"
                            >
                        </button>
                    </div>
                </div>

                <!-- Información -->

                <div
                    class="flex min-w-0 flex-col justify-center"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.15em] text-[#E84657] sm:text-[10px]"
                    >
                        {{
                            product.category?.name
                            ?? product.code
                        }}
                    </p>

                    <h1
                        class="mt-3 text-[clamp(2.25rem,7vw,3.75rem)] font-black leading-[.95] tracking-[-0.05em] text-[#1D1D1B]"
                    >
                        {{
                            product.name
                        }}
                    </h1>

                    <p
                        v-if="
                            product.short_description
                        "
                        class="mt-5 max-w-xl text-sm leading-7 text-black/45 sm:text-base"
                    >
                        {{
                            product.short_description
                        }}
                    </p>

                    <div
                        v-if="
                            product.price_visible
                        "
                        class="mt-6 rounded-2xl border border-[#0FA7B4]/20 bg-[#0FA7B4]/[0.06] p-5"
                    >
                        <p
                            v-if="
                                product.price_from
                            "
                            class="text-[9px] font-black uppercase tracking-[0.12em] text-black/30"
                        >
                            Desde
                        </p>

                        <p
                            class="mt-1 text-3xl font-black text-[#0FA7B4]"
                        >
                            {{
                                formatPrice(
                                    product.reference_price,
                                )
                            }}
                        </p>

                        <p
                            v-if="
                                product.price_note
                            "
                            class="mt-1 text-xs text-black/35"
                        >
                            {{
                                product.price_note
                            }}
                        </p>
                    </div>

                    <!-- Características -->

                    <div
                        v-if="
                            product.features.length
                        "
                        class="mt-7"
                    >
                        <p
                            class="text-xs font-black uppercase tracking-[0.1em] text-black/35"
                        >
                            Características
                        </p>

                        <div
                            class="mt-4 grid gap-3"
                        >
                            <div
                                v-for="
                                    feature in product.features
                                "
                                :key="
                                    feature
                                "
                                class="flex items-start gap-3"
                            >
                                <span
                                    class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#0FA7B4]/10 text-[#0FA7B4]"
                                >
                                    <Check
                                        class="h-3 w-3"
                                    />
                                </span>

                                <p
                                    class="text-sm leading-6 text-black/50"
                                >
                                    {{
                                        feature
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Cotizar -->

                    <button
                        type="button"
                        class="group relative mt-8 w-full overflow-hidden rounded-[18px] bg-[#101516] p-[1px] text-left shadow-[0_18px_45px_rgba(15,21,22,.16)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_26px_60px_rgba(15,167,180,.18)]"
                        @click="
                            quoteModalOpen =
                                true
                        "
                    >
                        <span
                            class="absolute inset-0 bg-[linear-gradient(110deg,#0FA7B4,#0FA7B4_38%,#ED7E24_68%,#E84657)] opacity-70"
                        />

                        <span
                            class="relative flex min-h-[78px] items-center justify-between gap-4 rounded-[17px] bg-[#101516] px-4 py-4 sm:min-h-[82px] sm:gap-5 sm:px-5"
                        >
                            <span
                                class="flex min-w-0 items-center gap-3 sm:gap-4"
                            >
                                <span
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-[#0FA7B4]/12 text-[#35c4ce] ring-1 ring-[#0FA7B4]/20 transition duration-300 group-hover:scale-105 group-hover:bg-[#0FA7B4]/18 sm:h-12 sm:w-12"
                                >
                                    <ShoppingBag
                                        class="h-5 w-5"
                                    />
                                </span>

                                <span
                                    class="min-w-0"
                                >
                                    <span
                                        class="block text-[8px] font-black uppercase tracking-[0.14em] text-[#35c4ce] sm:text-[9px]"
                                    >
                                        Solicitar cotización
                                    </span>

                                    <span
                                        class="mt-1 block truncate text-sm font-black text-white sm:text-base"
                                    >
                                        Cotizar
                                        {{
                                            product.name
                                        }}
                                    </span>
                                </span>
                            </span>

                            <span
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/[0.06] text-white/60 transition duration-300 group-hover:translate-x-1 group-hover:bg-[#0FA7B4] group-hover:text-white"
                            >
                                <ArrowRight
                                    class="h-4 w-4"
                                />
                            </span>
                        </span>
                    </button>

                    <!-- Descripción -->

                    <div
                        v-if="
                            product.description
                        "
                        class="mt-8 border-t border-black/[0.07] pt-7"
                    >
                        <p
                            class="whitespace-pre-line text-sm leading-7 text-black/50"
                        >
                            {{
                                product.description
                            }}
                        </p>
                    </div>

                    <!-- Promoción propia -->

                    <div
                        v-if="
                            ads.rectangle.length
                        "
                        class="mt-8 border-t border-black/[0.07] pt-7"
                    >
                        <div
                            class="mb-3 flex max-w-[336px] items-center justify-between gap-3"
                        >
                            <p
                                class="text-[8px] font-black uppercase tracking-[0.14em] text-black/25"
                            >
                                Conecta con ADN
                            </p>

                            <span
                                class="text-[8px] font-bold text-black/15"
                            >
                                Redes y novedades
                            </span>
                        </div>

                        <div
                            class="mx-auto w-full max-w-[336px] lg:mx-0"
                        >
                            <PromoSlot
                                :ads="
                                    ads.rectangle
                                "
                                aspect-class="aspect-[6/5]"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <QuoteModal
            :open="
                quoteModalOpen
            "
            :product="
                product
            "
            @close="
                quoteModalOpen =
                    false
            "
        />
    </PublicLayout>
</template>