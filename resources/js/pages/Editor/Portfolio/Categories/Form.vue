<script setup lang="ts">
import {
    Head,
    Link,
    useForm,
} from '@inertiajs/vue3';

import {
    ArrowLeft,
    Save,
} from '@lucide/vue';

import {
    computed,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface Category {
    id: number;
    slug: string;
    name: string;
    description: string | null;
    sort_order: number;
    active: boolean;
}

const props =
    defineProps<{
        category: Category | null;
    }>();

const editing =
    computed(
        () =>
            props.category !==
            null,
    );

const form =
    useForm({
        name:
            props.category?.name
            ?? '',

        slug:
            props.category?.slug
            ?? '',

        description:
            props.category?.description
            ?? '',

        sort_order:
            props.category?.sort_order
            ?? 10,

        active:
            props.category?.active
            ?? true,
    });

const submit =
    (): void => {
        if (
            props.category
        ) {
            form.put(
                `/editor-preview/portafolio/categorias/${props.category.slug}`,
            );

            return;
        }

        form.post(
            '/editor-preview/portafolio/categorias',
        );
    };
</script>

<template>
    <Head
        :title="
            editing
                ? 'Editar categoría'
                : 'Nueva categoría'
        "
    />

    <EditorLayout
        :title="
            editing
                ? 'Editar categoría'
                : 'Nueva categoría'
        "
        eyebrow="Portafolio"
    >
        <Link
            href="/editor-preview/portafolio/categorias"
            class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.12em] text-white/25 hover:text-[var(--adn-turquoise)]"
        >
            <ArrowLeft class="h-3.5 w-3.5" />
            Categorías
        </Link>

        <form
            class="mt-6 max-w-3xl space-y-5"
            @submit.prevent="submit"
        >
            <section class="adn-panel rounded-[22px] p-6">
                <div class="grid gap-5">
                    <label>
                        <span class="mb-2 block text-xs font-black text-white/55">
                            Nombre
                        </span>

                        <input
                            v-model="form.name"
                            type="text"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none"
                        >

                        <p
                            v-if="form.errors.name"
                            class="mt-2 text-xs text-[var(--adn-coral)]"
                        >
                            {{ form.errors.name }}
                        </p>
                    </label>

                    <label>
                        <span class="mb-2 block text-xs font-black text-white/55">
                            Slug
                        </span>

                        <input
                            v-model="form.slug"
                            type="text"
                            placeholder="Se genera automáticamente si lo dejas vacío"
                            class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white outline-none placeholder:text-white/15"
                        >
                    </label>

                    <label>
                        <span class="mb-2 block text-xs font-black text-white/55">
                            Descripción
                        </span>

                        <textarea
                            v-model="form.description"
                            rows="4"
                            class="w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 py-3 text-sm leading-6 text-white outline-none"
                        />
                    </label>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label>
                            <span class="mb-2 block text-xs font-black text-white/55">
                                Orden
                            </span>

                            <input
                                v-model.number="form.sort_order"
                                type="number"
                                min="0"
                                class="h-11 w-full rounded-xl border border-white/[0.07] bg-black/15 px-4 text-sm text-white"
                            >
                        </label>

                        <label
                            class="flex items-center gap-3 self-end rounded-xl border border-white/[0.06] bg-black/10 px-4 py-3"
                        >
                            <input
                                v-model="form.active"
                                type="checkbox"
                                class="h-4 w-4 accent-[#0FA7B4]"
                            >

                            <span class="text-xs font-black text-white/55">
                                Categoría activa
                            </span>
                        </label>
                    </div>
                </div>
            </section>

            <div class="flex justify-end">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="adn-primary-button inline-flex h-12 min-w-40 items-center justify-center gap-2 rounded-xl px-5 text-sm font-black disabled:opacity-50"
                >
                    <Save class="h-4 w-4" />

                    {{
                        form.processing
                            ? 'Guardando...'
                            : 'Guardar'
                    }}
                </button>
            </div>
        </form>
    </EditorLayout>
</template>