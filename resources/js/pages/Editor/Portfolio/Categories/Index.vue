<script setup lang="ts">
import {
    Head,
    Link,
    router,
    usePage,
} from '@inertiajs/vue3';

import {
    ArrowLeft,
    FolderTree,
    Pencil,
    Plus,
    Trash2,
} from '@lucide/vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface Category {
    id: number;
    slug: string;
    name: string;
    description: string | null;
    sort_order: number;
    active: boolean;
    projects_count: number;
}

interface SharedProps {
    flash?: {
        success?: string;
        error?: string;
    };

    [key: string]: unknown;
}

defineProps<{
    categories: Category[];
}>();

const page =
    usePage<SharedProps>();

const archive =
    (
        category:
            Category,
    ): void => {
        if (
            !window.confirm(
                `¿Archivar la categoría "${category.name}"?`,
            )
        ) {
            return;
        }

        router.delete(
            `/editor-preview/portafolio/categorias/${category.slug}`,
            {
                preserveScroll:
                    true,
            },
        );
    };
</script>

<template>
    <Head title="Categorías de portafolio" />

    <EditorLayout
        title="Categorías"
        eyebrow="Portafolio"
    >
        <div
            v-if="page.props.flash?.success"
            class="mb-5 rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-turquoise)]"
        >
            {{ page.props.flash.success }}
        </div>

        <div
            v-if="page.props.flash?.error"
            class="mb-5 rounded-2xl border border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-coral)]"
        >
            {{ page.props.flash.error }}
        </div>

        <div
            class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"
        >
            <div>
                <Link
                    href="/editor-preview/portafolio/proyectos"
                    class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.12em] text-white/25 hover:text-[var(--adn-turquoise)]"
                >
                    <ArrowLeft class="h-3.5 w-3.5" />
                    Portafolio
                </Link>

                <h1
                    class="mt-3 text-3xl font-black tracking-[-0.04em] text-white"
                >
                    Categorías
                </h1>

                <p
                    class="mt-2 max-w-xl text-xs leading-6 text-white/30"
                >
                    Organiza los proyectos sin convertir el portafolio en una lista difícil de explorar.
                </p>
            </div>

            <Link
                href="/editor-preview/portafolio/categorias/crear"
                class="adn-primary-button inline-flex h-11 items-center justify-center gap-2 rounded-xl px-4 text-xs font-black"
            >
                <Plus class="h-4 w-4" />
                Nueva categoría
            </Link>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <article
                v-for="category in categories"
                :key="category.id"
                class="adn-panel rounded-[22px] p-5"
            >
                <div class="flex items-start justify-between gap-4">
                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]"
                    >
                        <FolderTree class="h-5 w-5" />
                    </span>

                    <span
                        class="rounded-lg border px-2 py-1 text-[8px] font-black uppercase"
                        :class="
                            category.active
                                ? 'border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]'
                                : 'border-white/[0.06] bg-white/[0.02] text-white/25'
                        "
                    >
                        {{
                            category.active
                                ? 'Activa'
                                : 'Inactiva'
                        }}
                    </span>
                </div>

                <h2
                    class="mt-5 text-lg font-black text-white/85"
                >
                    {{ category.name }}
                </h2>

                <p
                    class="mt-1 text-[9px] font-bold text-white/20"
                >
                    /{{ category.slug }}
                </p>

                <p
                    v-if="category.description"
                    class="mt-3 text-xs leading-6 text-white/30"
                >
                    {{ category.description }}
                </p>

                <div
                    class="mt-5 flex items-center justify-between border-t border-white/[0.06] pt-4"
                >
                    <span class="text-[10px] font-black text-white/25">
                        {{ category.projects_count }}
                        proyectos
                    </span>

                    <div class="flex gap-2">
                        <Link
                            :href="`/editor-preview/portafolio/categorias/${category.slug}/editar`"
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/[0.06] text-white/40 hover:text-[var(--adn-turquoise)]"
                        >
                            <Pencil class="h-4 w-4" />
                        </Link>

                        <button
                            type="button"
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] text-[var(--adn-coral)]"
                            @click="archive(category)"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </article>
        </div>
    </EditorLayout>
</template>