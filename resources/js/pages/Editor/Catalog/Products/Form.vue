<script setup lang="ts">
import {
    Head,
    Link,
    useForm,
    usePage,
} from '@inertiajs/vue3';

import {
    ArrowLeft,
    Boxes,
    Check,
    DollarSign,
    ImagePlus,
    LockKeyhole,
    Save,
    SearchCheck,
    Settings2,
    ShieldCheck,
    Star,
} from '@lucide/vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface Category {
    id: number;
    name: string;
    code: string;
    parent_id: number | null;
    status: string;
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
    suggested_quote_mode: string;
}

interface Product {
    id: number;
    code: string;
    slug: string;
    category_id: number | null;
    short_description: string | null;
    description: string | null;
    features: string[] | null;
    price_visible: boolean;
    price_from: boolean;
    reference_price: string | null;
    price_note: string | null;
    quote_mode: string;
    status: string;
    featured: boolean;
    show_on_home: boolean;
    sort_order: number;
    meta_title: string | null;
    meta_description: string | null;
    images_count: number;
    fields_count: number;
    quote_request_items_count: number;
}

interface SharedProps {
    flash?: {
        success?: string | null;
    };

    [key: string]: unknown;
}

const props =
    defineProps<{
        mode: 'prepare' | 'edit';
        product: Product | null;
        appProduct: AppProduct;
        categories: Category[];
    }>();

const page =
    usePage<SharedProps>();

const form =
    useForm({
        category_id:
            props.product?.category_id
                ? String(
                    props.product.category_id,
                )
                : '',

        short_description:
            props.product?.short_description
            ?? '',

        description:
            props.product?.description
            ?? '',

        features_text:
            props.product?.features
                ?.join('\n')
            ?? '',

        price_visible:
            props.product?.price_visible
            ?? false,

        price_from:
            props.product?.price_from
            ?? false,

        reference_price:
            props.product?.reference_price
            ?? '',

        price_note:
            props.product?.price_note
            ?? '',

        quote_mode:
            props.product?.quote_mode
            ?? props.appProduct
                .suggested_quote_mode,

        status:
            props.product?.status
            ?? 'draft',

        featured:
            props.product?.featured
            ?? false,

        show_on_home:
            props.product?.show_on_home
            ?? false,

        sort_order:
            props.product?.sort_order
            ?? 0,

        meta_title:
            props.product?.meta_title
            ?? '',

        meta_description:
            props.product?.meta_description
            ?? '',
    });

