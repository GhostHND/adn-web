<script setup lang="ts">
import {
    Head,
    Link,
    router,
} from '@inertiajs/vue3';

import {
    ArrowRight,
    ImageIcon,
    Search,
    Sparkles,
} from '@lucide/vue';

import {
    reactive,
} from 'vue';

import PromoSlot
    from '@/components/Public/Promotions/PromoSlot.vue';

import PublicLayout
    from '@/layouts/Public/PublicLayout.vue';

interface Category {
    id: number;
    name: string;
    slug: string;
    parent_id: number | null;
    products_count: number;
}

interface Media {
    id: number;
    title: string | null;
    alt_text: string | null;
    width: number | null;
    height: number | null;
    url: string;
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
    price_note: string | null;
    image: Media | null;

    category: {
        id: number;
        name: string;
        slug: string;
    } | null;
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

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface ProductPaginator {
    data: Product[];
    current_page: number;
    last_page: number;
    total: number;
    links: PaginationLink[];
}

interface Filters {
    buscar: string;
    categoria: string;
}

interface Promotions {
    sidebar: WebsiteAd[];
    mobile: WebsiteAd[];
    horizontal: WebsiteAd[];
}

const props =
    defineProps<{
        products: ProductPaginator;
        categories: Category[];

        selectedCategory: {
            id: number;
            name: string;
            slug: string;
        } | null;

        filters: Filters;

        ads: Promotions;
    }>();

const filters =
    reactive({
        buscar:
            props.filters.buscar
            ?? '',
    });

const search =
    (): void => {
        router.get(
            '/catalogo',
            {
                buscar:
                    filters.buscar
                    || undefined,

                categoria:
                    props.filters.categoria
                    || undefined,
            },
            {
                preserveState:
                    true,

                replace:
                    true,
            },
        );
    };

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
        title="Catálogo | ADN Publicidad"
    />

    <PublicLayout>
        <section
            class="relative overflow-hidden bg-[#101516] text-white"
        >
            <div
                class="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-[#0FA7B4]/10 blur-3xl"
            />

            <div
                class="absolute -right-20 bottom-0 h-80 w-80 rounded-full bg-[#E84657]/10 blur-3xl"
            />

