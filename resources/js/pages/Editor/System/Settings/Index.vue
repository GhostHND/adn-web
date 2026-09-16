<script setup lang="ts">
import {
    Head,
    useForm,
    usePage,
} from '@inertiajs/vue3';

import {
    Building2,
    CheckCircle2,
    CircleAlert,
    Clock3,
    Globe2,
    Mail,
    MapPin,
    MessageCircle,
    Phone,
    Save,
    Search,
    Settings2,
    Share2,
    Sparkles,
} from '@lucide/vue';

import {
    computed,
} from 'vue';

import EditorLayout
    from '@/layouts/Editor/EditorLayout.vue';

interface BusinessSettings {
    business_name: string | null;
    business_tagline: string | null;
}

interface ContactSettings {
    phone: string | null;
    whatsapp: string | null;
    email: string | null;
    address: string | null;
    business_hours: string | null;
    map_url: string | null;
}

interface SocialSettings {
    facebook: string | null;
    instagram: string | null;
    tiktok: string | null;
}

interface SeoSettings {
    default_meta_title: string | null;
    default_meta_description: string | null;
}

const props =
    defineProps<{
        settings: {
            business: BusinessSettings;
            contact: ContactSettings;
            social: SocialSettings;
            seo: SeoSettings;
        };
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

const form =
    useForm({
        business_name:
            props.settings.business.business_name
            ?? '',

        business_tagline:
            props.settings.business.business_tagline
            ?? '',

        phone:
            props.settings.contact.phone
            ?? '',

        whatsapp:
            props.settings.contact.whatsapp
            ?? '',

        email:
            props.settings.contact.email
            ?? '',

        address:
            props.settings.contact.address
            ?? '',

        business_hours:
            props.settings.contact.business_hours
            ?? '',

        map_url:
            props.settings.contact.map_url
            ?? '',

        facebook:
            props.settings.social.facebook
            ?? '',

        instagram:
            props.settings.social.instagram
            ?? '',

        tiktok:
            props.settings.social.tiktok
            ?? '',

        default_meta_title:
            props.settings.seo.default_meta_title
            ?? '',

        default_meta_description:
            props.settings.seo.default_meta_description
            ?? '',
    });

const whatsappPreview =
    computed(
        (): string | null => {
            let digits =
                form.whatsapp
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

            if (
                !digits
            ) {
                return null;
            }

            return `https://wa.me/${digits}`;
        },
    );

const save =
    (): void => {
        form.put(
            '/editor-preview/configuracion',
            {
                preserveScroll:
                    true,
            },
        );
    };

const inputClass =
    'h-11 w-full rounded-xl border border-[var(--adn-border)] bg-white/[0.035] px-4 text-sm text-white outline-none transition placeholder:text-white/20 focus:border-[var(--adn-turquoise)]/50 focus:bg-white/[0.05]';

const textareaClass =
    'min-h-[110px] w-full resize-y rounded-xl border border-[var(--adn-border)] bg-white/[0.035] px-4 py-3 text-sm leading-6 text-white outline-none transition placeholder:text-white/20 focus:border-[var(--adn-turquoise)]/50 focus:bg-white/[0.05]';

const labelClass =
    'mb-2 block text-[9px] font-black uppercase tracking-[0.1em] text-white/35';

const errorClass =
    'mt-2 text-[10px] font-bold text-[var(--adn-coral)]';
</script>

<template>
    <Head
        title="Configuración"
    />

    <EditorLayout
        title="Configuración"
        eyebrow="Sistema"
    >
        <form
            @submit.prevent="
                save
            "
        >
            <!-- HERO -->

            <section
                class="adn-panel relative overflow-hidden rounded-[26px] p-6 sm:p-8"
            >
                <div
                    class="adn-brand-stripe absolute inset-x-0 top-0 h-[3px]"
                />

                <div
                    class="relative flex flex-col justify-between gap-6 lg:flex-row lg:items-center"
                >
                    <div
                        class="max-w-2xl"
                    >
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-[var(--adn-turquoise-border)] bg-[var(--adn-turquoise-soft)] px-3 py-1.5"
                        >
                            <Sparkles
                                class="h-3.5 w-3.5 text-[var(--adn-turquoise)]"
                            />

                            <span
                                class="text-[9px] font-black uppercase tracking-[0.16em] text-[var(--adn-turquoise)]"
                            >
                                ADN Web
                            </span>
                        </div>

                        <h1
                            class="mt-5 text-3xl font-black tracking-[-0.04em] sm:text-4xl"
                        >
                            Configuración general de

                            <span
                                class="text-[var(--adn-turquoise)]"
                            >
                                ADN Publicidad.
                            </span>
                        </h1>

                        <p
                            class="mt-3 max-w-xl text-sm leading-6 text-white/40"
                        >
                            Administra la información comercial,
                            canales de contacto, redes sociales y
                            valores SEO generales desde un solo lugar.
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="
                            form.processing
                        "
                        class="adn-primary-button inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-xl px-5 text-xs font-black disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <Save
                            class="h-4 w-4"
                        />

                        {{
                            form.processing
                                ? 'Guardando...'
                                : 'Guardar cambios'
                        }}
                    </button>
                </div>
            </section>

            <!-- FLASH -->

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
                <div
                    class="flex items-start gap-3"
                >
                    <CircleAlert
                        v-if="
                            flash.error
                        "
                        class="mt-0.5 h-4 w-4 shrink-0 text-[var(--adn-coral)]"
                    />

                    <CheckCircle2
                        v-else
                        class="mt-0.5 h-4 w-4 shrink-0 text-[var(--adn-turquoise)]"
                    />

                    <p
                        class="text-xs font-bold text-white/65"
                    >
                        {{
                            flash.error
                            ?? flash.success
                        }}
                    </p>
                </div>
            </div>

            <!-- SECCIONES -->

            <div
                class="mt-5 grid gap-5 xl:grid-cols-2"
            >
                <!-- EMPRESA -->

                <section
                    class="adn-panel rounded-[22px] p-6"
                >
                    <div
                        class="flex items-center justify-between gap-4 border-b border-[var(--adn-border)] pb-5"
                    >
                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                Identidad comercial
                            </h2>

                            <p
                                class="mt-1 text-[10px] text-white/30"
                            >
                                Información principal de la empresa
                            </p>
                        </div>

                        <Building2
                            class="h-5 w-5 text-[var(--adn-turquoise)]"
                        />
                    </div>

                    <div
                        class="mt-6 grid gap-5"
                    >
                        <div>
                            <label
                                :class="
                                    labelClass
                                "
                            >
                                Nombre comercial
                            </label>

                            <input
                                v-model="
                                    form.business_name
                                "
                                type="text"
                                placeholder="ADN Publicidad"
                                :class="
                                    inputClass
                                "
                            >

                            <p
                                v-if="
                                    form.errors.business_name
                                "
                                :class="
                                    errorClass
                                "
                            >
                                {{
                                    form.errors.business_name
                                }}
                            </p>
                        </div>

                        <div>
                            <label
                                :class="
                                    labelClass
                                "
                            >
                                Eslogan
                            </label>

                            <input
                                v-model="
                                    form.business_tagline
                                "
                                type="text"
                                placeholder="Diseño · Publicidad · Tecnología"
                                :class="
                                    inputClass
                                "
                            >

                            <p
                                v-if="
                                    form.errors.business_tagline
                                "
                                :class="
                                    errorClass
                                "
                            >
                                {{
                                    form.errors.business_tagline
                                }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- CONTACTO -->

                <section
                    class="adn-panel rounded-[22px] p-6"
                >
                    <div
                        class="flex items-center justify-between gap-4 border-b border-[var(--adn-border)] pb-5"
                    >
                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                Contacto
                            </h2>

                            <p
                                class="mt-1 text-[10px] text-white/30"
                            >
                                Canales públicos de ADN Publicidad
                            </p>
                        </div>

                        <MessageCircle
                            class="h-5 w-5 text-[#25D366]"
                        />
                    </div>

                    <div
                        class="mt-6 grid gap-5 sm:grid-cols-2"
                    >
                        <div>
                            <label
                                :class="
                                    labelClass
                                "
                            >
                                Teléfono
                            </label>

                            <div
                                class="relative"
                            >
                                <Phone
                                    class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                                />

                                <input
                                    v-model="
                                        form.phone
                                    "
                                    type="text"
                                    placeholder="+504 0000-0000"
                                    :class="
                                        `${inputClass} pl-11`
                                    "
                                >
                            </div>

                            <p
                                v-if="
                                    form.errors.phone
                                "
                                :class="
                                    errorClass
                                "
                            >
                                {{
                                    form.errors.phone
                                }}
                            </p>
                        </div>

                        <div>
                            <label
                                :class="
                                    labelClass
                                "
                            >
                                WhatsApp
                            </label>

                            <input
                                v-model="
                                    form.whatsapp
                                "
                                type="text"
                                placeholder="98518558"
                                :class="
                                    inputClass
                                "
                            >

                            <p
                                v-if="
                                    form.errors.whatsapp
                                "
                                :class="
                                    errorClass
                                "
                            >
                                {{
                                    form.errors.whatsapp
                                }}
                            </p>

                            <a
                                v-if="
                                    whatsappPreview
                                "
                                :href="
                                    whatsappPreview
                                "
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-2 inline-flex items-center gap-1.5 text-[10px] font-black text-[#25D366] hover:underline"
                            >
                                Probar WhatsApp
                            </a>
                        </div>

                        <div
                            class="sm:col-span-2"
                        >
                            <label
                                :class="
                                    labelClass
                                "
                            >
                                Correo electrónico
                            </label>

                            <div
                                class="relative"
                            >
                                <Mail
                                    class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                                />

                                <input
                                    v-model="
                                        form.email
                                    "
                                    type="email"
                                    placeholder="contacto@adnpublicidad.site"
                                    :class="
                                        `${inputClass} pl-11`
                                    "
                                >
                            </div>

                            <p
                                v-if="
                                    form.errors.email
                                "
                                :class="
                                    errorClass
                                "
                            >
                                {{
                                    form.errors.email
                                }}
                            </p>
                        </div>

                        <div
                            class="sm:col-span-2"
                        >
                            <label
                                :class="
                                    labelClass
                                "
                            >
                                Dirección
                            </label>

                            <textarea
                                v-model="
                                    form.address
                                "
                                placeholder="Dirección pública de ADN Publicidad"
                                :class="
                                    textareaClass
                                "
                            />

                            <p
                                v-if="
                                    form.errors.address
                                "
                                :class="
                                    errorClass
                                "
                            >
                                {{
                                    form.errors.address
                                }}
                            </p>
                        </div>

                        <div
                            class="sm:col-span-2"
                        >
                            <label
                                :class="
                                    labelClass
                                "
                            >
                                Horario de atención
                            </label>

                            <div
                                class="relative"
                            >
                                <Clock3
                                    class="absolute left-4 top-4 h-4 w-4 text-white/20"
                                />

                                <textarea
                                    v-model="
                                        form.business_hours
                                    "
                                    placeholder="Lunes a viernes..."
                                    :class="
                                        `${textareaClass} pl-11`
                                    "
                                />
                            </div>

                            <p
                                v-if="
                                    form.errors.business_hours
                                "
                                :class="
                                    errorClass
                                "
                            >
                                {{
                                    form.errors.business_hours
                                }}
                            </p>
                        </div>

                        <div
                            class="sm:col-span-2"
                        >
                            <label
                                :class="
                                    labelClass
                                "
                            >
                                Enlace de ubicación
                            </label>

                            <div
                                class="relative"
                            >
                                <MapPin
                                    class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                                />

                                <input
                                    v-model="
                                        form.map_url
                                    "
                                    type="url"
                                    placeholder="https://maps.google.com/..."
                                    :class="
                                        `${inputClass} pl-11`
                                    "
                                >
                            </div>

                            <p
                                v-if="
                                    form.errors.map_url
                                "
                                :class="
                                    errorClass
                                "
                            >
                                {{
                                    form.errors.map_url
                                }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- REDES -->

                <section
                    class="adn-panel rounded-[22px] p-6"
                >
                    <div
                        class="flex items-center justify-between gap-4 border-b border-[var(--adn-border)] pb-5"
                    >
                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                Redes sociales
                            </h2>

                            <p
                                class="mt-1 text-[10px] text-white/30"
                            >
                                Perfiles oficiales de la empresa
                            </p>
                        </div>

                        <Share2
                            class="h-5 w-5 text-[var(--adn-orange)]"
                        />
                    </div>

                    <div
                        class="mt-6 grid gap-5"
                    >
                        <div>
                            <label
                                :class="
                                    labelClass
                                "
                            >
                                Facebook
                            </label>

                            <input
                                v-model="
                                    form.facebook
                                "
                                type="url"
                                placeholder="https://facebook.com/..."
                                :class="
                                    inputClass
                                "
                            >

                            <p
                                v-if="
                                    form.errors.facebook
                                "
                                :class="
                                    errorClass
                                "
                            >
                                {{
                                    form.errors.facebook
                                }}
                            </p>
                        </div>

                        <div>
                            <label
                                :class="
                                    labelClass
                                "
                            >
                                Instagram
                            </label>

                            <input
                                v-model="
                                    form.instagram
                                "
                                type="url"
                                placeholder="https://instagram.com/..."
                                :class="
                                    inputClass
                                "
                            >

                            <p
                                v-if="
                                    form.errors.instagram
                                "
                                :class="
                                    errorClass
                                "
                            >
                                {{
                                    form.errors.instagram
                                }}
                            </p>
                        </div>

                        <div>
                            <label
                                :class="
                                    labelClass
                                "
                            >
                                TikTok
                            </label>

                            <input
                                v-model="
                                    form.tiktok
                                "
                                type="url"
                                placeholder="https://www.tiktok.com/@adnpublicidad"
                                :class="
                                    inputClass
                                "
                            >

                            <p
                                v-if="
                                    form.errors.tiktok
                                "
                                :class="
                                    errorClass
                                "
                            >
                                {{
                                    form.errors.tiktok
                                }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- SEO -->

                <section
                    class="adn-panel rounded-[22px] p-6"
                >
                    <div
                        class="flex items-center justify-between gap-4 border-b border-[var(--adn-border)] pb-5"
                    >
                        <div>
                            <h2
                                class="text-sm font-black"
                            >
                                SEO general
                            </h2>

                            <p
                                class="mt-1 text-[10px] text-white/30"
                            >
                                Valores predeterminados del sitio web
                            </p>
                        </div>

                        <Search
                            class="h-5 w-5 text-[var(--adn-yellow)]"
                        />
                    </div>

                    <div
                        class="mt-6 grid gap-5"
                    >
                        <div>
                            <label
                                :class="
                                    labelClass
                                "
                            >
                                Título predeterminado
                            </label>

                            <div
                                class="relative"
                            >
                                <Globe2
                                    class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                                />

                                <input
                                    v-model="
                                        form.default_meta_title
                                    "
                                    type="text"
                                    placeholder="ADN Publicidad"
                                    :class="
                                        `${inputClass} pl-11`
                                    "
                                >
                            </div>

                            <p
                                v-if="
                                    form.errors.default_meta_title
                                "
                                :class="
                                    errorClass
                                "
                            >
                                {{
                                    form.errors.default_meta_title
                                }}
                            </p>
                        </div>

                        <div>
                            <label
                                :class="
                                    labelClass
                                "
                            >
                                Descripción predeterminada
                            </label>

                            <textarea
                                v-model="
                                    form.default_meta_description
                                "
                                placeholder="Descripción general de ADN Publicidad..."
                                :class="
                                    textareaClass
                                "
                            />

                            <div
                                class="mt-2 flex justify-between gap-4"
                            >
                                <p
                                    v-if="
                                        form.errors.default_meta_description
                                    "
                                    :class="
                                        errorClass
                                    "
                                >
                                    {{
                                        form.errors.default_meta_description
                                    }}
                                </p>

                                <p
                                    class="ml-auto text-[9px] font-bold text-white/20"
                                >
                                    {{
                                        form.default_meta_description.length
                                    }}
                                    / 500
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-6 rounded-xl border border-white/[0.06] bg-white/[0.025] p-4"
                    >
                        <div
                            class="flex gap-3"
                        >
                            <Settings2
                                class="mt-0.5 h-4 w-4 shrink-0 text-[var(--adn-yellow)]"
                            />

                            <p
                                class="text-[10px] leading-5 text-white/35"
                            >
                                Estos valores sirven como información SEO
                                general. Las páginas que tengan SEO propio
                                pueden seguir utilizando sus valores
                                específicos.
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- GUARDAR -->

            <section
                class="adn-panel mt-5 flex flex-col justify-between gap-4 rounded-[22px] p-5 sm:flex-row sm:items-center"
            >
                <div>
                    <p
                        class="text-sm font-black text-white/75"
                    >
                        ¿Terminaste los cambios?
                    </p>

                    <p
                        class="mt-1 text-[10px] text-white/30"
                    >
                        Guarda para actualizar la configuración del sitio.
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="
                        form.processing
                    "
                    class="adn-primary-button inline-flex h-11 items-center justify-center gap-2 rounded-xl px-5 text-xs font-black disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <Save
                        class="h-4 w-4"
                    />

                    {{
                        form.processing
                            ? 'Guardando...'
                            : 'Guardar configuración'
                    }}
                </button>
            </section>
        </form>
    </EditorLayout>
</template>