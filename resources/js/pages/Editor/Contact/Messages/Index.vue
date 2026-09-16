<script setup lang="ts">
import {
    Head,
    Link,
    router,
} from '@inertiajs/vue3';

import {
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    CircleAlert,
    Clock3,
    Mail,
    MessageSquareText,
    Search,
} from '@lucide/vue';

import {
    reactive,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface Message {
    id: number;
    message_number: string | null;
    name: string;
    company: string | null;
    phone: string | null;
    email: string | null;
    subject: string;
    status: string;
    app_sync_status: string;
    app_lead_id: number | null;
    read_at: string | null;
    created_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Paginator {
    data: Message[];
    current_page: number;
    last_page: number;
    total: number;
    links: PaginationLink[];
}

interface Stats {
    total: number;
    new: number;
    pending_sync: number;
    failed_sync: number;
}

const props =
    defineProps<{
        messages: Paginator;

        filters: {
            search: string;
            status: string;
            sync: string;
        };

        stats: Stats;
    }>();

const filters =
    reactive({
        search:
            props.filters.search
            ?? '',

        status:
            props.filters.status
            ?? '',

        sync:
            props.filters.sync
            ?? '',
    });

const applyFilters =
    (): void => {
        router.get(
            '/editor-preview/contacto/mensajes',
            {
                search:
                    filters.search
                    || undefined,

                status:
                    filters.status
                    || undefined,

                sync:
                    filters.sync
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

        filters.sync =
            '';

        router.get(
            '/editor-preview/contacto/mensajes',
        );
    };

const statusLabel =
    (
        status:
            string,
    ): string => {
        const labels:
            Record<string, string> =
        {
            new:
                'Nuevo',

            reviewing:
                'En revisión',

            contacted:
                'Contactado',

            closed:
                'Cerrado',

            spam:
                'Spam',
        };

        return labels[
            status
        ] ?? status;
    };

const syncLabel =
    (
        status:
            string,
    ): string => {
        const labels:
            Record<string, string> =
        {
            pending:
                'Pendiente',

            synced:
                'Sincronizado',

            failed:
                'Error',
        };

        return labels[
            status
        ] ?? status;
    };

const syncClass =
    (
        status:
            string,
    ): string => {
        if (
            status ===
            'synced'
        ) {
            return 'border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]';
        }

        if (
            status ===
            'failed'
        ) {
            return 'border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] text-[var(--adn-coral)]';
        }

        return 'border-[var(--adn-orange-border)] bg-[var(--adn-orange-soft)] text-[var(--adn-orange)]';
    };
</script>

<template>
    <Head
        title="Mensajes de contacto"
    />

    <EditorLayout
        title="Mensajes"
        eyebrow="Contacto"
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
                    <Link
                        href="/editor-preview/contacto"
                        class="inline-flex items-center gap-2 text-xs font-black text-white/35 transition hover:text-[var(--adn-turquoise)]"
                    >
                        <ArrowLeft
                            class="h-4 w-4"
                        />

                        Volver a Contacto
                    </Link>

                    <h1
                        class="mt-5 text-3xl font-black tracking-[-0.04em] sm:text-4xl"
                    >
                        Bandeja de

                        <span
                            class="text-[var(--adn-turquoise)]"
                        >
                            mensajes.
                        </span>
                    </h1>

                    <p
                        class="mt-3 text-sm text-white/40"
                    >
                        Revisa consultas recibidas desde adnpublicidad.site
                        y su estado de sincronización con ADN APP.
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-5 py-4"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.12em] text-[var(--adn-turquoise)]"
                    >
                        Registros encontrados
                    </p>

                    <p
                        class="mt-1 text-2xl font-black"
                    >
                        {{
                            messages.total
                        }}
                    </p>
                </div>
            </div>
        </section>

        <section
            class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
        >
            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <MessageSquareText
                    class="h-5 w-5 text-[var(--adn-turquoise)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{ stats.total }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Total
                </p>
            </article>

            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <Mail
                    class="h-5 w-5 text-[var(--adn-coral)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{ stats.new }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Nuevos
                </p>
            </article>

            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <Clock3
                    class="h-5 w-5 text-[var(--adn-orange)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{ stats.pending_sync }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Pendientes APP
                </p>
            </article>

            <article
                class="adn-panel rounded-[20px] p-5"
            >
                <CircleAlert
                    class="h-5 w-5 text-[var(--adn-coral)]"
                />

                <p
                    class="mt-4 text-3xl font-black"
                >
                    {{ stats.failed_sync }}
                </p>

                <p
                    class="text-xs text-white/40"
                >
                    Fallidos
                </p>
            </article>
        </section>

        <form
            class="adn-panel mt-5 grid gap-3 rounded-[20px] p-4 sm:grid-cols-[minmax(0,1fr)_180px_180px_auto_auto]"
            @submit.prevent="
                applyFilters
            "
        >
            <div
                class="relative"
            >
                <Search
                    class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                />

                <input
                    v-model="
                        filters.search
                    "
                    type="search"
                    placeholder="Nombre, asunto, teléfono, correo..."
                    class="h-11 w-full rounded-xl border border-[var(--adn-border)] bg-white/[0.035] pl-11 pr-4 text-xs text-white outline-none placeholder:text-white/20"
                />
            </div>

            <select
                v-model="
                    filters.status
                "
                class="h-11 rounded-xl border border-[var(--adn-border)] bg-[#151a1b] px-3 text-xs font-bold text-white/65 outline-none"
            >
                <option value="">
                    Todos los estados
                </option>

                <option value="new">
                    Nuevos
                </option>

                <option value="reviewing">
                    En revisión
                </option>

                <option value="contacted">
                    Contactados
                </option>

                <option value="closed">
                    Cerrados
                </option>

                <option value="spam">
                    Spam
                </option>
            </select>

            <select
                v-model="
                    filters.sync
                "
                class="h-11 rounded-xl border border-[var(--adn-border)] bg-[#151a1b] px-3 text-xs font-bold text-white/65 outline-none"
            >
                <option value="">
                    Toda sincronización
                </option>

                <option value="pending">
                    Pendiente
                </option>

                <option value="synced">
                    Sincronizado
                </option>

                <option value="failed">
                    Error
                </option>
            </select>

            <button
                type="submit"
                class="adn-primary-button h-11 rounded-xl px-5 text-xs font-black"
            >
                Filtrar
            </button>

            <button
                type="button"
                class="h-11 rounded-xl border border-white/[0.07] px-4 text-xs font-black text-white/40 transition hover:bg-white/[0.04] hover:text-white"
                @click="
                    clearFilters
                "
            >
                Limpiar
            </button>
        </form>

        <section
            class="adn-panel mt-5 overflow-hidden rounded-[22px]"
        >
            <div
                v-if="
                    messages.data.length
                "
            >
                <div
                    class="hidden grid-cols-[150px_minmax(180px,.9fr)_minmax(220px,1.4fr)_130px_130px_48px] gap-4 border-b border-[var(--adn-border)] px-6 py-4 text-[9px] font-black uppercase tracking-[0.09em] text-white/25 lg:grid"
                >
                    <span>
                        Solicitud
                    </span>

                    <span>
                        Cliente
                    </span>

                    <span>
                        Asunto
                    </span>

                    <span>
                        Estado
                    </span>

                    <span>
                        ADN APP
                    </span>

                    <span />
                </div>

                <Link
                    v-for="
                        message in messages.data
                    "
                    :key="
                        message.id
                    "
                    :href="
                        `/editor-preview/contacto/mensajes/${message.id}`
                    "
                    class="block border-b border-white/[0.045] px-5 py-5 transition last:border-b-0 hover:bg-white/[0.025] lg:grid lg:grid-cols-[150px_minmax(180px,.9fr)_minmax(220px,1.4fr)_130px_130px_48px] lg:items-center lg:gap-4 lg:px-6"
                >
                    <div>
                        <p
                            class="text-xs font-black text-[var(--adn-turquoise)]"
                        >
                            {{
                                message.message_number
                            }}
                        </p>

                        <p
                            class="mt-1 text-[9px] text-white/20"
                        >
                            {{
                                message.created_at
                            }}
                        </p>
                    </div>

                    <div
                        class="mt-4 min-w-0 lg:mt-0"
                    >
                        <p
                            class="truncate text-sm font-black text-white/75"
                        >
                            {{
                                message.name
                            }}
                        </p>

                        <p
                            class="mt-1 truncate text-[10px] text-white/25"
                        >
                            {{
                                message.company
                                ?? message.phone
                                ?? message.email
                                ?? 'Sin dato adicional'
                            }}
                        </p>
                    </div>

                    <p
                        class="mt-3 truncate text-xs font-bold text-white/50 lg:mt-0"
                    >
                        {{
                            message.subject
                        }}
                    </p>

                    <div
                        class="mt-3 lg:mt-0"
                    >
                        <span
                            class="text-[9px] font-black uppercase tracking-[0.08em] text-white/40"
                        >
                            {{
                                statusLabel(
                                    message.status,
                                )
                            }}
                        </span>
                    </div>

                    <div
                        class="mt-3 lg:mt-0"
                    >
                        <span
                            class="inline-flex rounded-lg border px-2 py-1 text-[8px] font-black uppercase tracking-[0.07em]"
                            :class="
                                syncClass(
                                    message.app_sync_status,
                                )
                            "
                        >
                            {{
                                syncLabel(
                                    message.app_sync_status,
                                )
                            }}
                        </span>
                    </div>

                    <div
                        class="mt-4 flex justify-end lg:mt-0"
                    >
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/[0.04] text-white/30"
                        >
                            <ArrowRight
                                class="h-4 w-4"
                            />
                        </span>
                    </div>
                </Link>
            </div>

            <div
                v-else
                class="flex min-h-64 flex-col items-center justify-center px-6 text-center"
            >
                <Search
                    class="h-8 w-8 text-white/15"
                />

                <p
                    class="mt-4 text-sm font-black text-white/55"
                >
                    No encontramos mensajes
                </p>

                <p
                    class="mt-1 text-xs text-white/25"
                >
                    Prueba con otros filtros.
                </p>
            </div>
        </section>

        <div
            v-if="
                messages.last_page >
                1
            "
            class="mt-6 flex flex-wrap justify-center gap-2"
        >
            <Link
                v-for="
                    link in messages.links
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
                        ? 'border-[var(--adn-turquoise)] bg-[var(--adn-turquoise)] text-white'
                        : link.url
                            ? 'border-white/[0.07] bg-white/[0.03] text-white/40'
                            : 'pointer-events-none border-white/[0.03] text-white/15'
                "
                v-html="
                    link.label
                "
            />
        </div>
    </EditorLayout>
</template>