            <div
                class="relative mx-auto max-w-[1500px] px-5 py-16 sm:px-7 sm:py-20 xl:px-10"
            >
                <div
                    class="max-w-3xl"
                >
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-[#0FA7B4]/25 bg-[#0FA7B4]/10 px-3 py-2 text-[10px] font-black uppercase tracking-[0.14em] text-[#35c4ce]"
                    >
                        <Sparkles
                            class="h-3.5 w-3.5"
                        />

                        Catálogo ADN
                    </div>

                    <h1
                        class="mt-6 text-4xl font-black tracking-[-0.045em] sm:text-5xl lg:text-6xl"
                    >
                        Lo que imaginas,

                        <span
                            class="text-[#0FA7B4]"
                        >
                            lo producimos.
                        </span>
                    </h1>

                    <p
                        class="mt-5 max-w-2xl text-base leading-7 text-white/50"
                    >
                        Explora nuestros productos y solicita una cotización
                        según las medidas, acabados y características de tu proyecto.
                    </p>
                </div>

                <form
                    class="mt-9 flex max-w-2xl gap-2 rounded-2xl border border-white/[0.08] bg-white/[0.04] p-2 backdrop-blur-xl"
                    @submit.prevent="
                        search
                    "
                >
                    <div
                        class="relative min-w-0 flex-1"
                    >
                        <Search
                            class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/25"
                        />

                        <input
                            v-model="
                                filters.buscar
                            "
                            type="search"
                            enterkeyhint="search"
                            placeholder="¿Qué necesitas?"
                            class="h-12 w-full rounded-xl border-0 bg-transparent pl-11 pr-4 text-sm text-white outline-none placeholder:text-white/25"
                        >
                    </div>

                    <button
                        type="submit"
                        class="h-12 shrink-0 rounded-xl bg-[#0FA7B4] px-6 text-xs font-black text-white"
                    >
                        Buscar
                    </button>
                </form>
            </div>
        </section>

        <section
            class="mx-auto max-w-[1780px] px-5 py-10 sm:px-7 xl:px-10"
        >
            <div
                class="mx-auto max-w-[1500px]"
            >
                <div
                    class="flex gap-2 overflow-x-auto pb-3"
                >
                    <Link
                        href="/catalogo"
                        class="shrink-0 rounded-xl border px-4 py-2.5 text-xs font-black transition"
                        :class="
                            !selectedCategory
                                ? 'border-[#0FA7B4] bg-[#0FA7B4] text-white'
                                : 'border-black/[0.08] bg-white text-black/45 hover:border-[#0FA7B4]/30'
                        "
                    >
                        Todos
                    </Link>

                    <Link
                        v-for="
                            category in categories
                        "
                        :key="
                            category.id
                        "
                        :href="
                            `/catalogo?categoria=${encodeURIComponent(category.slug)}`
                        "
                        class="shrink-0 rounded-xl border px-4 py-2.5 text-xs font-black transition"
                        :class="
                            selectedCategory?.id ===
                            category.id
                                ? 'border-[#0FA7B4] bg-[#0FA7B4] text-white'
                                : 'border-black/[0.08] bg-white text-black/45 hover:border-[#0FA7B4]/30'
                        "
                    >
                        {{
                            category.name
                        }}
                    </Link>
                </div>

                <div
                    class="mt-8 flex flex-col justify-between gap-3 sm:flex-row sm:items-end"
                >
                    <div>
                        <p
                            class="text-[10px] font-black uppercase tracking-[0.15em] text-[#ED7E24]"
                        >
                            {{
                                selectedCategory
                                    ? selectedCategory.name
                                    : 'Todos los productos'
                            }}
                        </p>

                        <h2
                            class="mt-2 text-3xl font-black tracking-[-0.035em]"
                        >
                            {{
                                products.total
                            }}
                            productos
                        </h2>
                    </div>

                    <p
                        class="text-sm text-black/35"
                    >
                        Selecciona un producto para ver detalles y cotizar.
                    </p>
                </div>

                <div
                    v-if="
                        ads.mobile.length
                    "
                    class="mx-auto mt-7 max-w-[360px] sm:hidden"
                >
                    <PromoSlot
                        :ads="
                            ads.mobile
                        "
                        aspect-class="aspect-[6/5]"
                    />
                </div>

                <div
                    v-if="
                        ads.horizontal.length
                    "
                    class="mx-auto mt-7 hidden max-w-[728px] sm:block"
                    :class="
                        ads.sidebar.length
                            ? '2xl:hidden'
                            : ''
                    "
                >
                    <PromoSlot
                        :ads="
                            ads.horizontal
                        "
                        aspect-class="aspect-[728/90]"
                    />
                </div>
            </div>

            <div
                class="mx-auto mt-7 grid max-w-[1780px] items-start gap-6"
                :class="
                    ads.sidebar.length
                        ? '2xl:grid-cols-[minmax(0,1fr)_260px]'
                        : 'max-w-[1500px]'
                "
            >
                <div
                    class="min-w-0"
                >
                    <div
                        v-if="
                            products.data.length
                        "
                        class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                    >
                        <Link
                            v-for="
                                product in products.data
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
                                            class="text-[10px] font-black uppercase tracking-[0.08em] text-black/25"
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
                        class="flex min-h-80 flex-col items-center justify-center rounded-[24px] border border-dashed border-black/[0.08] bg-white px-6 text-center"
                    >
                        <Search
                            class="h-10 w-10 text-black/10"
                        />

                        <p
                            class="mt-4 text-lg font-black"
                        >
                            No encontramos productos
                        </p>

                        <p
                            class="mt-2 text-sm text-black/35"
                        >
                            Prueba con otra búsqueda o categoría.
                        </p>

                        <Link
                            href="/catalogo"
                            class="mt-5 rounded-xl bg-[#0FA7B4] px-5 py-3 text-xs font-black text-white"
                        >
                            Ver todo
                        </Link>
                    </div>
                </div>

                <aside
                    v-if="
                        ads.sidebar.length
                    "
                    class="hidden 2xl:block"
                >
                    <div
                        class="sticky top-24"
                    >
                        <div
                            class="mb-2 flex items-center justify-between px-1"
                        >
                            <p
                                class="text-[8px] font-black uppercase tracking-[0.14em] text-black/20"
                            >
                                Conecta con ADN
                            </p>

                            <span
                                class="text-[8px] font-bold text-black/15"
                            >
                                ADN Publicidad
                            </span>
                        </div>

                        <PromoSlot
                            :ads="
                                ads.sidebar
                            "
                            aspect-class="aspect-[1/2]"
                        />
                    </div>
                </aside>
            </div>

            <div
                v-if="
                    products.last_page >
                    1
                "
                class="mx-auto mt-9 flex max-w-[1500px] flex-wrap justify-center gap-2"
            >
                <Link
                    v-for="
                        link in products.links
                    "
                    :key="
                        link.label
                    "
                    :href="
                        link.url
                        ?? ''
                    "
                    preserve-scroll
                    class="flex min-h-10 min-w-10 items-center justify-center rounded-xl border px-3 text-xs font-black"
                    :class="
                        link.active
                            ? 'border-[#0FA7B4] bg-[#0FA7B4] text-white'
                            : link.url
                                ? 'border-black/[0.08] bg-white text-black/40'
                                : 'pointer-events-none border-black/[0.04] text-black/15'
                    "
                    v-html="
                        link.label
                    "
                />
            </div>
        </section>
    </PublicLayout>
</template>