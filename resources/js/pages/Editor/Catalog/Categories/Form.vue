<script setup lang="ts">
import {
    Head,
    Link,
    useForm,
    usePage,
} from '@inertiajs/vue3';

import {
    ArrowLeft,
    Check,
    FolderTree,
    Save,
    SearchCheck,
} from '@lucide/vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface ParentCategory {
    id: number;
    name: string;
    code: string;
    parent_id: number | null;
}

interface Category {
    id: number;
    code: string;
    slug: string;
    name: string;
    description: string | null;
    parent_id: number | null;
    status: string;
    sort_order: number;
    meta_title: string | null;
    meta_description: string | null;
    products_count: number;
    children_count: number;
}

interface SharedPageProps {
    flash?: {
        success?: string | null;
        error?: string | null;
    };

    [key: string]: unknown;
}

const props =
    defineProps<{
        mode: 'create' | 'edit';
        category: Category | null;
        parentCategories: ParentCategory[];
    }>();

const page =
    usePage<SharedPageProps>();

const form =
    useForm({
        name:
            props.category?.name
            ?? '',

        description:
            props.category?.description
            ?? '',

        parent_id:
            props.category?.parent_id
                ? String(
                    props.category.parent_id,
                )
                : '',

        status:
            props.category?.status
            ?? 'published',

        sort_order:
            props.category?.sort_order
            ?? 0,

        meta_title:
            props.category?.meta_title
            ?? '',

        meta_description:
            props.category?.meta_description
            ?? '',
    });

const submit =
    () => {
        form.transform(
            (
                data,
            ) => ({
                ...data,

                parent_id:
                    data.parent_id
                        ? Number(
                            data.parent_id,
                        )
                        : null,

                sort_order:
                    Number(
                        data.sort_order,
                    ),
            }),
        );

        if (
            props.mode ===
                'create'
        ) {
            form.post(
                '/editor-preview/catalogo/categorias',
            );

            return;
        }

        form.put(
            `/editor-preview/catalogo/categorias/${props.category?.id}`,
            {
                preserveScroll:
                    true,
            },
        );
    };
</script>

