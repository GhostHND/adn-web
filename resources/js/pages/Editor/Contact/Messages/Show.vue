<script setup lang="ts">
import {
    Head,
    Link,
    useForm,
    usePage,
} from '@inertiajs/vue3';

import {
    ArrowLeft,
    CheckCircle2,
    CircleAlert,
    Clock3,
    Mail,
    MessageCircle,
    Phone,
    RefreshCw,
    Send,
    UserRound,
} from '@lucide/vue';

import {
    computed,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface ContactMessage {
    id: number;
    message_number: string | null;
    status: string;
    name: string;
    company: string | null;
    phone: string | null;
    email: string | null;
    preferred_contact: string | null;
    subject: string;
    message: string;
    source: string;
    ip_address: string | null;
    user_agent: string | null;
    app_sync_status: string;
    app_lead_id: number | null;
    sync_attempts: number;
    sync_error: string | null;
    last_sync_at: string | null;
    read_at: string | null;
    replied_at: string | null;
    created_at: string | null;
}

const props =
    defineProps<{
        message: ContactMessage;
    }>();

const page =
    usePage();

const flash =
    computed(
        () =>
            (
                page.props.flash
                ?? {}
            ) as {
                success?: string | null;
                error?: string | null;
            },
    );

const statusForm =
    useForm({
        status:
            props.message.status,
    });

const syncForm =
    useForm({});

const updateStatus =
    (): void => {
        statusForm.patch(
            `/editor-preview/contacto/mensajes/${props.message.id}/estado`,
            {
                preserveScroll:
                    true,
            },
        );
    };

const retrySync =
    (): void => {
        syncForm.post(
            `/editor-preview/contacto/mensajes/${props.message.id}/sincronizar`,
            {
                preserveScroll:
                    true,
            },
        );
    };

const whatsappUrl =
    computed(
        (): string | null => {
            if (
                !props.message.phone
            ) {
                return null;
            }

            let digits =
                props.message.phone
                    .replace(
                        /\D/g,
                        '',
                    );

            if (
                digits.length ===
                8
            ) {
                digits =
                    `504${digits}`;
            }

            return digits
                ? `https://wa.me/${digits}`
                : null;
        },
    );

const preferredContactLabel =
    computed(
        (): string => {
            const labels:
                Record<string, string> =
            {
                whatsapp:
                    'WhatsApp',

                phone:
                    'Llamada telefónica',

                email:
                    'Correo electrónico',
            };

            return props.message.preferred_contact
                ? (
                    labels[
                        props.message.preferred_contact
                    ]
                    ?? props.message.preferred_contact
                )
                : 'No indicada';
        },
    );

const syncLabel =
    computed(
        (): string => {
            const labels:
                Record<string, string> =
            {
                pending:
                    'Pendiente',

                synced:
                    'Sincronizado',

                failed:
                    'Error de sincronización',
            };

            return labels[
                props.message.app_sync_status
            ]
            ?? props.message.app_sync_status;
        },
    );
</script>

<template>
    <Head
        :title="
            `${message.message_number ?? 'Mensaje'} | Contacto`
        "
    />

    <EditorLayout
        title="Detalle del mensaje"
        eyebrow="Contacto"
    >
        <section
            class="adn-panel relative overflow-hidden rounded-[26px] p-6 sm:p-8"
        >
            <div
                class="adn-brand-stripe absolute inset-x-0 top-0 h-[3px]"
            />

            <Link
                href="/editor-preview/contacto/mensajes"
                class="inline-flex items-center gap-2 text-xs font-black text-white/35 transition hover:text-[var(--adn-turquoise)]"
            >
                <ArrowLeft
                    class="h-4 w-4"
                />

                Volver a mensajes
            </Link>

            <div
                class="mt-6 flex flex-col justify-between gap-5 lg:flex-row lg:items-end"
            >
                <div
                    class="min-w-0"
                >
                    <p
                        class="text-[10px] font-black uppercase tracking-[0.13em] text-[var(--adn-turquoise)]"
                    >
                        {{
                            message.message_number
                        }}
                    </p>

                    <h1
                        class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl"
                    >
                        {{
                            message.subject
                        }}
                    </h1>

                    <p
                        class="mt-3 text-sm text-white/35"
                    >
                        Recibido
                        {{
                            message.created_at
                        }}
                    </p>
                </div>

                <div
                    class="flex flex-wrap gap-2"
                >
                    <a
                        v-if="
                            whatsappUrl
                        "
                        :href="
                            whatsappUrl
                        "
                        target="_blank"
                        rel="noopener noreferrer"
                        class="adn-primary-button inline-flex h-11 items-center gap-2 rounded-xl px-4 text-xs font-black"
                    >
                        <MessageCircle
                            class="h-4 w-4"
                        />

                        WhatsApp
                    </a>

                    <a
                        v-if="
                            message.phone
                        "
                        :href="
                            `tel:${message.phone}`
                        "
                        class="inline-flex h-11 items-center gap-2 rounded-xl border border-white/[0.08] bg-white/[0.04] px-4 text-xs font-black text-white/55 transition hover:bg-white/[0.08]"
                    >
                        <Phone
                            class="h-4 w-4"
                        />

                        Llamar
                    </a>

                    <a
                        v-if="
                            message.email
                        "
                        :href="
                            `mailto:${message.email}`
                        "
                        class="inline-flex h-11 items-center gap-2 rounded-xl border border-white/[0.08] bg-white/[0.04] px-4 text-xs font-black text-white/55 transition hover:bg-white/[0.08]"
                    >
                        <Mail
                            class="h-4 w-4"
                        />

                        Correo
                    </a>
                </div>
            </div>
        </section>

        <div
            v-if="
                flash.success
                ||
                flash.error
            "
            class="mt-5 rounded-[18px] border p-4"
            :class="
                flash.error
                    ? 'border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)]'
                    : 'border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)]'
            "
        >
            <p
                class="text-xs font-bold text-white/65"
            >
                {{
                    flash.error
                    ?? flash.success
                }}
            </p>
        </div>

        <section
            class="mt-5 grid gap-5 xl:grid-cols-[minmax(0,1.2fr)_380px]"
        >
            <div
                class="space-y-5"
            >
                <article
                    class="adn-panel rounded-[22px] p-6 sm:p-7"
                >
                    <div
                        class="flex items-center gap-3"
                    >
                        <span
                            class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--adn-turquoise-soft)] text-[var(--adn-turquoise)]"
                        >
                            <UserRound
                                class="h-5 w-5"
                            />
                        </span>

                        <div>
                            <p
                                class="text-lg font-black"
                            >
                                {{
                                    message.name
                                }}
                            </p>

                            <p
                                v-if="
                                    message.company
                                "
                                class="mt-0.5 text-xs text-white/30"
                            >
                                {{
                                    message.company
                                }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-6 grid gap-4 sm:grid-cols-2"
                    >
                        <div
                            class="rounded-xl border border-white/[0.05] bg-white/[0.025] p-4"
                        >
                            <p
                                class="text-[9px] font-black uppercase tracking-[0.09em] text-white/25"
                            >
                                Teléfono
                            </p>

                            <p
                                class="mt-2 text-sm font-bold text-white/65"
                            >
                                {{
                                    message.phone
                                    ?? 'No proporcionado'
                                }}
                            </p>
                        </div>

                        <div
                            class="rounded-xl border border-white/[0.05] bg-white/[0.025] p-4"
                        >
                            <p
                                class="text-[9px] font-black uppercase tracking-[0.09em] text-white/25"
                            >
                                Correo
                            </p>

                            <p
                                class="mt-2 break-all text-sm font-bold text-white/65"
                            >
                                {{
                                    message.email
                                    ?? 'No proporcionado'
                                }}
                            </p>
                        </div>

                        <div
                            class="rounded-xl border border-white/[0.05] bg-white/[0.025] p-4 sm:col-span-2"
                        >
                            <p
                                class="text-[9px] font-black uppercase tracking-[0.09em] text-white/25"
                            >
                                Medio preferido
                            </p>

                            <p
                                class="mt-2 text-sm font-bold text-white/65"
                            >
                                {{
                                    preferredContactLabel
                                }}
                            </p>
                        </div>
                    </div>
                </article>

                <article
                    class="adn-panel rounded-[22px] p-6 sm:p-7"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.1em] text-[var(--adn-orange)]"
                    >
                        Mensaje
                    </p>

                    <p
                        class="mt-4 whitespace-pre-line text-sm leading-7 text-white/60"
                    >
                        {{
                            message.message
                        }}
                    </p>
                </article>

                <article
                    class="adn-panel rounded-[22px] p-6"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.1em] text-white/25"
                    >
                        Información técnica
                    </p>

                    <div
                        class="mt-4 space-y-3 text-xs text-white/30"
                    >
                        <p>
                            <span
                                class="font-black text-white/50"
                            >
                                Origen:
                            </span>

                            {{
                                message.source
                            }}
                        </p>

                        <p>
                            <span
                                class="font-black text-white/50"
                            >
                                IP:
                            </span>

                            {{
                                message.ip_address
                                ?? 'No disponible'
                            }}
                        </p>

                        <p
                            class="break-words"
                        >
                            <span
                                class="font-black text-white/50"
                            >
                                Navegador:
                            </span>

                            {{
                                message.user_agent
                                ?? 'No disponible'
                            }}
                        </p>
                    </div>
                </article>
            </div>

            <aside
                class="space-y-5"
            >
                <form
                    class="adn-panel rounded-[22px] p-6"
                    @submit.prevent="
                        updateStatus
                    "
                >
                    <h2
                        class="text-sm font-black"
                    >
                        Estado del mensaje
                    </h2>

                    <select
                        v-model="
                            statusForm.status
                        "
                        class="mt-5 h-11 w-full rounded-xl border border-[var(--adn-border)] bg-[#151a1b] px-3 text-xs font-bold text-white/65 outline-none"
                    >
                        <option value="new">
                            Nuevo
                        </option>

                        <option value="reviewing">
                            En revisión
                        </option>

                        <option value="contacted">
                            Contactado
                        </option>

                        <option value="closed">
                            Cerrado
                        </option>

                        <option value="spam">
                            Spam
                        </option>
                    </select>

                    <button
                        type="submit"
                        :disabled="
                            statusForm.processing
                        "
                        class="adn-primary-button mt-4 h-10 w-full rounded-xl text-xs font-black disabled:opacity-50"
                    >
                        Actualizar estado
                    </button>
                </form>

                <section
                    class="adn-panel rounded-[22px] p-6"
                >
                    <div
                        class="flex items-center justify-between gap-3"
                    >
                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                ADN APP
                            </h2>

                            <p
                                class="mt-1 text-[10px] text-white/30"
                            >
                                Sincronización de solicitud
                            </p>
                        </div>

                        <CheckCircle2
                            v-if="
                                message.app_sync_status ===
                                'synced'
                            "
                            class="h-5 w-5 text-[var(--adn-turquoise)]"
                        />

                        <CircleAlert
                            v-else-if="
                                message.app_sync_status ===
                                'failed'
                            "
                            class="h-5 w-5 text-[var(--adn-coral)]"
                        />

                        <Clock3
                            v-else
                            class="h-5 w-5 text-[var(--adn-orange)]"
                        />
                    </div>

                    <div
                        class="mt-5 rounded-xl border border-white/[0.05] bg-white/[0.025] p-4"
                    >
                        <p
                            class="text-[9px] font-black uppercase tracking-[0.09em] text-white/25"
                        >
                            Estado
                        </p>

                        <p
                            class="mt-2 text-sm font-black text-white/65"
                        >
                            {{
                                syncLabel
                            }}
                        </p>
                    </div>

                    <div
                        class="mt-3 grid grid-cols-2 gap-3"
                    >
                        <div
                            class="rounded-xl border border-white/[0.05] bg-white/[0.025] p-3"
                        >
                            <p
                                class="text-[8px] font-black uppercase text-white/20"
                            >
                                Intentos
                            </p>

                            <p
                                class="mt-1 text-lg font-black"
                            >
                                {{
                                    message.sync_attempts
                                }}
                            </p>
                        </div>

                        <div
                            class="rounded-xl border border-white/[0.05] bg-white/[0.025] p-3"
                        >
                            <p
                                class="text-[8px] font-black uppercase text-white/20"
                            >
                                ID APP
                            </p>

                            <p
                                class="mt-1 text-lg font-black"
                            >
                                {{
                                    message.app_lead_id
                                    ?? '—'
                                }}
                            </p>
                        </div>
                    </div>

                    <p
                        v-if="
                            message.last_sync_at
                        "
                        class="mt-4 text-[10px] text-white/25"
                    >
                        Último intento:
                        {{
                            message.last_sync_at
                        }}
                    </p>

                    <div
                        v-if="
                            message.sync_error
                        "
                        class="mt-4 rounded-xl border border-[var(--adn-coral-border)] bg-[var(--adn-coral-soft)] p-4"
                    >
                        <p
                            class="text-[9px] font-black uppercase tracking-[0.08em] text-[var(--adn-coral)]"
                        >
                            Error
                        </p>

                        <p
                            class="mt-2 break-words text-xs leading-5 text-white/50"
                        >
                            {{
                                message.sync_error
                            }}
                        </p>
                    </div>

                    <button
                        v-if="
                            message.app_sync_status !==
                            'synced'
                        "
                        type="button"
                        :disabled="
                            syncForm.processing
                        "
                        class="mt-5 inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] text-xs font-black text-[var(--adn-turquoise)] disabled:opacity-50"
                        @click="
                            retrySync
                        "
                    >
                        <RefreshCw
                            class="h-4 w-4"
                            :class="
                                syncForm.processing
                                    ? 'animate-spin'
                                    : ''
                            "
                        />

                        {{
                            syncForm.processing
                                ? 'Sincronizando...'
                                : 'Reintentar sincronización'
                        }}
                    </button>
                </section>

                <section
                    class="adn-panel rounded-[22px] p-6"
                >
                    <p
                        class="text-[9px] font-black uppercase tracking-[0.1em] text-white/25"
                    >
                        Seguimiento
                    </p>

                    <div
                        class="mt-4 space-y-3 text-xs text-white/35"
                    >
                        <p>
                            Leído:
                            {{
                                message.read_at
                                ?? 'No'
                            }}
                        </p>

                        <p>
                            Contactado:
                            {{
                                message.replied_at
                                ?? 'No'
                            }}
                        </p>
                    </div>
                </section>
            </aside>
        </section>
    </EditorLayout>
</template>