<script setup lang="ts">
import {
    Head,
    Link,
    router,
    usePage,
} from '@inertiajs/vue3';

import {
    BriefcaseBusiness,
    Eye,
    FolderTree,
    ImageIcon,
    Pencil,
    Plus,
    Search,
    Star,
    Trash2,
} from '@lucide/vue';

import {
    computed,
    ref,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface Media {
    id: number;
    url: string;
    alt_text: string | null;
}

interface Project {
    id: number;
    code: string;
    slug: string;
    title: string;
    client_name: string | null;
    excerpt: string | null;
    status: string;
    featured: boolean;
    sort_order: number;
    project_date: string | null;
    images_count: number;
    main_media: Media | null;

    category: {
        id: number;
        name: string;
        slug: string;
    } | null;
}

interface SharedProps {
    flash?: {
        success?: string;
        error?: string;
    };

    [key: string]: unknown;
}

const props =
    defineProps<{
        projects: Project[];
    }>();

const page =
    usePage<SharedProps>();

const search =
    ref('');

const filteredProjects =
    computed(
        (): Project[] => {
            const term =
                search.value
                    .trim()
                    .toLowerCase();

            if (
                !term
            ) {
                return props.projects;
            }

            return props.projects.filter(
                (
                    project,
                ) =>
                    project.title
                        .toLowerCase()
                        .includes(
                            term,
                        )
                    ||
                    (
                        project.client_name
                        ?? ''
                    )
                        .toLowerCase()
                        .includes(
                            term,
                        )
                    ||
                    (
                        project.category?.name
                        ?? ''
                    )
                        .toLowerCase()
                        .includes(
                            term,
                        ),
            );
        },
    );

const archive =
    (
        project:
            Project,
    ): void => {
        if (
            !window.confirm(
                `¿Archivar "${project.title}"?`,
            )
        ) {
            return;
        }

        router.delete(
            `/editor-preview/portafolio/proyectos/${project.slug}`,
        );
    };
</script>

<template>
    <Head title="Portafolio" />

    <EditorLayout
        title="Portafolio"
        eyebrow="Proyectos"
    >
        <div
            v-if="page.props.flash?.success"
            class="mb-5 rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-5 py-4 text-sm font-bold text-[var(--adn-turquoise)]"
        >
            {{ page.props.flash.success }}
        </div>

        <div
            class="mb-6 flex flex-col justify-between gap-4 xl:flex-row xl:items-end"
        >
            <div>
                <p
                    class="text-[9px] font-black uppercase tracking-[0.15em] text-[var(--adn-orange)]"
                >
                    Trabajos reales
                </p>

                <h1
                    class="mt-2 text-3xl font-black tracking-[-0.04em] text-white"
                >
                    Proyectos
                </h1>

                <p
                    class="mt-2 max-w-2xl text-xs leading-6 text-white/30"
                >
                    Aquí sí administramos el portafolio real: cada proyecto puede tener portada, categoría y galería propia.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <Link
                    href="/editor-preview/paginas/portafolio/editar"
                    class="inline-flex h-11 items-center gap-2 rounded-xl border border-white/[0.07] px-4 text-xs font-black text-white/40 hover:text-[var(--adn-turquoise)]"
                >
                    <Eye class="h-4 w-4" />
                    Página pública
                </Link>

                <Link
                    href="/editor-preview/portafolio/categorias"
                    class="inline-flex h-11 items-center gap-2 rounded-xl border border-white/[0.07] px-4 text-xs font-black text-white/40 hover:text-[var(--adn-turquoise)]"
                >
                    <FolderTree class="h-4 w-4" />
                    Categorías
                </Link>

                <Link
                    href="/editor-preview/portafolio/proyectos/crear"
                    class="adn-primary-button inline-flex h-11 items-center gap-2 rounded-xl px-4 text-xs font-black"
                >
                    <Plus class="h-4 w-4" />
                    Nuevo proyecto
                </Link>
            </div>
        </div>

        <div
            class="adn-panel mb-5 rounded-[18px] p-3"
        >
            <div class="relative max-w-md">
                <Search
                    class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                />

                <input
                    v-model="search"
                    type="search"
                    placeholder="Buscar proyecto, cliente o categoría..."
                    class="h-10 w-full rounded-xl border border-white/[0.06] bg-black/15 pl-10 pr-4 text-xs text-white outline-none placeholder:text-white/15"
                >
            </div>
        </div>

        <div
            v-if="filteredProjects.length"
            class="grid gap-5 md:grid-cols-2 xl:grid-cols-3"
        >
            <article
                v-for="project in filteredProjects"
                :key="project.id"
                class="adn-panel overflow-hidden rounded-[22px]"
            >
                <div
                    class="relative aspect-[16/10] overflow-hidden bg-black/15"
                >
                    <img
                        v-if="project.main_media"
                        :src="project.main_media.url"
                        :alt="project.main_media.alt_text ?? project.title"
                        class="h-full w-full object-cover"
                    >

                    <div
                        v-else
                        class="flex h-full items-center justify-center"
                    >
                        <ImageIcon class="h-10 w-10 text-white/10" />
                    </div>

                    <span
                        v-if="project.featured"
                        class="absolute left-3 top-3 flex items-center gap-1 rounded-lg bg-[#F5C000] px-2 py-1 text-[8px] font-black uppercase text-[#1D1D1B]"
                    >
                        <Star class="h-3 w-3" />
                        Destacado
                    </span>

                    <span
                        class="absolute right-3 top-3 rounded-lg px-2 py-1 text-[8px] font-black uppercase"
                        :class="
                            project.status === 'published'
                                ? 'bg-[#0FA7B4] text-white'
                                : 'bg-black/70 text-white/60'
                        "
                    >
                        {{
                            project.status === 'published'
                                ? 'Publicado'
                                : 'Borrador'
                        }}
                    </span>
                </div>

                <div class="p-5">
                    <div
                        class="flex items-center justify-between gap-3"
                    >
                        <p
                            class="text-[9px] font-black uppercase tracking-[0.12em] text-[var(--adn-orange)]"
                        >
                            {{
                                project.category?.name
                                ?? 'Sin categoría'
                            }}
                        </p>

                        <span
                            class="text-[9px] font-bold text-white/20"
                        >
                            {{ project.images_count }}
                            imágenes
                        </span>
                    </div>

                    <h2
                        class="mt-2 text-lg font-black tracking-[-0.025em] text-white/85"
                    >
                        {{ project.title }}
                    </h2>

                    <p
                        v-if="project.client_name"
                        class="mt-1 text-[10px] text-white/25"
                    >
                        {{ project.client_name }}
                    </p>

                    <p
                        v-if="project.excerpt"
                        class="mt-3 line-clamp-2 text-xs leading-5 text-white/30"
                    >
                        {{ project.excerpt }}
                    </p>

                    <div
                        class="mt-5 flex items-center justify-between border-t border-white/[0.06] pt-4"
                    >
                        <span class="text-[9px] font-black text-white/18">
                            {{ project.code }}
                        </span>

                        <div class="flex gap-2">
                            <Link
                                :href="`/editor-preview/portafolio/proyectos/${project.slug}/editar`"
                                class="flex h-9 w-9 items-center justify-center rounded-xl border border-white/[0.06] text-white/40 hover:text-[var(--adn-turquoise)]"
                            >
                                <Pencil class="h-4 w-4" />
                            </Link>

                            <button
                                type="button"
                                class="flex h-9 w-9 items-center justify-center rounded-xl border border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] text-[var(--adn-coral)]"
                                @click="archive(project)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div
            v-else
            class="adn-panel flex min-h-72 flex-col items-center justify-center rounded-[24px] p-8 text-center"
        >
            <BriefcaseBusiness class="h-10 w-10 text-white/10" />

            <h2 class="mt-4 text-lg font-black text-white/65">
                No hay proyectos
            </h2>

            <p class="mt-2 max-w-sm text-xs leading-6 text-white/25">
                Crea el primer proyecto para comenzar a construir el portafolio.
            </p>
        </div>
    </EditorLayout>
</template>