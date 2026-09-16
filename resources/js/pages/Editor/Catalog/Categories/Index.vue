<script setup lang="ts">
import {
    Head,
    Link,
    router,
    usePage,
} from '@inertiajs/vue3';

import {
    ChevronLeft,
    ChevronRight,
    FolderPlus,
    FolderTree,
    Pencil,
    Search,
    Trash2,
} from '@lucide/vue';

import {
    reactive,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface ParentCategory {
    id: number;
    name: string;
}

interface Category {
    id: number;
    code: string;
    name: string;
    slug: string;
    description: string | null;
    status: string;
    sort_order: number;
    parent: ParentCategory | null;
    products_count: number;
    children_count: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface CategoriesPaginator {
    data: Category[];
    current_page: number;
    last_page: number;
    total: number;
    links: PaginationLink[];
}

interface Summary {
    total: number;
    published: number;
    draft: number;
    hidden: number;
}

interface Filters {
    search: string;
    status: string;
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
        categories: CategoriesPaginator;
        summary: Summary;
        filters: Filters;
    }>();

const page =
    usePage<SharedPageProps>();

const filterForm =
    reactive({
        search:
            props.filters.search ?? '',

        status:
            props.filters.status ?? '',
    });

const applyFilters =
    () => {
        router.get(
            '/editor-preview/catalogo/categorias',
            {
                search:
                    filterForm.search || undefined,

                status:
                    filterForm.status || undefined,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    };

const clearFilters =
    () => {
        filterForm.search =
            '';

        filterForm.status =
            '';

        router.get(
            '/editor-preview/catalogo/categorias',
            {},
            {
                replace: true,
            },
        );
    };

const deleteCategory =
    (
        category:
            Category,
    ) => {
        const confirmed =
            window.confirm(
                `¿Eliminar la categoría "${category.name}"?`,
            );

        if (
            !confirmed
        ) {
            return;
        }

        router.delete(
            `/editor-preview/catalogo/categorias/${category.id}`,
            {
                preserveScroll: true,
            },
        );
    };

const statusLabel =
    (
        status:
            string,
    ) => {
        const labels:
            Record<string, string> =
        {
            published:
                'Publicada',

            draft:
                'Borrador',

            hidden:
                'Oculta',
        };

        return labels[
            status
        ] ?? status;
    };

const statusClasses =
    (
        status:
            string,
    ) => {
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
</script>

<template>
    <Head
        title="Categorías del catálogo"
    />

    <EditorLayout
        title="Categorías"
        eyebrow="Catálogo ADN"
    >
        <div
            v-if="page.props.flash?.success"
            class="mb-5 rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-turquoise)]"
        >
            {{ page.props.flash.success }}
        </div>

        <div
            v-if="$page.props.errors?.delete"
            class="mb-5 rounded-2xl border border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-coral)]"
        >
            {{ $page.props.errors.delete }}
        </div>

        <section
            class="flex flex-col justify-between gap-5 sm:flex-row sm:items-center"
        >
            <div>
                <p
                    class="text-[9px] font-black uppercase tracking-[0.17em] text-[var(--adn-orange)]"
                >
                    Organización comercial
                </p>

                <h1
                    class="mt-2 text-3xl font-black tracking-[-0.04em]"
                >
                    Categorías del catálogo
                </h1>

                <p
                    class="mt-2 max-w-xl text-sm text-white/35"
                >
                    Organiza los productos que los clientes
                    encontrarán en el sitio web.
                </p>
            </div>

            <Link
                href="/editor-preview/catalogo/categorias/crear"
                class="adn-primary-button inline-flex h-11 items-center justify-center gap-2 rounded-xl px-5 text-xs font-black"
            >
                <FolderPlus
                    class="h-4 w-4"
                />

                Nueva categoría
            </Link>
        </section>

        <section
            class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4"
        >
            <article
                class="adn-panel rounded-[18px] p-4"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/25"
                >
                    Total
                </p>

                <p
                    class="mt-2 text-2xl font-black text-[var(--adn-turquoise)]"
                >
                    {{ summary.total }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-4"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/25"
                >
                    Publicadas
                </p>

                <p
                    class="mt-2 text-2xl font-black text-[var(--adn-turquoise)]"
                >
                    {{ summary.published }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-4"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/25"
                >
                    Borradores
                </p>

                <p
                    class="mt-2 text-2xl font-black text-[var(--adn-yellow)]"
                >
                    {{ summary.draft }}
                </p>
            </article>

            <article
                class="adn-panel rounded-[18px] p-4"
            >
                <p
                    class="text-[9px] font-black uppercase tracking-[0.12em] text-white/25"
                >
                    Ocultas
                </p>

                <p
                    class="mt-2 text-2xl font-black text-[var(--adn-coral)]"
                >
                    {{ summary.hidden }}
                </p>
            </article>
        </section>

        <section
            class="adn-panel mt-5 rounded-[22px] p-4 sm:p-5"
        >
            <form
                class="grid gap-3 lg:grid-cols-[1fr_220px_auto]"
                @submit.prevent="applyFilters"
            >
                <label
                    class="relative"
                >
                    <Search
                        class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/25"
                    />

                    <input
                        v-model="filterForm.search"
                        type="search"
                        placeholder="Buscar por nombre, código o URL..."
                        class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 pl-11 pr-4 text-sm text-white outline-none transition placeholder:text-white/20 focus:border-[var(--adn-turquoise-border)]"
                    >
                </label>

                <select
                    v-model="filterForm.status"
                    class="h-11 rounded-xl border border-white/[0.07] bg-[#101719] px-4 text-sm text-white/70 outline-none focus:border-[var(--adn-turquoise-border)]"
                >
                    <option
                        value=""
                    >
                        Todos los estados
                    </option>

                    <option
                        value="published"
                    >
                        Publicadas
                    </option>

                    <option
                        value="draft"
                    >
                        Borradores
                    </option>

                    <option
                        value="hidden"
                    >
                        Ocultas
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
                        class="h-11 rounded-xl border border-white/[0.07] px-4 text-xs font-black text-white/40 hover:bg-white/[0.04]"
                        @click="clearFilters"
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
                v-if="categories.data.length"
                class="overflow-x-auto"
            >
                <table
                    class="w-full min-w-[920px]"
                >
                    <thead>
                        <tr
                            class="border-b border-[var(--adn-border)]"
                        >
                            <th
                                class="px-6 py-4 text-left text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                            >
                                Categoría
                            </th>

                            <th
                                class="px-4 py-4 text-left text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                            >
                                Categoría superior
                            </th>

                            <th
                                class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                            >
                                Productos
                            </th>

                            <th
                                class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                            >
                                Subcategorías
                            </th>

                            <th
                                class="px-4 py-4 text-center text-[9px] font-black uppercase tracking-[0.13em] text-white/22"
                            >
                                Estado
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
                            v-for="category in categories.data"
                            :key="category.id"
                            class="border-b border-white/[0.035] transition hover:bg-white/[0.018] last:border-0"
                        >
                            <td
                                class="px-6 py-4"
                            >
                                <div
                                    class="flex items-center gap-3"
                                >
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--adn-orange-soft)] text-[var(--adn-orange)]"
                                    >
                                        <FolderTree
                                            class="h-[18px] w-[18px]"
                                        />
                                    </div>

                                    <div>
                                        <p
                                            class="text-sm font-black text-white/80"
                                        >
                                            {{ category.name }}
                                        </p>

                                        <p
                                            class="mt-0.5 text-[10px] text-white/27"
                                        >
                                            {{ category.code }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td
                                class="px-4 py-4 text-xs text-white/40"
                            >
                                {{
                                    category.parent?.name
                                        ?? '—'
                                }}
                            </td>

                            <td
                                class="px-4 py-4 text-center text-sm font-black"
                            >
                                {{
                                    category.products_count
                                }}
                            </td>

                            <td
                                class="px-4 py-4 text-center text-sm font-black"
                            >
                                {{
                                    category.children_count
                                }}
                            </td>

                            <td
                                class="px-4 py-4 text-center"
                            >
                                <span
                                    class="inline-flex rounded-lg border px-2 py-1 text-[8px] font-black uppercase tracking-[0.08em]"
                                    :class="
                                        statusClasses(
                                            category.status,
                                        )
                                    "
                                >
                                    {{
                                        statusLabel(
                                            category.status,
                                        )
                                    }}
                                </span>
                            </td>

                            <td
                                class="px-6 py-4"
                            >
                                <div
                                    class="flex justify-end gap-2"
                                >
                                    <Link
                                        :href="`/editor-preview/catalogo/categorias/${category.id}/editar`"
                                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/[0.06] text-white/35 transition hover:border-[var(--adn-turquoise-border)] hover:bg-[var(--adn-turquoise-soft)] hover:text-[var(--adn-turquoise)]"
                                        title="Editar"
                                    >
                                        <Pencil
                                            class="h-4 w-4"
                                        />
                                    </Link>

                                    <button
                                        type="button"
                                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/[0.06] text-white/35 transition hover:border-[var(--adn-coral-border)] hover:bg-[var(--adn-coral-soft)] hover:text-[var(--adn-coral)]"
                                        title="Eliminar"
                                        @click="
                                            deleteCategory(
                                                category,
                                            )
                                        "
                                    >
                                        <Trash2
                                            class="h-4 w-4"
                                        />
                                    </button>
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
                <FolderTree
                    class="h-9 w-9 text-white/12"
                />

                <p
                    class="mt-4 text-sm font-black text-white/55"
                >
                    No encontramos categorías
                </p>

                <p
                    class="mt-1 text-xs text-white/25"
                >
                    Crea una nueva o modifica los filtros.
                </p>
            </div>

            <div
                v-if="categories.last_page > 1"
                class="flex items-center justify-between border-t border-[var(--adn-border)] px-6 py-4"
            >
                <p
                    class="text-[10px] text-white/28"
                >
                    Página
                    {{ categories.current_page }}
                    de
                    {{ categories.last_page }}
                    ·
                    {{ categories.total }}
                    registros
                </p>

                <div
                    class="flex items-center gap-1.5"
                >
                    <Link
                        v-for="link in categories.links"
                        :key="link.label"
                        :href="link.url ?? ''"
                        preserve-scroll
                        class="flex min-h-9 min-w-9 items-center justify-center rounded-lg border px-2 text-xs font-black"
                        :class="
                            link.active
                                ? 'border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]'
                                : link.url
                                    ? 'border-white/[0.06] text-white/35 hover:bg-white/[0.04]'
                                    : 'pointer-events-none border-white/[0.03] text-white/10'
                        "
                    >
                        <ChevronLeft
                            v-if="
                                link.label.includes(
                                    'Previous',
                                )
                            "
                            class="h-4 w-4"
                        />

                        <ChevronRight
                            v-else-if="
                                link.label.includes(
                                    'Next',
                                )
                            "
                            class="h-4 w-4"
                        />

                        <span
                            v-else
                            v-html="link.label"
                        />
                    </Link>
                </div>
            </div>
        </section>
    </EditorLayout>
</template>