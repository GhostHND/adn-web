<script setup lang="ts">
import {
    Head,
    Link,
    router,
    useForm,
    usePage,
} from '@inertiajs/vue3';

import {
    ArrowLeft,
    Asterisk,
    Check,
    FileUp,
    GripVertical,
    Hash,
    ListChecks,
    Pencil,
    Plus,
    Save,
    SlidersHorizontal,
    TextCursorInput,
    Trash2,
    Type,
    X,
} from '@lucide/vue';

import {
    computed,
    ref,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface Option {
    id: number;
    label: string;
    value: string;
    extra_price: string;
    sort_order: number;
    active: boolean;
}

interface ProductField {
    id: number;
    catalog_product_id: number;
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
    sort_order: number;
    active: boolean;
    options: Option[];
}

interface Product {
    id: number;
    code: string;
    name: string;
    slug: string;
    quote_mode: string;
    measurement_unit: string | null;

    category: {
        id: number;
        name: string;
    } | null;

    fields: ProductField[];
}

interface FormOption {
    label: string;
    value: string;
    extra_price: string;
    active: boolean;
}

interface SharedProps {
    flash?: {
        success?: string | null;
    };

    [key: string]: unknown;
}

const props =
    defineProps<{
        product: Product;
    }>();

const page =
    usePage<SharedProps>();

const editingFieldId =
    ref<number | null>(
        null,
    );

const nextSortOrder =
    () => {
        if (
            props.product.fields.length ===
            0
        ) {
            return 10;
        }

        return Math.max(
            ...props.product.fields.map(
                (
                    field,
                ) =>
                    Number(
                        field.sort_order,
                    ),
            ),
        ) + 10;
    };

const form =
    useForm({
        label:
            '',

        field_type:
            'number',

        placeholder:
            '',

        help_text:
            '',

        unit:
            '',

        required:
            true,

        min_value:
            '',

        max_value:
            '',

        step:
            '',

        default_value:
            '',

        sort_order:
            nextSortOrder(),

        active:
            true,

        options:
            [] as FormOption[],
    });

const requiresOptions =
    computed(
        () =>
            [
                'select',
                'radio',
            ].includes(
                form.field_type,
            ),
    );

const isNumberField =
    computed(
        () =>
            form.field_type ===
            'number',
    );

const typeOptions = [
    {
        value:
            'number',

        label:
            'Número',

        description:
            'Cantidad, ancho, alto, largo, etc.',
    },

    {
        value:
            'text',

        label:
            'Texto corto',

        description:
            'Nombre, color personalizado, texto breve.',
    },

    {
        value:
            'textarea',

        label:
            'Texto largo',

        description:
            'Indicaciones u observaciones.',
    },

    {
        value:
            'select',

        label:
            'Lista',

        description:
            'El cliente selecciona una opción.',
    },

    {
        value:
            'radio',

        label:
            'Opciones visibles',

        description:
            'Una opción entre varias.',
    },

    {
        value:
            'checkbox',

        label:
            'Casilla',

        description:
            'Sí / No para un complemento.',
    },

    {
        value:
            'file',

        label:
            'Archivo',

        description:
            'Diseño, logotipo o referencia.',
    },
];

const fieldIcon =
    (
        type:
            string,
    ) => {
        if (
            type ===
            'number'
        ) {
            return Hash;
        }

        if (
            type ===
                'select'
            ||
            type ===
                'radio'
        ) {
            return ListChecks;
        }

        if (
            type ===
            'file'
        ) {
            return FileUp;
        }

        if (
            type ===
            'checkbox'
        ) {
            return Check;
        }

        if (
            type ===
            'textarea'
        ) {
            return Type;
        }

        return TextCursorInput;
    };

const typeLabel =
    (
        type:
            string,
    ) => {
        return typeOptions.find(
            (
                option,
            ) =>
                option.value ===
                type,
        )?.label ?? type;
    };

const resetForm =
    () => {
        editingFieldId.value =
            null;

        form.reset();

        form.clearErrors();

        form.label =
            '';

        form.field_type =
            'number';

        form.placeholder =
            '';

        form.help_text =
            '';

        form.unit =
            '';

        form.required =
            true;

        form.min_value =
            '';

        form.max_value =
            '';

        form.step =
            '';

        form.default_value =
            '';

        form.sort_order =
            nextSortOrder();

        form.active =
            true;

        form.options =
            [];
    };

const editField =
    (
        field:
            ProductField,
    ) => {
        editingFieldId.value =
            field.id;

        form.clearErrors();

        form.label =
            field.label;

        form.field_type =
            field.field_type;

        form.placeholder =
            field.placeholder
            ?? '';

        form.help_text =
            field.help_text
            ?? '';

        form.unit =
            field.unit
            ?? '';

        form.required =
            field.required;

        form.min_value =
            field.min_value
            ?? '';

        form.max_value =
            field.max_value
            ?? '';

        form.step =
            field.step
            ?? '';

        form.default_value =
            field.default_value
            ?? '';

        form.sort_order =
            field.sort_order;

        form.active =
            field.active;

        form.options =
            field.options.map(
                (
                    option,
                ) => ({
                    label:
                        option.label,

                    value:
                        option.value,

                    extra_price:
                        option.extra_price
                        ?? '0',

                    active:
                        option.active,
                }),
            );

        document
            .getElementById(
                'field-builder',
            )
            ?.scrollIntoView({
                behavior:
                    'smooth',

                block:
                    'start',
            });
    };

const addOption =
    () => {
        form.options.push({
            label:
                '',

            value:
                '',

            extra_price:
                '0',

            active:
                true,
        });
    };

const removeOption =
    (
        index:
            number,
    ) => {
        form.options.splice(
            index,
            1,
        );
    };

const submit =
    () => {
        form.transform(
            (
                data,
            ) => ({
                label:
                    data.label,

                field_type:
                    data.field_type,

                placeholder:
                    data.placeholder
                    || null,

                help_text:
                    data.help_text
                    || null,

                unit:
                    data.unit
                    || null,

                required:
                    Boolean(
                        data.required,
                    ),

                min_value:
                    data.min_value !== ''
                        ? Number(
                            data.min_value,
                        )
                        : null,

                max_value:
                    data.max_value !== ''
                        ? Number(
                            data.max_value,
                        )
                        : null,

                step:
                    data.step !== ''
                        ? Number(
                            data.step,
                        )
                        : null,

                default_value:
                    data.default_value
                    || null,

                sort_order:
                    Number(
                        data.sort_order,
                    ),

                active:
                    Boolean(
                        data.active,
                    ),

                options:
                    data.options.map(
                        (
                            option,
                        ) => ({
                            label:
                                option.label,

                            value:
                                option.value,

                            extra_price:
                                option.extra_price !== ''
                                    ? Number(
                                        option.extra_price,
                                    )
                                    : 0,

                            active:
                                Boolean(
                                    option.active,
                                ),
                        }),
                    ),
            }),
        );

        if (
            editingFieldId.value !==
            null
        ) {
            form.put(
                `/editor-preview/catalogo/productos/${props.product.id}/campos/${editingFieldId.value}`,
                {
                    preserveScroll:
                        true,

                    onSuccess:
                        resetForm,
                },
            );

            return;
        }

        form.post(
            `/editor-preview/catalogo/productos/${props.product.id}/campos`,
            {
                preserveScroll:
                    true,

                onSuccess:
                    resetForm,
            },
        );
    };

const deleteField =
    (
        field:
            ProductField,
    ) => {
        const confirmed =
            window.confirm(
                `¿Eliminar el campo "${field.label}"?`,
            );

        if (
            !confirmed
        ) {
            return;
        }

        router.delete(
            `/editor-preview/catalogo/productos/${props.product.id}/campos/${field.id}`,
            {
                preserveScroll:
                    true,

                onSuccess:
                    () => {
                        if (
                            editingFieldId.value ===
                            field.id
                        ) {
                            resetForm();
                        }
                    },
            },
        );
    };
</script>

<template>
    <Head
        :title="`Cotización · ${product.name}`"
    />

    <EditorLayout
        title="Constructor de cotización"
        eyebrow="Catálogo ADN"
    >
        <div
            v-if="page.props.flash?.success"
            class="mb-5 flex items-center gap-3 rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-turquoise)]"
        >
            <Check
                class="h-4 w-4"
            />

            {{ page.props.flash.success }}
        </div>

        <!-- Encabezado -->

        <section
            class="flex flex-col justify-between gap-5 lg:flex-row lg:items-center"
        >
            <div
                class="flex min-w-0 items-center gap-4"
            >
                <Link
                    :href="`/editor-preview/catalogo/productos/${product.id}/editar`"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/[0.07] text-white/35 transition hover:bg-white/[0.04] hover:text-white"
                >
                    <ArrowLeft
                        class="h-4 w-4"
                    />
                </Link>

                <div
                    class="min-w-0"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-coral)]"
                    >
                        {{ product.code }}
                    </p>

                    <h1
                        class="mt-1 truncate text-2xl font-black tracking-[-0.035em] sm:text-3xl"
                    >
                        {{ product.name }}
                    </h1>

                    <p
                        class="mt-1 text-xs text-white/30"
                    >
                        {{
                            product.category?.name
                            ?? 'Sin categoría'
                        }}
                        ·
                        {{ product.quote_mode }}
                    </p>
                </div>
            </div>

            <div
                class="shrink-0 rounded-xl border border-[var(--adn-yellow-border)] bg-[var(--adn-yellow-soft)] px-4 py-3"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-[var(--adn-yellow)]"
                >
                    {{
                        product.fields.length
                    }}
                    campos configurados
                </p>
            </div>
        </section>

        <section
            class="mt-6 grid min-w-0 gap-5 xl:grid-cols-[minmax(0,1fr)_minmax(360px,470px)]"
        >
            <!-- Lista actual -->

            <div
                class="min-w-0"
            >
                <section
                    class="adn-panel overflow-hidden rounded-[22px]"
                >
                    <div
                        class="flex items-center justify-between border-b border-[var(--adn-border)] px-5 py-5 sm:px-6"
                    >
                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                Formulario del cliente
                            </h2>

                            <p
                                class="mt-1 text-[10px] text-white/28"
                            >
                                Estos campos aparecerán al solicitar una cotización.
                            </p>
                        </div>

                        <SlidersHorizontal
                            class="h-5 w-5 shrink-0 text-[var(--adn-coral)]"
                        />
                    </div>

                    <div
                        v-if="product.fields.length"
                        class="divide-y divide-white/[0.04]"
                    >
                        <article
                            v-for="field in product.fields"
                            :key="field.id"
                            class="group flex min-w-0 items-start gap-4 px-5 py-5 transition hover:bg-white/[0.015] sm:px-6"
                        >
                            <div
                                class="mt-1 shrink-0 text-white/12"
                            >
                                <GripVertical
                                    class="h-5 w-5"
                                />
                            </div>

                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[var(--adn-coral-soft)] text-[var(--adn-coral)]"
                            >
                                <component
                                    :is="
                                        fieldIcon(
                                            field.field_type,
                                        )
                                    "
                                    class="h-5 w-5"
                                />
                            </div>

                            <div
                                class="min-w-0 flex-1"
                            >
                                <div
                                    class="flex flex-wrap items-center gap-2"
                                >
                                    <p
                                        class="truncate text-sm font-black text-white/75"
                                    >
                                        {{ field.label }}
                                    </p>

                                    <Asterisk
                                        v-if="field.required"
                                        class="h-3.5 w-3.5 shrink-0 text-[var(--adn-coral)]"
                                    />

                                    <span
                                        v-if="!field.active"
                                        class="shrink-0 rounded-md border border-white/[0.05] bg-white/[0.02] px-1.5 py-0.5 text-[7px] font-black uppercase text-white/25"
                                    >
                                        Inactivo
                                    </span>
                                </div>

                                <div
                                    class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-[10px] text-white/25"
                                >
                                    <span>
                                        {{
                                            typeLabel(
                                                field.field_type,
                                            )
                                        }}
                                    </span>

                                    <span>
                                        {{
                                            field.field_key
                                        }}
                                    </span>

                                    <span
                                        v-if="field.unit"
                                    >
                                        Unidad:
                                        {{ field.unit }}
                                    </span>

                                    <span
                                        v-if="field.options.length"
                                    >
                                        {{
                                            field.options.length
                                        }}
                                        opciones
                                    </span>
                                </div>

                                <p
                                    v-if="field.help_text"
                                    class="mt-2 text-xs leading-5 text-white/32"
                                >
                                    {{ field.help_text }}
                                </p>
                            </div>

                            <div
                                class="flex shrink-0 gap-2"
                            >
                                <button
                                    type="button"
                                    title="Editar"
                                    class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/[0.06] text-white/35 transition hover:border-[var(--adn-turquoise-border)] hover:bg-[var(--adn-turquoise-soft)] hover:text-[var(--adn-turquoise)]"
                                    @click="
                                        editField(
                                            field,
                                        )
                                    "
                                >
                                    <Pencil
                                        class="h-4 w-4"
                                    />
                                </button>

                                <button
                                    type="button"
                                    title="Eliminar"
                                    class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/[0.06] text-white/35 transition hover:border-[var(--adn-coral-border)] hover:bg-[var(--adn-coral-soft)] hover:text-[var(--adn-coral)]"
                                    @click="
                                        deleteField(
                                            field,
                                        )
                                    "
                                >
                                    <Trash2
                                        class="h-4 w-4"
                                    />
                                </button>
                            </div>
                        </article>
                    </div>

                    <div
                        v-else
                        class="flex min-h-72 flex-col items-center justify-center px-6 py-12 text-center"
                    >
                        <SlidersHorizontal
                            class="h-10 w-10 text-white/12"
                        />

                        <p
                            class="mt-4 text-sm font-black text-white/55"
                        >
                            Este producto todavía no pregunta nada
                        </p>

                        <p
                            class="mt-1 max-w-md text-xs leading-5 text-white/25"
                        >
                            Crea los datos que necesitas conocer antes de preparar la cotización.
                        </p>
                    </div>
                </section>
            </div>

            <!-- Constructor -->

            <aside
                id="field-builder"
                class="min-w-0 scroll-mt-28"
            >
                <form
                    class="adn-panel min-w-0 overflow-hidden rounded-[22px] p-5 sm:p-6"
                    @submit.prevent="submit"
                >
                    <div
                        class="flex min-w-0 items-start justify-between gap-4"
                    >
                        <div
                            class="min-w-0"
                        >
                            <p
                                class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-orange)]"
                            >
                                {{
                                    editingFieldId
                                        ? 'Editando campo'
                                        : 'Nuevo campo'
                                }}
                            </p>

                            <h2
                                class="mt-1 truncate text-lg font-black"
                            >
                                {{
                                    editingFieldId
                                        ? form.label || 'Campo'
                                        : 'Agregar pregunta'
                                }}
                            </h2>
                        </div>

                        <button
                            v-if="editingFieldId"
                            type="button"
                            title="Cancelar edición"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-white/[0.06] text-white/30 hover:bg-white/[0.04] hover:text-white"
                            @click="resetForm"
                        >
                            <X
                                class="h-4 w-4"
                            />
                        </button>
                    </div>

                    <div
                        class="mt-6 min-w-0 space-y-5"
                    >
                        <label
                            class="block min-w-0"
                        >
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Pregunta / etiqueta
                            </span>

                            <input
                                v-model="form.label"
                                type="text"
                                maxlength="180"
                                placeholder="Ej. Ancho"
                                class="h-11 w-full min-w-0 rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/18 focus:border-[var(--adn-turquoise-border)]"
                            >

                            <p
                                v-if="form.errors.label"
                                class="mt-2 text-xs font-bold text-[var(--adn-coral)]"
                            >
                                {{ form.errors.label }}
                            </p>
                        </label>

                        <label
                            class="block min-w-0"
                        >
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Tipo de respuesta
                            </span>

                            <select
                                v-model="form.field_type"
                                class="h-11 w-full min-w-0 rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white/70 outline-none"
                            >
                                <option
                                    v-for="option in typeOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>
                            </select>

                            <p
                                class="mt-2 text-[10px] leading-4 text-white/25"
                            >
                                {{
                                    typeOptions.find(
                                        option =>
                                            option.value ===
                                            form.field_type,
                                    )?.description
                                }}
                            </p>
                        </label>

                        <div
                            class="grid min-w-0 gap-4 sm:grid-cols-2"
                        >
                            <label
                                class="min-w-0"
                            >
                                <span
                                    class="mb-2 block text-xs font-black text-white/55"
                                >
                                    Unidad
                                </span>

                                <input
                                    v-model="form.unit"
                                    type="text"
                                    maxlength="50"
                                    placeholder="pulgadas, unidades..."
                                    class="h-11 w-full min-w-0 rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/18"
                                >
                            </label>

                            <label
                                class="min-w-0"
                            >
                                <span
                                    class="mb-2 block text-xs font-black text-white/55"
                                >
                                    Orden
                                </span>

                                <input
                                    v-model.number="form.sort_order"
                                    type="number"
                                    min="0"
                                    step="1"
                                    class="h-11 w-full min-w-0 rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none"
                                >
                            </label>
                        </div>

                        <label
                            class="block min-w-0"
                        >
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Placeholder
                            </span>

                            <input
                                v-model="form.placeholder"
                                type="text"
                                maxlength="255"
                                placeholder="Ej. Ingresa el ancho"
                                class="h-11 w-full min-w-0 rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/18"
                            >
                        </label>

                        <label
                            class="block min-w-0"
                        >
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Texto de ayuda
                            </span>

                            <textarea
                                v-model="form.help_text"
                                rows="3"
                                maxlength="1000"
                                placeholder="Ej. Escribe la medida en pulgadas."
                                class="w-full min-w-0 resize-y rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm leading-5 text-white outline-none placeholder:text-white/18"
                            />
                        </label>

                        <!-- Validaciones numéricas -->

                        <div
                            v-if="isNumberField"
                            class="min-w-0 rounded-2xl border border-[var(--adn-orange-border)] bg-[var(--adn-orange-soft)] p-4"
                        >
                            <p
                                class="text-[9px] font-black uppercase tracking-[0.13em] text-[var(--adn-orange)]"
                            >
                                Validación numérica
                            </p>

                            <div
                                class="mt-4 grid min-w-0 gap-3 sm:grid-cols-3"
                            >
                                <label
                                    class="min-w-0"
                                >
                                    <span
                                        class="mb-1.5 block text-[9px] font-black uppercase text-white/30"
                                    >
                                        Mínimo
                                    </span>

                                    <input
                                        v-model="form.min_value"
                                        type="number"
                                        step="any"
                                        class="h-10 w-full min-w-0 rounded-xl border border-white/[0.07] bg-black/15 px-3 text-xs text-white outline-none"
                                    >
                                </label>

                                <label
                                    class="min-w-0"
                                >
                                    <span
                                        class="mb-1.5 block text-[9px] font-black uppercase text-white/30"
                                    >
                                        Máximo
                                    </span>

                                    <input
                                        v-model="form.max_value"
                                        type="number"
                                        step="any"
                                        class="h-10 w-full min-w-0 rounded-xl border border-white/[0.07] bg-black/15 px-3 text-xs text-white outline-none"
                                    >
                                </label>

                                <label
                                    class="min-w-0"
                                >
                                    <span
                                        class="mb-1.5 block text-[9px] font-black uppercase text-white/30"
                                    >
                                        Incremento
                                    </span>

                                    <input
                                        v-model="form.step"
                                        type="number"
                                        min="0"
                                        step="any"
                                        class="h-10 w-full min-w-0 rounded-xl border border-white/[0.07] bg-black/15 px-3 text-xs text-white outline-none"
                                    >
                                </label>
                            </div>
                        </div>

                        <!-- Opciones -->

                        <div
                            v-if="requiresOptions"
                            class="min-w-0 overflow-hidden rounded-2xl border border-[var(--adn-yellow-border)] bg-[var(--adn-yellow-soft)] p-4"
                        >
                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div
                                    class="min-w-0"
                                >
                                    <p
                                        class="text-[9px] font-black uppercase tracking-[0.13em] text-[var(--adn-yellow)]"
                                    >
                                        Opciones
                                    </p>

                                    <p
                                        class="mt-1 text-[10px] leading-4 text-white/30"
                                    >
                                        Materiales, tamaños, acabados, colores, etc.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    class="flex h-9 shrink-0 items-center justify-center gap-2 rounded-xl border border-[var(--adn-yellow-border)] px-3 text-[9px] font-black uppercase text-[var(--adn-yellow)]"
                                    @click="addOption"
                                >
                                    <Plus
                                        class="h-3.5 w-3.5"
                                    />

                                    Agregar
                                </button>
                            </div>

                            <div
                                v-if="form.options.length"
                                class="mt-4 min-w-0 space-y-3"
                            >
                                <div
                                    v-for="(
                                        option,
                                        index
                                    ) in form.options"
                                    :key="index"
                                    class="min-w-0 rounded-xl border border-white/[0.06] bg-black/15 p-3"
                                >
                                    <!-- Etiqueta y valor -->

                                    <div
                                        class="grid min-w-0 gap-2 sm:grid-cols-2"
                                    >
                                        <label
                                            class="min-w-0"
                                        >
                                            <span
                                                class="mb-1.5 block text-[8px] font-black uppercase tracking-[0.08em] text-white/25"
                                            >
                                                Etiqueta
                                            </span>

                                            <input
                                                v-model="option.label"
                                                type="text"
                                                placeholder="Ej. Sin acabado"
                                                class="h-10 w-full min-w-0 rounded-lg border border-white/[0.06] bg-black/20 px-3 text-xs text-white outline-none placeholder:text-white/18"
                                            >
                                        </label>

                                        <label
                                            class="min-w-0"
                                        >
                                            <span
                                                class="mb-1.5 block text-[8px] font-black uppercase tracking-[0.08em] text-white/25"
                                            >
                                                Valor
                                            </span>

                                            <input
                                                v-model="option.value"
                                                type="text"
                                                placeholder="Automático"
                                                class="h-10 w-full min-w-0 rounded-lg border border-white/[0.06] bg-black/20 px-3 text-xs text-white outline-none placeholder:text-white/18"
                                            >
                                        </label>
                                    </div>

                                    <!-- Precio y acciones -->

                                    <div
                                        class="mt-3 flex min-w-0 items-end gap-2"
                                    >
                                        <label
                                            class="min-w-0 flex-1"
                                        >
                                            <span
                                                class="mb-1.5 block text-[8px] font-black uppercase tracking-[0.08em] text-white/25"
                                            >
                                                Precio extra
                                            </span>

                                            <div
                                                class="relative"
                                            >
                                                <span
                                                    class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-black text-white/25"
                                                >
                                                    L
                                                </span>

                                                <input
                                                    v-model="option.extra_price"
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    placeholder="0.00"
                                                    class="h-10 w-full min-w-0 rounded-lg border border-white/[0.06] bg-black/20 pl-7 pr-3 text-xs text-white outline-none placeholder:text-white/18"
                                                >
                                            </div>
                                        </label>

                                        <button
                                            type="button"
                                            title="Eliminar opción"
                                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-white/[0.05] text-white/25 transition hover:border-[var(--adn-coral-border)] hover:bg-[var(--adn-coral-soft)] hover:text-[var(--adn-coral)]"
                                            @click="
                                                removeOption(
                                                    index,
                                                )
                                            "
                                        >
                                            <Trash2
                                                class="h-4 w-4"
                                            />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button
                                v-else
                                type="button"
                                class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl border border-dashed border-white/[0.08] py-4 text-[10px] font-black text-white/30"
                                @click="addOption"
                            >
                                <Plus
                                    class="h-4 w-4"
                                />

                                Crear primera opción
                            </button>
                        </div>

                        <!-- General -->

                        <div
                            class="grid min-w-0 gap-3 sm:grid-cols-2"
                        >
                            <label
                                class="flex min-w-0 cursor-pointer items-center justify-between gap-3 rounded-xl border border-white/[0.06] bg-black/10 p-3.5"
                            >
                                <div
                                    class="min-w-0"
                                >
                                    <p
                                        class="text-xs font-black text-white/60"
                                    >
                                        Obligatorio
                                    </p>

                                    <p
                                        class="mt-0.5 text-[9px] text-white/25"
                                    >
                                        El cliente debe responderlo.
                                    </p>
                                </div>

                                <input
                                    v-model="form.required"
                                    type="checkbox"
                                    class="h-4 w-4 shrink-0 accent-[#E84657]"
                                >
                            </label>

                            <label
                                class="flex min-w-0 cursor-pointer items-center justify-between gap-3 rounded-xl border border-white/[0.06] bg-black/10 p-3.5"
                            >
                                <div
                                    class="min-w-0"
                                >
                                    <p
                                        class="text-xs font-black text-white/60"
                                    >
                                        Activo
                                    </p>

                                    <p
                                        class="mt-0.5 text-[9px] text-white/25"
                                    >
                                        Mostrar al cliente.
                                    </p>
                                </div>

                                <input
                                    v-model="form.active"
                                    type="checkbox"
                                    class="h-4 w-4 shrink-0 accent-[#0FA7B4]"
                                >
                            </label>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="adn-primary-button flex h-12 w-full items-center justify-center gap-2 rounded-xl text-sm font-black disabled:opacity-50"
                        >
                            <Save
                                class="h-4 w-4"
                            />

                            {{
                                form.processing
                                    ? 'Guardando...'
                                    : editingFieldId
                                        ? 'Guardar cambios'
                                        : 'Agregar campo'
                            }}
                        </button>
                    </div>
                </form>
            </aside>
        </section>
    </EditorLayout>
</template>