const submit =
    (): void => {
        form.transform(
            (
                data,
            ) => ({
                category_id:
                    data.category_id
                        ? Number(
                            data.category_id,
                        )
                        : null,

                short_description:
                    data.short_description
                    || null,

                description:
                    data.description
                    || null,

                features:
                    data.features_text
                        .split('\n')
                        .map(
                            (
                                item,
                            ) =>
                                item.trim(),
                        )
                        .filter(
                            Boolean,
                        ),

                price_visible:
                    Boolean(
                        data.price_visible,
                    ),

                price_from:
                    Boolean(
                        data.price_from,
                    ),

                reference_price:
                    data.reference_price !==
                    ''
                        ? Number(
                            data.reference_price,
                        )
                        : null,

                price_note:
                    data.price_note
                    || null,

                quote_mode:
                    data.quote_mode,

                status:
                    data.status,

                featured:
                    Boolean(
                        data.featured,
                    ),

                show_on_home:
                    Boolean(
                        data.show_on_home,
                    ),

                sort_order:
                    Number(
                        data.sort_order,
                    ),

                meta_title:
                    data.meta_title
                    || null,

                meta_description:
                    data.meta_description
                    || null,
            }),
        );

        if (
            props.mode ===
            'prepare'
        ) {
            form.post(
                `/editor-preview/catalogo/productos/app/${props.appProduct.id}`,
                {
                    preserveScroll:
                        true,
                },
            );

            return;
        }

        if (
            !props.product
        ) {
            return;
        }

        form.put(
            `/editor-preview/catalogo/productos/${props.product.id}`,
            {
                preserveScroll:
                    true,
            },
        );
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
                'Medida lineal',

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
</script>

<template>
    <Head
        :title="
            mode === 'prepare'
                ? `Preparar ${appProduct.name}`
                : `Contenido web · ${appProduct.name}`
        "
    />

    <EditorLayout
        :title="
            mode === 'prepare'
                ? 'Preparar para sitio'
                : 'Editar contenido web'
        "
        eyebrow="Catálogo ADN"
    >
        <div
            v-if="
                page.props.flash?.success
            "
            class="mb-5 flex items-center gap-3 rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-turquoise)]"
        >
            <Check
                class="h-4 w-4"
            />

            {{
                page.props.flash.success
            }}
        </div>

        <section
            class="flex flex-col justify-between gap-5 lg:flex-row lg:items-center"
        >
            <div
                class="flex items-center gap-4"
            >
                <Link
                    href="/editor-preview/catalogo/productos"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/[0.07] text-white/35 transition hover:bg-white/[0.04] hover:text-white"
                >
                    <ArrowLeft
                        class="h-4 w-4"
                    />
                </Link>

                <div>
                    <div
                        class="flex flex-wrap items-center gap-2"
                    >
                        <p
                            class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-turquoise)]"
                        >
                            {{
                                appProduct.item_code
                            }}
                        </p>

                        <span
                            class="inline-flex items-center gap-1 rounded-lg border border-white/[0.06] bg-white/[0.025] px-2 py-1 text-[8px] font-black uppercase tracking-[0.08em] text-white/30"
                        >
                            <LockKeyhole
                                class="h-3 w-3"
                            />

                            ADN APP
                        </span>
                    </div>

                    <h1
                        class="mt-2 text-2xl font-black tracking-[-0.035em] sm:text-3xl"
                    >
                        {{
                            appProduct.name
                        }}
                    </h1>
                </div>
            </div>

            <div
                v-if="
                    product
                "
                class="flex flex-wrap items-center gap-2"
            >
                <span
                    class="rounded-xl border border-[var(--adn-orange-border)] bg-[var(--adn-orange-soft)] px-3 py-2 text-[9px] font-black uppercase tracking-[0.08em] text-[var(--adn-orange)]"
                >
                    {{
                        product.images_count
                    }}
                    imágenes
                </span>

                <span
                    class="rounded-xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-3 py-2 text-[9px] font-black uppercase tracking-[0.08em] text-[var(--adn-turquoise)]"
                >
                    {{
                        product.fields_count
                    }}
                    campos
                </span>

                <Link
                    :href="
                        `/editor-preview/catalogo/productos/${product.id}/imagenes`
                    "
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[var(--adn-orange-border)] bg-[var(--adn-orange-soft)] px-4 text-[10px] font-black uppercase tracking-[0.08em] text-[var(--adn-orange)] transition hover:-translate-y-0.5"
                >
                    <ImagePlus
                        class="h-4 w-4"
                    />

                    Imágenes
                </Link>

                <Link
                    :href="
                        `/editor-preview/catalogo/productos/${product.id}/campos`
                    "
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] px-4 text-[10px] font-black uppercase tracking-[0.08em] text-[var(--adn-coral)] transition hover:-translate-y-0.5"
                >
                    <Settings2
                        class="h-4 w-4"
                    />

                    Cotización
                </Link>
            </div>
        </section>

        <!-- Datos maestros -->

        <section
            class="mt-6 overflow-hidden rounded-[22px] border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)]"
        >
            <div
                class="flex flex-col gap-4 border-b border-[var(--adn-turquoise-border)] px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div
                    class="flex items-center gap-3"
                >
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-black/15 text-[var(--adn-turquoise)]"
                    >
                        <ShieldCheck
                            class="h-5 w-5"
                        />
                    </div>

                    <div>
                        <h2
                            class="text-sm font-black text-[var(--adn-turquoise)]"
                        >
                            Información maestra
                        </h2>

                        <p
                            class="mt-0.5 text-[10px] text-white/32"
                        >
                            Estos datos vienen de ADN APP y no pueden modificarse desde el Editor.
                        </p>
                    </div>
                </div>

                <span
                    class="inline-flex w-fit items-center gap-2 rounded-xl border border-[var(--adn-turquoise-border)] bg-black/10 px-3 py-2 text-[9px] font-black uppercase tracking-[0.08em] text-[var(--adn-turquoise)]"
                >
                    <LockKeyhole
                        class="h-3.5 w-3.5"
                    />

                    Solo lectura
                </span>
            </div>

            <div
                class="grid gap-px bg-[var(--adn-turquoise-border)] sm:grid-cols-2 xl:grid-cols-5"
            >
                <div
                    class="bg-[#081113] p-4"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.1em] text-white/22"
                    >
                        Código APP
                    </p>

                    <p
                        class="mt-2 font-mono text-sm font-black text-[var(--adn-turquoise)]"
                    >
                        {{
                            appProduct.item_code
                        }}
                    </p>
                </div>

                <div
                    class="bg-[#081113] p-4"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.1em] text-white/22"
                    >
                        Tipo
                    </p>

                    <p
                        class="mt-2 text-sm font-black text-white/65"
                    >
                        {{
                            itemTypeLabel(
                                appProduct.item_type,
                            )
                        }}
                    </p>
                </div>

                <div
                    class="bg-[#081113] p-4"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.1em] text-white/22"
                    >
                        Categoría APP
                    </p>

                    <p
                        class="mt-2 text-sm font-black text-white/65"
                    >
                        {{
                            appProduct.category
                            ?? 'Sin categoría'
                        }}
                    </p>
                </div>

                <div
                    class="bg-[#081113] p-4"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.1em] text-white/22"
                    >
                        Cálculo
                    </p>

                    <p
                        class="mt-2 text-sm font-black text-white/65"
                    >
                        {{
                            pricingMethodLabel(
                                appProduct.pricing_method,
                            )
                        }}
                    </p>
                </div>

                <div
                    class="bg-[#081113] p-4"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.1em] text-white/22"
                    >
                        Unidad
                    </p>

                    <p
                        class="mt-2 text-sm font-black text-white/65"
                    >
                        {{
                            appProduct.measurement_unit
                            ?? '—'
                        }}
                    </p>
                </div>
            </div>

            <div
                v-if="
                    appProduct.description
                "
                class="border-t border-[var(--adn-turquoise-border)] bg-[#081113] px-5 py-4"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.1em] text-white/22"
                >
                    Descripción base de ADN APP
                </p>

                <p
                    class="mt-2 whitespace-pre-line text-xs leading-6 text-white/42"
                >
                    {{
                        appProduct.description
                    }}
                </p>
            </div>
        </section>

        <form
            class="mt-5 grid gap-5 xl:grid-cols-[1fr_390px]"
            @submit.prevent="
                submit
            "
        >
            <div
                class="space-y-5"
            >
                <!-- Información comercial -->

                <section
                    class="adn-panel rounded-[22px] p-5 sm:p-6"
                >
                    <div
                        class="mb-6 flex items-center gap-3"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--adn-orange-soft)] text-[var(--adn-orange)]"
                        >
                            <Boxes
                                class="h-5 w-5"
                            />
                        </div>

                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                Información para el cliente
                            </h2>

                            <p
                                class="mt-0.5 text-[10px] text-white/28"
                            >
                                Contenido comercial que sí puedes administrar desde el Editor
                            </p>
                        </div>
                    </div>

                    <div
                        class="grid gap-5"
                    >
                        <label>
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Categoría del sitio
                            </span>

                            <select
                                v-model="
                                    form.category_id
                                "
                                class="h-12 w-full rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white/70 outline-none"
                            >
                                <option value="">
                                    Selecciona una categoría
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

                            <p
                                v-if="
                                    form.errors.category_id
                                "
                                class="mt-2 text-xs font-bold text-[var(--adn-coral)]"
                            >
                                {{
                                    form.errors.category_id
                                }}
                            </p>
                        </label>

                        <label>
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Descripción corta
                            </span>

                            <textarea
                                v-model="
                                    form.short_description
                                "
                                rows="3"
                                maxlength="500"
                                placeholder="Resumen comercial del producto..."
                                class="w-full resize-y rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm leading-6 text-white outline-none placeholder:text-white/18"
                            />
                        </label>

                        <label>
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Descripción completa
                            </span>

                            <textarea
                                v-model="
                                    form.description
                                "
                                rows="7"
                                maxlength="20000"
                                placeholder="Explica materiales, usos, acabados, ventajas y demás información para el cliente..."
                                class="w-full resize-y rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm leading-6 text-white outline-none placeholder:text-white/18"
                            />
                        </label>

                        <label>
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Características
                            </span>

                            <textarea
                                v-model="
                                    form.features_text
                                "
                                rows="6"
                                placeholder="Una característica por línea"
                                class="w-full resize-y rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm leading-6 text-white outline-none placeholder:text-white/18"
                            />

                            <p
                                class="mt-2 text-[10px] text-white/23"
                            >
                                Escribe una característica por línea.
                            </p>
                        </label>
                    </div>
                </section>

                <!-- Cotización -->

                <section
                    class="adn-panel rounded-[22px] p-5 sm:p-6"
                >
                    <div
                        class="mb-6 flex items-center gap-3"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--adn-coral-soft)] text-[var(--adn-coral)]"
                        >
                            <Settings2
                                class="h-5 w-5"
                            />
                        </div>

                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                Configuración de cotización web
                            </h2>

                            <p
                                class="mt-0.5 text-[10px] text-white/28"
                            >
                                Define qué información deberá proporcionar el cliente
                            </p>
                        </div>
                    </div>

                    <div
                        class="grid gap-5 md:grid-cols-2"
                    >
                        <label>
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Modo web
                            </span>

                            <select
                                v-model="
                                    form.quote_mode
                                "
                                class="h-12 w-full rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white/70"
                            >
                                <option value="UNIT">
                                    Por unidad
                                </option>

                                <option value="AREA">
                                    Por área
                                </option>

                                <option value="LINEAR">
                                    Medida lineal
                                </option>

                                <option value="CUSTOM">
                                    Personalizado
                                </option>
                            </select>
                        </label>

                        <div>
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Unidad principal
                            </span>

                            <div
                                class="flex h-12 items-center justify-between rounded-xl border border-white/[0.055] bg-white/[0.025] px-4"
                            >
                                <span
                                    class="text-sm font-bold text-white/45"
                                >
                                    {{
                                        appProduct.measurement_unit
                                        ?? 'Sin unidad'
                                    }}
                                </span>

                                <LockKeyhole
                                    class="h-4 w-4 text-white/18"
                                />
                            </div>

                            <p
                                class="mt-2 text-[10px] text-white/20"
                            >
                                Administrado desde ADN APP.
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="
                            product
                        "
                        class="mt-5 rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] p-5"
                    >
                        <div
                            class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
                        >
                            <div>
                                <p
                                    class="text-xs font-black text-[var(--adn-turquoise)]"
                                >
                                    Formulario de cotización
                                </p>

                                <p
                                    class="mt-1 text-[10px] leading-5 text-white/35"
                                >
                                    Este producto tiene
                                    {{
                                        product.fields_count
                                    }}
                                    campos configurados.
                                </p>
                            </div>

                            <Link
                                :href="
                                    `/editor-preview/catalogo/productos/${product.id}/campos`
                                "
                                class="inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-xl border border-[var(--adn-turquoise-border)] bg-black/10 px-4 text-[10px] font-black uppercase tracking-[0.08em] text-[var(--adn-turquoise)] transition hover:bg-[var(--adn-turquoise-soft)]"
                            >
                                <Settings2
                                    class="h-4 w-4"
                                />

                                Configurar campos
                            </Link>
                        </div>
                    </div>

                    <div
                        v-else
                        class="mt-5 rounded-2xl border border-[var(--adn-yellow-border)] bg-[var(--adn-yellow-soft)] p-4"
                    >
                        <p
                            class="text-xs font-black text-[var(--adn-yellow)]"
                        >
                            Primero prepara el producto
                        </p>

                        <p
                            class="mt-1 text-[10px] leading-5 text-white/35"
                        >
                            Al guardar se habilitarán imágenes y el constructor del formulario de cotización.
                        </p>
                    </div>
                </section>

                <!-- SEO -->

                <section
                    class="adn-panel rounded-[22px] p-5 sm:p-6"
                >
                    <div
                        class="mb-6 flex items-center gap-3"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--adn-yellow-soft)] text-[var(--adn-yellow)]"
                        >
                            <SearchCheck
                                class="h-5 w-5"
                            />
                        </div>

                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                SEO
                            </h2>

                            <p
                                class="mt-0.5 text-[10px] text-white/28"
                            >
                                Información para buscadores
                            </p>
                        </div>
                    </div>

                    <div
                        class="grid gap-5"
                    >
                        <input
                            v-model="
                                form.meta_title
                            "
                            type="text"
                            maxlength="255"
                            placeholder="Título SEO"
                            class="h-12 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/18"
                        >

                        <textarea
                            v-model="
                                form.meta_description
                            "
                            rows="4"
                            maxlength="1000"
                            placeholder="Descripción SEO..."
                            class="w-full resize-y rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm leading-6 text-white outline-none placeholder:text-white/18"
                        />
                    </div>
                </section>
            </div>

            <aside
                class="space-y-5"
            >
                <section
                    class="adn-panel rounded-[22px] p-5"
                >
                    <h2
                        class="text-sm font-black"
                    >
                        Publicación
                    </h2>

                    <div
                        class="mt-5 grid gap-2"
                    >
                        <label
                            v-for="
                                option in [
                                    {
                                        value: 'published',
                                        label: 'Publicado',
                                        color: '#0FA7B4',
                                    },
                                    {
                                        value: 'draft',
                                        label: 'Borrador',
                                        color: '#F5C000',
                                    },
                                    {
                                        value: 'hidden',
                                        label: 'Oculto',
                                        color: '#E84657',
                                    },
                                ]
                            "
                            :key="
                                option.value
                            "
                            class="flex items-center gap-3 rounded-xl border p-3.5"
                            :class="[
                                form.status ===
                                option.value
                                    ? 'border-white/[0.13] bg-white/[0.045]'
                                    : 'border-white/[0.055] bg-black/10',

                                option.value ===
                                'published'
                                &&
                                !appProduct.active
                                    ? 'cursor-not-allowed opacity-35'
                                    : 'cursor-pointer',
                            ]"
                        >
                            <input
                                v-model="
                                    form.status
                                "
                                type="radio"
                                :value="
                                    option.value
                                "
                                :disabled="
                                    option.value ===
                                    'published'
                                    &&
                                    !appProduct.active
                                "
                                class="sr-only"
                            >

                            <span
                                class="h-2.5 w-2.5 rounded-full"
                                :style="{
                                    backgroundColor:
                                        option.color,
                                }"
                            />

                            <span
                                class="text-xs font-black text-white/60"
                            >
                                {{
                                    option.label
                                }}
                            </span>

                            <Check
                                v-if="
                                    form.status ===
                                    option.value
                                "
                                class="ml-auto h-4 w-4 text-[var(--adn-turquoise)]"
                            />
                        </label>
                    </div>
                </section>

                <section
                    class="adn-panel rounded-[22px] p-5"
                >
                    <div
                        class="flex items-center gap-3"
                    >
                        <Star
                            class="h-5 w-5 text-[var(--adn-yellow)]"
                        />

                        <h2
                            class="text-sm font-black"
                        >
                            Visibilidad
                        </h2>
                    </div>

                    <div
                        class="mt-5 space-y-4"
                    >
                        <label
                            class="flex cursor-pointer items-center justify-between gap-4"
                        >
                            <div>
                                <p
                                    class="text-xs font-black text-white/60"
                                >
                                    Producto destacado
                                </p>

                                <p
                                    class="mt-1 text-[10px] text-white/25"
                                >
                                    Prioridad dentro del catálogo.
                                </p>
                            </div>

                            <input
                                v-model="
                                    form.featured
                                "
                                type="checkbox"
                                class="h-4 w-4 accent-[#F5C000]"
                            >
                        </label>

                        <label
                            class="flex cursor-pointer items-center justify-between gap-4"
                        >
                            <div>
                                <p
                                    class="text-xs font-black text-white/60"
                                >
                                    Mostrar en Inicio
                                </p>

                                <p
                                    class="mt-1 text-[10px] text-white/25"
                                >
                                    Puede aparecer en la página principal.
                                </p>
                            </div>

                            <input
                                v-model="
                                    form.show_on_home
                                "
                                type="checkbox"
                                class="h-4 w-4 accent-[#0FA7B4]"
                            >
                        </label>
                    </div>
                </section>

                <section
                    class="adn-panel rounded-[22px] p-5"
                >
                    <div
                        class="flex items-center gap-3"
                    >
                        <DollarSign
                            class="h-5 w-5 text-[var(--adn-orange)]"
                        />

                        <h2
                            class="text-sm font-black"
                        >
                            Precio público
                        </h2>
                    </div>

                    <p
                        class="mt-2 text-[10px] leading-5 text-white/23"
                    >
                        Este dato es exclusivamente comercial para el sitio.
                        No corresponde al costo interno de ADN APP.
                    </p>

                    <label
                        class="mt-5 flex cursor-pointer items-center justify-between"
                    >
                        <span
                            class="text-xs font-black text-white/60"
                        >
                            Mostrar precio
                        </span>

                        <input
                            v-model="
                                form.price_visible
                            "
                            type="checkbox"
                            class="h-4 w-4 accent-[#ED7E24]"
                        >
                    </label>

                    <div
                        v-if="
                            form.price_visible
                        "
                        class="mt-5 space-y-4"
                    >
                        <label>
                            <span
                                class="mb-2 block text-[10px] font-black uppercase tracking-[0.1em] text-white/30"
                            >
                                Precio de referencia
                            </span>

                            <input
                                v-model="
                                    form.reference_price
                                "
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                                class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none"
                            >
                        </label>

                        <label
                            class="flex items-center gap-2"
                        >
                            <input
                                v-model="
                                    form.price_from
                                "
                                type="checkbox"
                                class="h-4 w-4 accent-[#ED7E24]"
                            >

                            <span
                                class="text-xs font-bold text-white/45"
                            >
                                Mostrar como “Desde”
                            </span>
                        </label>

                        <input
                            v-model="
                                form.price_note
                            "
                            type="text"
                            maxlength="255"
                            placeholder="Ej. Precio varía según medidas"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-xs text-white outline-none placeholder:text-white/18"
                        >
                    </div>
                </section>

                <section
                    class="adn-panel rounded-[22px] p-5"
                >
                    <label>
                        <span
                            class="mb-2 block text-xs font-black text-white/55"
                        >
                            Orden de visualización
                        </span>

                        <input
                            v-model.number="
                                form.sort_order
                            "
                            type="number"
                            min="0"
                            step="1"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none"
                        >
                    </label>
                </section>

                <section
                    v-if="
                        product
                    "
                    class="adn-panel rounded-[22px] p-5"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.15em] text-white/24"
                    >
                        Identificación web
                    </p>

                    <div
                        class="mt-4 space-y-3"
                    >
                        <div>
                            <p
                                class="text-[9px] uppercase text-white/20"
                            >
                                Código web
                            </p>

                            <p
                                class="mt-1 text-xs font-black text-[var(--adn-orange)]"
                            >
                                {{
                                    product.code
                                }}
                            </p>
                        </div>

                        <div>
                            <p
                                class="text-[9px] uppercase text-white/20"
                            >
                                URL
                            </p>

                            <p
                                class="mt-1 break-all text-xs font-bold text-white/45"
                            >
                                /catalogo/{{
                                    product.slug
                                }}
                            </p>
                        </div>
                    </div>
                </section>

                <button
                    type="submit"
                    :disabled="
                        form.processing
                    "
                    class="adn-primary-button flex h-12 w-full items-center justify-center gap-2 rounded-xl text-sm font-black disabled:opacity-50"
                >
                    <Save
                        class="h-4 w-4"
                    />

                    {{
                        form.processing
                            ? 'Guardando...'
                            : mode === 'prepare'
                                ? 'Guardar y preparar'
                                : 'Guardar cambios'
                    }}
                </button>
            </aside>
        </form>
    </EditorLayout>
</template>