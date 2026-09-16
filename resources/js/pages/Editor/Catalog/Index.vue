<script setup lang="ts">
import {
    Head,
    Link,
} from '@inertiajs/vue3';

import {
    ArrowRight,
    Boxes,
    FolderTree,
    PackagePlus,
    Sparkles,
    Star,
} from '@lucide/vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface Stats {
    categories: number;
    published_categories: number;
    products: number;
    published_products: number;
    featured_products: number;
}

interface Category {
    id: number;
    code: string;
    name: string;
    slug: string;
    status: string;
    products_count: number;
}

defineProps<{
    stats: Stats;
    categories: Category[];
}>();

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
</script>

<template>
    <Head
        title="Catálogo"
    />

    <EditorLayout
        title="Catálogo"
        eyebrow="Contenido comercial"
    >
        <section
            class="adn-panel relative overflow-hidden rounded-[26px] p-6 sm:p-8"
        >
            <div
                class="adn-brand-stripe absolute inset-x-0 top-0 h-[3px]"
            />

            <div
                class="relative flex flex-col justify-between gap-6 lg:flex-row lg:items-center"
            >
                <div>
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-[var(--adn-orange-border)] bg-[var(--adn-orange-soft)] px-3 py-1.5"
                    >
                        <Sparkles
                            class="h-3.5 w-3.5 text-[var(--adn-orange)]"
                        />

                        <span
                            class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-orange)]"
                        >
                            Catálogo web
                        </span>
                    </div>

                    <h1
                        class="mt-5 text-3xl font-black tracking-[-0.04em] sm:text-4xl"
                    >
                        Productos y
                        <span
                            class="text-[var(--adn-turquoise)]"
                        >
                            categorías ADN.
                        </span>
                    </h1>

                    <p
                        class="mt-3 max-w-2xl text-sm leading-6 text-white/40"
                    >
                        Organiza lo que los clientes podrán
                        explorar y posteriormente solicitar
                        en cotización desde el sitio web.
                    </p>
                </div>

                <Link
                    href="/editor-preview/catalogo/categorias"
                    class="adn-primary-button inline-flex h-11 items-center justify-center gap-2 rounded-xl px-5 text-xs font-black"
                >
                    Gestionar categorías

                    <ArrowRight
                        class="h-4 w-4"
                    />
                </Link>
            </div>
        </section>

        <section
            class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-5"
        >
            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <FolderTree
                    class="h-5 w-5 text-[var(--adn-turquoise)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{ stats.categories }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Categorías
                </p>
            </article>

            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <FolderTree
                    class="h-5 w-5 text-[var(--adn-coral)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{
                        stats.published_categories
                    }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Categorías publicadas
                </p>
            </article>

            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <Boxes
                    class="h-5 w-5 text-[var(--adn-orange)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{ stats.products }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Productos
                </p>
            </article>

            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <PackagePlus
                    class="h-5 w-5 text-[var(--adn-yellow)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{
                        stats.published_products
                    }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Publicados
                </p>
            </article>

            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <Star
                    class="h-5 w-5 text-[var(--adn-yellow)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{
                        stats.featured_products
                    }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Destacados
                </p>
            </article>
        </section>

        <section
            class="adn-panel mt-5 overflow-hidden rounded-[22px]"
        >
            <div
                class="flex items-center justify-between border-b border-[var(--adn-border)] px-6 py-5"
            >
                <div>
                    <h2
                        class="text-sm font-black"
                    >
                        Categorías del catálogo
                    </h2>

                    <p
                        class="mt-1 text-[10px] text-white/30"
                    >
                        Vista rápida de la estructura comercial
                    </p>
                </div>

                <Link
                    href="/editor-preview/catalogo/categorias"
                    class="text-xs font-black text-[var(--adn-turquoise)]"
                >
                    Ver todas
                </Link>
            </div>

            <div
                v-if="categories.length"
                class="divide-y divide-white/[0.045]"
            >
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="flex items-center justify-between gap-4 px-6 py-4"
                >
                    <div>
                        <p
                            class="text-sm font-black text-white/80"
                        >
                            {{ category.name }}
                        </p>

                        <p
                            class="mt-1 text-[10px] text-white/28"
                        >
                            {{ category.code }}
                            ·
                            {{ category.products_count }}
                            productos
                        </p>
                    </div>

                    <span
                        class="rounded-lg border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-2 py-1 text-[8px] font-black uppercase tracking-[0.08em] text-[var(--adn-turquoise)]"
                    >
                        {{
                            statusLabel(
                                category.status,
                            )
                        }}
                    </span>
                </div>
            </div>

            <div
                v-else
                class="flex min-h-56 flex-col items-center justify-center px-6 py-10 text-center"
            >
                <FolderTree
                    class="h-8 w-8 text-white/15"
                />

                <p
                    class="mt-4 text-sm font-black text-white/55"
                >
                    Todavía no hay categorías
                </p>

                <p
                    class="mt-1 text-xs text-white/25"
                >
                    Empieza creando la estructura del catálogo.
                </p>

                <Link
                    href="/editor-preview/catalogo/categorias/crear"
                    class="adn-primary-button mt-5 inline-flex h-10 items-center rounded-xl px-4 text-xs font-black"
                >
                    Crear primera categoría
                </Link>
            </div>
        </section>
    </EditorLayout>
</template>