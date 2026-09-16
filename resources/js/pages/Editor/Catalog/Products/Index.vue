<script setup lang="ts">
import {
    Head,
    Link,
    router,
    usePage,
} from '@inertiajs/vue3';

import {
    Boxes,
    CheckCircle2,
    ImagePlus,
    LockKeyhole,
    Pencil,
    Search,
    Settings2,
    Star,
    TriangleAlert,
    WandSparkles,
} from '@lucide/vue';

import {
    reactive,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface Category {
    id: number;
    name: string;
}

interface WebProduct {
    id: number;
    code: string;
    slug: string;
    short_description: string | null;
    status: string;
    featured: boolean;
    show_on_home: boolean;
    price_visible: boolean;
    reference_price: string | null;
    category: Category | null;
    images_count: number;
    fields_count: number;
}

interface AppProduct {
    id: number;
    app_catalog_item_id: number;
    item_code: string;
    name: string;
    description: string | null;
    item_type: string;
    category: string | null;
    pricing_method: string;
    measurement_unit: string | null;
    active: boolean;
    source_updated_at: string | null;
    web_product: WebProduct | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface ProductPaginator {
    data: AppProduct[];
    current_page: number;
    last_page: number;
    total: number;
    links: PaginationLink[];
}

interface Summary {
    total: number;
    prepared: number;
    pending: number;
    published: number;
    legacy: number;
}

interface CatalogCategory {
    id: number;
    name: string;
    code: string;
    parent_id: number | null;
    status: string;
}

interface LegacyProduct {
    id: number;
    code: string;
    name: string;
    app_reference_code: string | null;
    status: string;
}

interface Filters {
    search: string;
    status: string;
    category: number | null;
}

interface SharedProps {
    flash?: {
        success?: string | null;
        error?: string | null;
    };

    [key: string]: unknown;
}

const props =
    defineProps<{
        products: ProductPaginator;
        summary: Summary;
        categories: CatalogCategory[];
        legacyProducts: LegacyProduct[];
        filters: Filters;
    }>();

const page =
    usePage<SharedProps>();

const filters =
    reactive({
        search:
            props.filters.search
            ?? '',

        status:
            props.filters.status
            ?? '',

        category:
            props.filters.category
                ? String(
                    props.filters.category,
                )
                : '',
    });

const applyFilters =
    (): void => {
        router.get(
            '/editor-preview/catalogo/productos',
            {
                search:
                    filters.search
                    || undefined,

                status:
                    filters.status
                    || undefined,

                category:
                    filters.category
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

const clearFilters =
    (): void => {
        filters.search =
            '';

        filters.status =
            '';

        filters.category =
            '';

        router.get(
            '/editor-preview/catalogo/productos',
        );
    };

const statusLabel =
    (
        status:
            string,
    ): string => {
        const labels:
            Record<string, string> = {
            published:
                'Publicado',

            draft:
                'Borrador',

            hidden:
                'Oculto',
        };

        return labels[
            status
        ]
        ?? status;
    };

const statusClasses =
    (
        status:
            string,
    ): string => {
        if (
            status ===
            'published'
        ) {
            return 'border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]';
        }

        if (
            status ===
            'hidden'
        ) {
            return 'border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] text-[var(--adn-coral)]';
        }

        return 'border-[var(--adn-yellow-border)] bg-[var(--adn-yellow-soft)] text-[var(--adn-yellow)]';
    };

const itemTypeLabel =
    (
        type:
            string,
    ): string => {
        const labels:
            Record<string, string> = {
            product:
                'Producto',

            service:
                'Servicio',

            custom:
                'Personalizado',
        };

        return labels[
            type
        ]
        ?? type;
    };

const pricingMethodLabel =
    (
        method:
            string,
    ): string => {
        const labels:
            Record<string, string> = {
            AREA:
                'Por área',

            UNIT:
                'Por unidad',

            FIXED:
                'Precio fijo',

            LINEAR:
                'Lineal',

            COST_MARGIN:
                'Costo + margen',

            RESALE:
                'Reventa',

            MANUAL:
                'Manual',
        };

        return labels[
            method
        ]
        ?? method;
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
            return '—';
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
        title="Productos"
    />

    <EditorLayout
        title="Productos"
        eyebrow="Catálogo ADN"
    >
        <div
            v-if="page.props.flash?.success"
            class="mb-5 flex items-center gap-3 rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-turquoise)]"
        >
            <CheckCircle2
                class="h-4 w-4"
            />

            {{
                page.props.flash.success
            }}
        </div>

        <section
            class="flex flex-col justify-between gap-5 lg:flex-row lg:items-end"
        >
            <div>
                <p
                    class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-orange)]"
                >
                    Catálogo maestro sincronizado
                </p>

                <h1
                    class="mt-2 text-2xl font-black tracking-[-0.04em] sm:text-3xl"
                >
                    Productos de ADN APP
                </h1>

                <p
                    class="mt-2 max-w-3xl text-xs leading-6 text-white/32"
                >
                    Los productos se crean y administran desde ADN APP.
                    Desde aquí únicamente preparas su contenido público,
                    imágenes y formulario de cotización.
                </p>
            </div>

            <div
                class="flex items-center gap-2 rounded-xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-4 py-3"
            >
                <LockKeyhole
                    class="h-4 w-4 text-[var(--adn-turquoise)]"
                />

                <div>
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.1em] text-[var(--adn-turquoise)]"
                    >
                        Catálogo protegido
                    </p>

                    <p
                        class="mt-0.5 text-[10px] text-white/35"
                    >
                        Altas y cambios maestros: solo ADN APP
                    </p>
                </div>
            </div>
        </section>

        <section
            class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4"
        >
            <article
                class="adn-panel rounded-[18px] p-5"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/24"
                >
                    Sincronizados
                </p>

                <p
                    class="mt-2 text-2xl font-black text-[var(--adn-orange)]"
                >
                    {{
                        summary.total
                    }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-5"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/24"
                >
                    Preparados
                </p>

                <p
                    class="mt-2 text-2xl font-black text-[var(--adn-turquoise)]"
                >
                    {{
                        summary.prepared
                    }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-5"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/24"
                >
                    Pendientes
                </p>

                <p
                    class="mt-2 text-2xl font-black text-[var(--adn-yellow)]"
                >
                    {{
                        summary.pending
                    }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-5"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/24"
                >
                    Publicados
                </p>

                <p
                    class="mt-2 text-2xl font-black text-[var(--adn-coral)]"
                >
                    {{
                        summary.published
                    }}
                </p>
            </article>
        </section>

        <section
            class="adn-panel mt-5 rounded-[22px] p-4"
        >
            <form
                class="grid gap-3 xl:grid-cols-[1fr_220px_260px_auto]"
                @submit.prevent="
                    applyFilters
                "
            >
                <label
                    class="relative"
                >
                    <Search
                        class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/25"
                    />

                    <input
                        v-model="
                            filters.search
                        "
                        type="text"
                        placeholder="Buscar por nombre, código APP, categoría..."
                        class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 pl-11 pr-4 text-sm text-white outline-none placeholder:text-white/20"
                    >
                </label>

                <select
                    v-model="
                        filters.status
                    "
                    class="h-11 rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white/70"
                >
                    <option value="">
                        Todos los estados
                    </option>

                    <option value="pending">
                        Pendientes
                    </option>

                    <option value="prepared">
                        Preparados
                    </option>

                    <option value="published">
                        Publicados
                    </option>

                    <option value="draft">
                        Borradores
                    </option>

                    <option value="hidden">
                        Ocultos
                    </option>

                    <option value="inactive">
                        Inactivos en APP
                    </option>
                </select>

                <select
                    v-model="
                        filters.category
                    "
                    class="h-11 rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white/70"
                >
                    <option value="">
                        Todas las categorías web
                    </option>

                    <option
                        v-for="
                            category in categories
                        "
                        :key="
                            category.id
                        "
                        :value="
                            String(
                                category.id,
                            )
                        "
                    >
                        {{
                            category.name
                        }}
                    </option>
                </select>

                <div
                    class="flex gap-2"
                >
                    <button
                        type="submit"
                        class="adn-primary-button h-11 rounded-xl px-5 text-xs font-black"
                    >
                        Filtrar
                    </button>

                    <button
                        type="button"
                        class="h-11 rounded-xl border border-white/[0.07] px-4 text-xs font-black text-white/40"
                        @click="
                            clearFilters
                        "
                    >
                        Limpiar
                    </button>
                </div>
            </form>
        </section>

        <section
            class="adn-panel mt-5 overflow-hidden rounded-[22px]"
        >
            <div
                v-if="
                    products.data.length
                "
                class="overflow-x-auto"
            >
                <table
                    class="w-full min-w-[1320px]"
                >
                    <thead>
                        <tr
                            class="border-b border-[var(--adn-border)]"
                        >
                            <th
                                class="px-6 py-4 text-left text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                            >
                                Producto APP
                            </th>

                            <th
                                class="px-4 py-4 text-left text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                            >
                                Clasificación
                            </th>

                            <th
                                class="px-4 py-4 text-left text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                            >
                                Cálculo
                            </th>

                            <th
                                class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                            >
                                APP
                            </th>

                            <th
                                class="px-4 py-4 text-left text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                            >
                                Sitio web
                            </th>

                            <th
                                class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                            >
                                Recursos
                            </th>

                            <th
                                class="px-6 py-4 text-right text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                            >
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="
                                product in products.data
                            "
                            :key="
                                product.id
                            "
                            class="border-b border-white/[0.035] transition hover:bg-white/[0.018] last:border-0"
                        >
                            <td
                                class="px-6 py-4"
                            >
                                <div
                                    class="flex items-center gap-3"
                                >
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[var(--adn-orange-soft)] text-[var(--adn-orange)]"
                                    >
                                        <Boxes
                                            class="h-5 w-5"
                                        />
                                    </div>

                                    <div
                                        class="min-w-0"
                                    >
                                        <div
                                            class="flex items-center gap-2"
                                        >
                                            <p
                                                class="max-w-[300px] truncate text-sm font-black text-white/80"
                                            >
                                                {{
                                                    product.name
                                                }}
                                            </p>

                                            <Star
                                                v-if="
                                                    product.web_product?.featured
                                                "
                                                class="h-3.5 w-3.5 fill-[var(--adn-yellow)] text-[var(--adn-yellow)]"
                                            />
                                        </div>

                                        <p
                                            class="mt-1 font-mono text-[10px] font-black text-[var(--adn-turquoise)]"
                                        >
                                            {{
                                                product.item_code
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td
                                class="px-4 py-4"
                            >
                                <p
                                    class="text-xs font-bold text-white/55"
                                >
                                    {{
                                        itemTypeLabel(
                                            product.item_type,
                                        )
                                    }}
                                </p>

                                <p
                                    class="mt-1 text-[10px] text-white/25"
                                >
                                    {{
                                        product.category
                                        ?? 'Sin categoría APP'
                                    }}
                                </p>
                            </td>

                            <td
                                class="px-4 py-4"
                            >
                                <p
                                    class="text-xs font-bold text-white/55"
                                >
                                    {{
                                        pricingMethodLabel(
                                            product.pricing_method,
                                        )
                                    }}
                                </p>

                                <p
                                    class="mt-1 text-[10px] text-white/25"
                                >
                                    {{
                                        product.measurement_unit
                                        ?? 'Sin unidad'
                                    }}
                                </p>
                            </td>

                            <td
                                class="px-4 py-4 text-center"
                            >
                                <span
                                    v-if="
                                        product.active
                                    "
                                    class="inline-flex rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-[8px] font-black uppercase tracking-[0.08em] text-emerald-400"
                                >
                                    Activo
                                </span>

                                <span
                                    v-else
                                    class="inline-flex rounded-lg border border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] px-2.5 py-1 text-[8px] font-black uppercase tracking-[0.08em] text-[var(--adn-coral)]"
                                >
                                    Inactivo
                                </span>
                            </td>

                            <td
                                class="px-4 py-4"
                            >
                                <template
                                    v-if="
                                        product.web_product
                                    "
                                >
                                    <div
                                        class="flex flex-wrap items-center gap-2"
                                    >
                                        <span
                                            class="inline-flex rounded-lg border px-2 py-1 text-[8px] font-black uppercase tracking-[0.08em]"
                                            :class="
                                                statusClasses(
                                                    product.web_product.status,
                                                )
                                            "
                                        >
                                            {{
                                                statusLabel(
                                                    product.web_product.status,
                                                )
                                            }}
                                        </span>

                                        <span
                                            class="text-[10px] font-black text-white/25"
                                        >
                                            {{
                                                product.web_product.code
                                            }}
                                        </span>
                                    </div>

                                    <p
                                        class="mt-2 text-[10px] text-white/30"
                                    >
                                        {{
                                            product.web_product.category?.name
                                            ?? 'Sin categoría web'
                                        }}
                                    </p>

                                    <p
                                        v-if="
                                            product.web_product.price_visible
                                        "
                                        class="mt-1 text-[10px] font-bold text-[var(--adn-orange)]"
                                    >
                                        {{
                                            formatPrice(
                                                product.web_product.reference_price,
                                            )
                                        }}
                                    </p>
                                </template>

                                <div
                                    v-else
                                    class="flex items-center gap-2"
                                >
                                    <span
                                        class="h-2 w-2 rounded-full bg-[var(--adn-yellow)]"
                                    />

                                    <span
                                        class="text-xs font-bold text-[var(--adn-yellow)]"
                                    >
                                        Sin preparar
                                    </span>
                                </div>
                            </td>

                            <td
                                class="px-4 py-4 text-center"
                            >
                                <div
                                    v-if="
                                        product.web_product
                                    "
                                    class="flex justify-center gap-2"
                                >
                                    <Link
                                        :href="
                                            `/editor-preview/catalogo/productos/${product.web_product.id}/imagenes`
                                        "
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-[var(--adn-orange-border)] bg-[var(--adn-orange-soft)] px-3 py-2 text-[9px] font-black text-[var(--adn-orange)]"
                                    >
                                        <ImagePlus
                                            class="h-3.5 w-3.5"
                                        />

                                        {{
                                            product.web_product.images_count
                                        }}
                                    </Link>

                                    <Link
                                        :href="
                                            `/editor-preview/catalogo/productos/${product.web_product.id}/campos`
                                        "
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-3 py-2 text-[9px] font-black text-[var(--adn-turquoise)]"
                                    >
                                        <Settings2
                                            class="h-3.5 w-3.5"
                                        />

                                        {{
                                            product.web_product.fields_count
                                        }}
                                    </Link>
                                </div>

                                <span
                                    v-else
                                    class="text-xs text-white/18"
                                >
                                    —
                                </span>
                            </td>

                            <td
                                class="px-6 py-4"
                            >
                                <div
                                    class="flex justify-end gap-2"
                                >
                                    <template
                                        v-if="
                                            product.web_product
                                        "
                                    >
                                        <Link
                                            :href="
                                                `/editor-preview/catalogo/productos/${product.web_product.id}/imagenes`
                                            "
                                            title="Gestionar imágenes"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/[0.06] text-white/35 transition hover:border-[var(--adn-orange-border)] hover:bg-[var(--adn-orange-soft)] hover:text-[var(--adn-orange)]"
                                        >
                                            <ImagePlus
                                                class="h-4 w-4"
                                            />
                                        </Link>

                                        <Link
                                            :href="
                                                `/editor-preview/catalogo/productos/${product.web_product.id}/campos`
                                            "
                                            title="Configurar cotización"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/[0.06] text-white/35 transition hover:border-[var(--adn-coral-border)] hover:bg-[var(--adn-coral-soft)] hover:text-[var(--adn-coral)]"
                                        >
                                            <Settings2
                                                class="h-4 w-4"
                                            />
                                        </Link>

                                        <Link
                                            :href="
                                                `/editor-preview/catalogo/productos/${product.web_product.id}/editar`
                                            "
                                            title="Editar contenido web"
                                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/[0.06] text-white/35 transition hover:border-[var(--adn-turquoise-border)] hover:bg-[var(--adn-turquoise-soft)] hover:text-[var(--adn-turquoise)]"
                                        >
                                            <Pencil
                                                class="h-4 w-4"
                                            />
                                        </Link>
                                    </template>

                                    <Link
                                        v-else-if="
                                            product.active
                                        "
                                        :href="
                                            `/editor-preview/catalogo/productos/app/${product.id}/preparar`
                                        "
                                        class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[var(--adn-yellow-border)] bg-[var(--adn-yellow-soft)] px-4 text-[9px] font-black uppercase tracking-[0.08em] text-[var(--adn-yellow)] transition hover:-translate-y-0.5"
                                    >
                                        <WandSparkles
                                            class="h-4 w-4"
                                        />

                                        Preparar para sitio
                                    </Link>

                                    <span
                                        v-else
                                        class="inline-flex items-center gap-2 rounded-xl border border-white/[0.05] px-3 py-2 text-[9px] font-black uppercase text-white/20"
                                    >
                                        <LockKeyhole
                                            class="h-3.5 w-3.5"
                                        />

                                        No disponible
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-else
                class="flex min-h-72 flex-col items-center justify-center px-6 py-12 text-center"
            >
                <Boxes
                    class="h-10 w-10 text-white/12"
                />

                <p
                    class="mt-4 text-sm font-black text-white/55"
                >
                    No hay productos sincronizados
                </p>

                <p
                    class="mt-1 text-xs text-white/25"
                >
                    Los productos aparecerán aquí cuando sean enviados desde ADN APP.
                </p>
            </div>
        </section>

        <div
            v-if="
                products.links
                &&
                products.last_page >
                    1
            "
            class="mt-5 flex flex-wrap justify-center gap-2"
        >
            <template
                v-for="
                    link in products.links
                "
                :key="
                    link.label
                "
            >
                <Link
                    v-if="
                        link.url
                    "
                    :href="
                        link.url
                    "
                    preserve-scroll
                    class="rounded-xl border px-3 py-2 text-xs font-bold transition"
                    :class="
                        link.active
                            ? 'border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]'
                            : 'border-white/[0.06] text-white/35 hover:text-white/60'
                    "
                    v-html="
                        link.label
                    "
                />

                <span
                    v-else
                    class="rounded-xl border border-white/[0.04] px-3 py-2 text-xs text-white/15"
                    v-html="
                        link.label
                    "
                />
            </template>
        </div>

        <section
            v-if="
                legacyProducts.length
            "
            class="mt-6 rounded-[22px] border border-[var(--adn-yellow-border)] bg-[var(--adn-yellow-soft)] p-5"
        >
            <div
                class="flex items-start gap-3"
            >
                <TriangleAlert
                    class="mt-0.5 h-5 w-5 shrink-0 text-[var(--adn-yellow)]"
                />

                <div>
                    <h2
                        class="text-sm font-black text-[var(--adn-yellow)]"
                    >
                        Contenido web heredado sin vínculo
                    </h2>

                    <p
                        class="mt-1 max-w-3xl text-[10px] leading-5 text-white/35"
                    >
                        Estos productos fueron creados antes de conectar el catálogo
                        con ADN APP. No pueden editarse ni eliminarse desde este
                        listado. Cuando prepares su equivalente desde ADN APP, el
                        sistema intentará vincularlos automáticamente sin perder
                        imágenes ni contenido.
                    </p>
                </div>
            </div>

            <div
                class="mt-4 grid gap-2 lg:grid-cols-2"
            >
                <div
                    v-for="
                        legacy in legacyProducts
                    "
                    :key="
                        legacy.id
                    "
                    class="rounded-xl border border-white/[0.06] bg-black/10 px-4 py-3"
                >
                    <div
                        class="flex items-center justify-between gap-3"
                    >
                        <div>
                            <p
                                class="text-xs font-black text-white/60"
                            >
                                {{
                                    legacy.name
                                }}
                            </p>

                            <p
                                class="mt-1 text-[9px] text-white/25"
                            >
                                {{
                                    legacy.code
                                }}
                                ·
                                {{
                                    legacy.app_reference_code
                                    ?? 'Sin referencia'
                                }}
                            </p>
                        </div>

                        <span
                            class="rounded-lg border border-[var(--adn-yellow-border)] px-2 py-1 text-[8px] font-black uppercase text-[var(--adn-yellow)]"
                        >
                            Sin vínculo
                        </span>
                    </div>
                </div>
            </div>
        </section>
    </EditorLayout>
</template>