<template>
    <Head
        :title="
            mode === 'create'
                ? 'Nueva categoría'
                : `Editar ${category?.name}`
        "
    />

    <EditorLayout
        :title="
            mode === 'create'
                ? 'Nueva categoría'
                : 'Editar categoría'
        "
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

        <section
            class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
        >
            <div
                class="flex items-center gap-4"
            >
                <Link
                    href="/editor-preview/catalogo/categorias"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/[0.07] text-white/35 transition hover:bg-white/[0.04] hover:text-white"
                >
                    <ArrowLeft
                        class="h-4 w-4"
                    />
                </Link>

                <div>
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-orange)]"
                    >
                        {{
                            mode === 'create'
                                ? 'Nueva estructura'
                                : category?.code
                        }}
                    </p>

                    <h1
                        class="mt-1 text-2xl font-black tracking-[-0.035em] sm:text-3xl"
                    >
                        {{
                            mode === 'create'
                                ? 'Crear categoría'
                                : category?.name
                        }}
                    </h1>
                </div>
            </div>

            <div
                v-if="category"
                class="flex gap-2"
            >
                <span
                    class="rounded-xl border border-[var(--adn-orange-border)] bg-[var(--adn-orange-soft)] px-3 py-2 text-[9px] font-black uppercase text-[var(--adn-orange)]"
                >
                    {{
                        category.products_count
                    }}
                    productos
                </span>

                <span
                    class="rounded-xl border border-[var(--adn-yellow-border)] bg-[var(--adn-yellow-soft)] px-3 py-2 text-[9px] font-black uppercase text-[var(--adn-yellow)]"
                >
                    {{
                        category.children_count
                    }}
                    subcategorías
                </span>
            </div>
        </section>

        <form
            class="mt-6 grid gap-5 xl:grid-cols-[1fr_370px]"
            @submit.prevent="submit"
        >
            <div
                class="space-y-5"
            >
                <section
                    class="adn-panel rounded-[22px] p-5 sm:p-6"
                >
                    <div
                        class="mb-6 flex items-center gap-3"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]"
                        >
                            <FolderTree
                                class="h-5 w-5"
                            />
                        </div>

                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                Información principal
                            </h2>

                            <p
                                class="mt-0.5 text-[10px] text-white/28"
                            >
                                Datos visibles y estructura del catálogo
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
                                Nombre de la categoría
                            </span>

                            <input
                                v-model="form.name"
                                type="text"
                                maxlength="180"
                                placeholder="Ej. Impresión gran formato"
                                class="h-12 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none transition placeholder:text-white/18 focus:border-[var(--adn-turquoise-border)]"
                            >

                            <p
                                v-if="form.errors.name"
                                class="mt-2 text-xs font-bold text-[var(--adn-coral)]"
                            >
                                {{ form.errors.name }}
                            </p>
                        </label>

                        <label>
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Descripción
                            </span>

                            <textarea
                                v-model="form.description"
                                rows="5"
                                maxlength="5000"
                                placeholder="Describe qué tipo de productos pertenecen a esta categoría..."
                                class="w-full resize-y rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm leading-6 text-white outline-none transition placeholder:text-white/18 focus:border-[var(--adn-turquoise-border)]"
                            />

                            <p
                                v-if="form.errors.description"
                                class="mt-2 text-xs font-bold text-[var(--adn-coral)]"
                            >
                                {{
                                    form.errors.description
                                }}
                            </p>
                        </label>

                        <div
                            class="grid gap-5 md:grid-cols-2"
                        >
                            <label>
                                <span
                                    class="mb-2 block text-xs font-black text-white/55"
                                >
                                    Categoría superior
                                </span>

                                <select
                                    v-model="form.parent_id"
                                    class="h-12 w-full rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white/70 outline-none focus:border-[var(--adn-turquoise-border)]"
                                >
                                    <option
                                        value=""
                                    >
                                        Categoría principal
                                    </option>

                                    <option
                                        v-for="parent in parentCategories"
                                        :key="parent.id"
                                        :value="String(parent.id)"
                                    >
                                        {{
                                            parent.name
                                        }}
                                        —
                                        {{
                                            parent.code
                                        }}
                                    </option>
                                </select>

                                <p
                                    v-if="form.errors.parent_id"
                                    class="mt-2 text-xs font-bold text-[var(--adn-coral)]"
                                >
                                    {{
                                        form.errors.parent_id
                                    }}
                                </p>
                            </label>

                            <label>
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
                                    class="h-12 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none focus:border-[var(--adn-turquoise-border)]"
                                >

                                <p
                                    v-if="form.errors.sort_order"
                                    class="mt-2 text-xs font-bold text-[var(--adn-coral)]"
                                >
                                    {{
                                        form.errors.sort_order
                                    }}
                                </p>
                            </label>
                        </div>
                    </div>
                </section>

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
                                Información utilizada por buscadores
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
                                Título SEO
                            </span>

                            <input
                                v-model="form.meta_title"
                                type="text"
                                maxlength="255"
                                placeholder="Opcional"
                                class="h-12 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/18 focus:border-[var(--adn-yellow-border)]"
                            >
                        </label>

                        <label>
                            <span
                                class="mb-2 block text-xs font-black text-white/55"
                            >
                                Descripción SEO
                            </span>

                            <textarea
                                v-model="form.meta_description"
                                rows="4"
                                maxlength="1000"
                                placeholder="Descripción corta para resultados de búsqueda..."
                                class="w-full resize-y rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm leading-6 text-white outline-none placeholder:text-white/18 focus:border-[var(--adn-yellow-border)]"
                            />
                        </label>
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

                    <p
                        class="mt-1 text-[10px] leading-5 text-white/28"
                    >
                        Controla si la categoría está disponible
                        para el catálogo público.
                    </p>

                    <div
                        class="mt-5 grid gap-2"
                    >
                        <label
                            v-for="option in [
                                {
                                    value: 'published',
                                    label: 'Publicada',
                                    color: '#0FA7B4',
                                },
                                {
                                    value: 'draft',
                                    label: 'Borrador',
                                    color: '#F5C000',
                                },
                                {
                                    value: 'hidden',
                                    label: 'Oculta',
                                    color: '#E84657',
                                },
                            ]"
                            :key="option.value"
                            class="flex cursor-pointer items-center gap-3 rounded-xl border p-3.5 transition"
                            :class="
                                form.status === option.value
                                    ? 'border-white/[0.13] bg-white/[0.045]'
                                    : 'border-white/[0.055] bg-black/10 hover:bg-white/[0.02]'
                            "
                        >
                            <input
                                v-model="form.status"
                                type="radio"
                                :value="option.value"
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
                                {{ option.label }}
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

                    <p
                        v-if="form.errors.status"
                        class="mt-2 text-xs font-bold text-[var(--adn-coral)]"
                    >
                        {{ form.errors.status }}
                    </p>
                </section>

                <section
                    v-if="category"
                    class="adn-panel rounded-[22px] p-5"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.15em] text-white/24"
                    >
                        Identificación
                    </p>

                    <div
                        class="mt-4 space-y-3"
                    >
                        <div>
                            <p
                                class="text-[9px] uppercase text-white/20"
                            >
                                Código
                            </p>

                            <p
                                class="mt-1 text-xs font-black text-[var(--adn-orange)]"
                            >
                                {{ category.code }}
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
                                /catalogo/categoria/{{
                                    category.slug
                                }}
                            </p>
                        </div>
                    </div>
                </section>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="adn-primary-button flex h-12 w-full items-center justify-center gap-2 rounded-xl text-sm font-black disabled:cursor-wait disabled:opacity-50"
                >
                    <Save
                        class="h-4 w-4"
                    />

                    {{
                        form.processing
                            ? 'Guardando...'
                            : mode === 'create'
                                ? 'Crear categoría'
                                : 'Guardar cambios'
                    }}
                </button>
            </aside>
        </form>
    </EditorLayout>
</template>