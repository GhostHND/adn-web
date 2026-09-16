<script setup lang="ts">
import {
    useForm,
} from '@inertiajs/vue3';

import {
    Building2,
    CheckCircle2,
    FileUp,
    ImageIcon,
    Mail,
    Minus,
    Phone,
    Plus,
    Send,
    ShieldCheck,
    Sparkles,
    UserRound,
    X,
} from '@lucide/vue';

import {
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue';

interface Media {
    id: number;
    title: string | null;
    alt_text: string | null;
    width: number | null;
    height: number | null;
    url: string;
}

interface FieldOption {
    id: number;
    label: string;
    value: string;
    extra_price: string | null;
}

interface ProductField {
    id: number;
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
    options: FieldOption[];
}

interface Product {
    id: number;
    code: string;
    slug: string;
    name: string;
    quote_mode: string;
    measurement_unit: string | null;
    main_media: Media | null;

    category: {
        id: number;
        name: string;
        slug: string;
    } | null;

    fields: ProductField[];
}

interface Props {
    open: boolean;
    product: Product;
}

type DynamicValue =
    | string
    | boolean;

const props =
    defineProps<Props>();

const emit =
    defineEmits([
        'close',
    ]);

const makeInitialValues =
    (): Record<
        string,
        DynamicValue
    > => {
        const values:
            Record<
                string,
                DynamicValue
            > =
            {};

        props.product.fields.forEach(
            (
                field,
            ) => {
                values[
                    field.field_key
                ] =
                    field.field_type ===
                        'checkbox'
                        ? false
                        : field.default_value
                            ?? '';
            },
        );

        return values;
    };

const makeInitialFiles =
    (): Record<
        string,
        File[]
    > => {
        const files:
            Record<
                string,
                File[]
            > =
            {};

        props.product.fields.forEach(
            (
                field,
            ) => {
                if (
                    field.field_type ===
                    'file'
                ) {
                    files[
                        field.field_key
                    ] =
                        [];
                }
            },
        );

        return files;
    };

const form =
    useForm({
        client_name:
            '',

        phone:
            '',

        whatsapp:
            '',

        email:
            '',

        company:
            '',

        notes:
            '',

        privacy_consent:
            false,

        quantity:
            1,

        values:
            makeInitialValues(),

        files:
            makeInitialFiles(),
    });

const successMessage =
    ref<string | null>(
        null,
    );

const resetFormData =
    () => {
        form.client_name =
            '';

        form.phone =
            '';

        form.whatsapp =
            '';

        form.email =
            '';

        form.company =
            '';

        form.notes =
            '';

        form.privacy_consent =
            false;

        form.quantity =
            1;

        form.values =
            makeInitialValues();

        form.files =
            makeInitialFiles();

        form.clearErrors();
    };

const requestClose =
    () => {
        resetFormData();

        successMessage.value =
            null;

        emit(
            'close',
        );
    };

const handleEscape =
    (
        event:
            KeyboardEvent,
    ) => {
        if (
            event.key ===
                'Escape'
            &&
            props.open
        ) {
            requestClose();
        }
    };

watch(
    () =>
        props.open,
    (
        open,
    ) => {
        document.body.style.overflow =
            open
                ? 'hidden'
                : '';
    },
);

onMounted(
    () => {
        window.addEventListener(
            'keydown',
            handleEscape,
        );
    },
);

onBeforeUnmount(
    () => {
        window.removeEventListener(
            'keydown',
            handleEscape,
        );

        document.body.style.overflow =
            '';
    },
);

const increaseQuantity =
    () => {
        form.quantity++;
    };

const decreaseQuantity =
    () => {
        if (
            form.quantity >
            1
        ) {
            form.quantity--;
        }
    };

const fieldValue =
    (
        field:
            ProductField,
    ): string => {
        const value =
            form.values[
                field.field_key
            ];

        return typeof value ===
            'string'
                ? value
                : '';
    };

const checkboxValue =
    (
        field:
            ProductField,
    ): boolean => {
        return form.values[
            field.field_key
        ] ===
            true;
    };

const getEventValue =
    (
        event:
            Event,
    ): string => {
        const target =
            event.target;

        if (
            !target
            ||
            !(
                'value'
                in target
            )
        ) {
            return '';
        }

        const value =
            Reflect.get(
                target,
                'value',
            );

        if (
            typeof value ===
                'string'
            ||
            typeof value ===
                'number'
        ) {
            return String(
                value,
            );
        }

        return '';
    };

const updateValue =
    (
        field:
            ProductField,
        event:
            Event,
    ) => {
        form.values[
            field.field_key
        ] =
            getEventValue(
                event,
            );
    };

const setOptionValue =
    (
        field:
            ProductField,
        value:
            string,
    ) => {
        form.values[
            field.field_key
        ] =
            value;
    };

const updateCheckbox =
    (
        field:
            ProductField,
        event:
            Event,
    ) => {
        const target =
            event.target;

        if (
            !target
            ||
            !(
                'checked'
                in target
            )
        ) {
            form.values[
                field.field_key
            ] =
                false;

            return;
        }

        form.values[
            field.field_key
        ] =
            Reflect.get(
                target,
                'checked',
            ) ===
            true;
    };

const fileLimit =
    (): number => {
        const quantity =
            Number(
                form.quantity,
            );

        if (
            !Number.isFinite(
                quantity,
            )
            ||
            quantity <
                1
        ) {
            return 1;
        }

        return Math.floor(
            quantity,
        );
    };

const selectedFiles =
    (
        field:
            ProductField,
    ): File[] => {
        return form.files[
            field.field_key
        ]
        ?? [];
    };

const fileIdentity =
    (
        file:
            File,
    ): string => {
        return [
            file.name,
            file.size,
            file.lastModified,
        ].join(
            ':',
        );
    };

const updateFiles =
    (
        field:
            ProductField,
        event:
            Event,
    ) => {
        const target =
            event.target;

        if (
            !target
            ||
            !(
                'files'
                in target
            )
        ) {
            return;
        }

        const fileList =
            Reflect.get(
                target,
                'files',
            );

        if (
            !(
                fileList
                instanceof
                FileList
            )
        ) {
            return;
        }

        const incomingFiles =
            Array.from(
                fileList,
            );

        const existingFiles =
            selectedFiles(
                field,
            );

        const combined =
            [
                ...existingFiles,
                ...incomingFiles,
            ];

        const unique =
            combined.filter(
                (
                    file,
                    index,
                    files,
                ) => {
                    const identity =
                        fileIdentity(
                            file,
                        );

                    return files.findIndex(
                        (
                            candidate,
                        ) =>
                            fileIdentity(
                                candidate,
                            ) ===
                            identity,
                    ) ===
                    index;
                },
            );

        const limit =
            fileLimit();

        form.files[
            field.field_key
        ] =
            unique.slice(
                0,
                limit,
            );

        form.clearErrors(
            `files.${field.field_key}`,
        );

        if (
            unique.length >
            limit
        ) {
            form.setError(
                `files.${field.field_key}`,
                `Puedes adjuntar como máximo ${limit} archivo${limit === 1 ? '' : 's'} para esta cantidad.`,
            );
        }

        try {
            Reflect.set(
                target,
                'value',
                '',
            );
        } catch {
            // El navegador puede impedir limpiar el input.
        }
    };

const removeFile =
    (
        field:
            ProductField,
        index:
            number,
    ) => {
        const files =
            selectedFiles(
                field,
            );

        form.files[
            field.field_key
        ] =
            files.filter(
                (
                    _file,
                    fileIndex,
                ) =>
                    fileIndex !==
                    index,
            );

        form.clearErrors(
            `files.${field.field_key}`,
        );
    };

const formatFileSize =
    (
        bytes:
            number,
    ): string => {
        if (
            bytes <
            1024
        ) {
            return `${bytes} B`;
        }

        if (
            bytes <
            1024 * 1024
        ) {
            return `${(
                bytes /
                1024
            ).toFixed(
                1,
            )} KB`;
        }

        return `${(
            bytes /
            1024 /
            1024
        ).toFixed(
            1,
        )} MB`;
    };

const errorFor =
    (
        key:
            string,
    ): string | null => {
        const value =
            Reflect.get(
                form.errors,
                key,
            );

        return typeof value ===
            'string'
                ? value
                : null;
    };

const dynamicError =
    (
        field:
            ProductField,
    ): string | null => {
        const prefix =
            field.field_type ===
                'file'
                ? `files.${field.field_key}`
                : `values.${field.field_key}`;

        const direct =
            errorFor(
                prefix,
            );

        if (
            direct
        ) {
            return direct;
        }

        const errors =
            Object.entries(
                form.errors,
            );

        const nested =
            errors.find(
                (
                    [
                        key,
                    ],
                ) =>
                    key.startsWith(
                        `${prefix}.`,
                    ),
            );

        if (
            !nested
        ) {
            return null;
        }

        return typeof nested[1] ===
            'string'
                ? nested[1]
                : null;
    };

const readFlashSuccess =
    (
        pageProps:
            object,
    ): string | null => {
        const flash =
            Reflect.get(
                pageProps,
                'flash',
            );

        if (
            !flash
            ||
            typeof flash !==
                'object'
        ) {
            return null;
        }

        const success =
            Reflect.get(
                flash,
                'success',
            );

        return typeof success ===
            'string'
                ? success
                : null;
    };

const validateFileCounts =
    (): boolean => {
        let valid =
            true;

        props.product.fields.forEach(
            (
                field,
            ) => {
                if (
                    field.field_type !==
                    'file'
                ) {
                    return;
                }

                const count =
                    selectedFiles(
                        field,
                    ).length;

                const limit =
                    fileLimit();

                if (
                    count >
                    limit
                ) {
                    form.setError(
                        `files.${field.field_key}`,
                        `Puedes adjuntar como máximo ${limit} archivo${limit === 1 ? '' : 's'} para esta cantidad.`,
                    );

                    valid =
                        false;
                }
            },
        );

        return valid;
    };

const submit =
    () => {
        successMessage.value =
            null;

        if (
            !validateFileCounts()
        ) {
            return;
        }

        form.post(
            `/catalogo/${props.product.slug}/cotizar`,
            {
                forceFormData:
                    true,

                preserveScroll:
                    true,

                preserveState:
                    true,

                onSuccess:
                    (
                        page,
                    ) => {
                        const message =
                            readFlashSuccess(
                                page.props,
                            )
                            ??
                            'Tu solicitud fue recibida correctamente.';

                        resetFormData();

                        successMessage.value =
                            message;
                    },
            },
        );
    };
</script>

<template>
    <Teleport to="body">
        <Transition
            name="adn-quote"
        >
            <div
                v-if="open"
                class="fixed inset-0 z-[150] flex items-center justify-center p-3 sm:p-5 lg:p-8 max-sm:items-end max-sm:p-0"
                role="dialog"
                aria-modal="true"
                :aria-label="`Cotizar ${product.name}`"
            >
                <button
                    type="button"
                    aria-label="Cerrar cotización"
                    class="absolute inset-0 bg-[#061011]/82 backdrop-blur-[14px]"
                    @click="requestClose"
                />

                <div
                    class="pointer-events-none absolute left-1/2 top-1/2 h-[75vh] w-[75vw] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#0FA7B4]/10 blur-[110px]"
                />

                <section
                    class="quote-dialog relative flex max-h-[92dvh] w-full max-w-[1080px] flex-col overflow-hidden rounded-[30px] border border-white/[0.11] bg-[#111819] shadow-[0_50px_140px_rgba(0,0,0,.68),0_0_0_1px_rgba(255,255,255,.025),0_22px_70px_rgba(15,167,180,.1)] max-sm:max-h-[96dvh] max-sm:rounded-b-none max-sm:rounded-t-[28px]"
                >
                    <div
                        class="h-[4px] shrink-0 bg-[linear-gradient(90deg,#0FA7B4_0%,#0FA7B4_28%,#E84657_28%,#E84657_55%,#ED7E24_55%,#ED7E24_78%,#F5C000_78%,#F5C000_100%)]"
                    />

                    <header
                        class="relative z-10 shrink-0 border-b border-white/[0.07] bg-[#111819]/95 px-5 py-4 backdrop-blur-xl sm:px-7 sm:py-5"
                    >
                        <div
                            class="flex items-start justify-between gap-4"
                        >
                            <div
                                class="flex min-w-0 items-start gap-4"
                            >
                                <div
                                    class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-[#0FA7B4]/20 bg-[#0FA7B4]/10 text-[#35c4ce] sm:flex"
                                >
                                    <Sparkles
                                        class="h-5 w-5"
                                    />
                                </div>

                                <div
                                    class="min-w-0"
                                >
                                    <p
                                        class="text-[8px] font-black uppercase tracking-[0.15em] text-[#35c4ce] sm:text-[9px]"
                                    >
                                        Cotización personalizada
                                    </p>

                                    <h2
                                        class="mt-1 truncate text-xl font-black tracking-[-0.035em] text-white sm:text-3xl"
                                    >
                                        Personaliza tu

                                        <span
                                            class="text-[#0FA7B4]"
                                        >
                                            {{ product.name }}
                                        </span>
                                    </h2>

                                    <p
                                        class="mt-1 hidden max-w-xl text-xs leading-5 text-white/35 sm:block"
                                    >
                                        Completa la información y envíanos tu proyecto.
                                    </p>
                                </div>
                            </div>

                            <button
                                type="button"
                                title="Cerrar"
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-white/[0.08] bg-white/[0.035] text-white/40 transition duration-200 hover:rotate-90 hover:border-[#E84657]/30 hover:bg-[#E84657]/10 hover:text-[#ff7280] sm:h-11 sm:w-11"
                                @click="requestClose"
                            >
                                <X
                                    class="h-5 w-5"
                                />
                            </button>
                        </div>
                    </header>

                    <div
                        class="quote-scroll flex-1 overflow-y-auto overscroll-contain"
                    >
                        <!-- ÉXITO -->

                        <div
                            v-if="successMessage"
                            class="flex min-h-[540px] items-center justify-center p-6 sm:p-10"
                        >
                            <div
                                class="w-full max-w-xl text-center"
                            >
                                <div
                                    class="relative mx-auto flex h-24 w-24 items-center justify-center"
                                >
                                    <div
                                        class="absolute inset-0 rounded-full bg-[#0FA7B4]/15 blur-xl"
                                    />

                                    <div
                                        class="relative flex h-20 w-20 items-center justify-center rounded-full border border-[#0FA7B4]/30 bg-[#0FA7B4]/10 text-[#35c4ce] shadow-[0_18px_50px_rgba(15,167,180,.18)]"
                                    >
                                        <CheckCircle2
                                            class="h-9 w-9"
                                        />
                                    </div>
                                </div>

                                <p
                                    class="mt-7 text-[9px] font-black uppercase tracking-[0.16em] text-[#35c4ce]"
                                >
                                    Solicitud recibida
                                </p>

                                <h3
                                    class="mt-2 text-3xl font-black tracking-[-0.04em] text-white sm:text-4xl"
                                >
                                    ¡Gracias!
                                </h3>

                                <p
                                    class="mx-auto mt-4 max-w-lg text-sm leading-7 text-white/45"
                                >
                                    {{ successMessage }}
                                </p>

                                <div
                                    class="mx-auto mt-7 max-w-md rounded-2xl border border-white/[0.07] bg-white/[0.03] p-5"
                                >
                                    <p
                                        class="text-xs font-black text-white/65"
                                    >
                                        {{ product.name }}
                                    </p>

                                    <p
                                        class="mt-1 text-[10px] text-white/28"
                                    >
                                        ADN Publicidad recibió la configuración y los archivos que acabas de enviar.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    class="mt-7 h-12 rounded-xl bg-[#0FA7B4] px-8 text-sm font-black text-white shadow-[0_16px_40px_rgba(15,167,180,.22)] transition hover:-translate-y-0.5"
                                    @click="requestClose"
                                >
                                    Cerrar
                                </button>
                            </div>
                        </div>

                        <!-- FORMULARIO -->

                        <form
                            v-else
                            class="grid lg:grid-cols-[300px_minmax(0,1fr)]"
                            @submit.prevent="submit"
                        >
                            <!-- RESUMEN -->

                            <aside
                                class="relative hidden overflow-hidden border-r border-white/[0.07] bg-black/10 p-7 lg:block"
                            >
                                <div
                                    class="absolute -left-20 top-20 h-52 w-52 rounded-full bg-[#0FA7B4]/7 blur-3xl"
                                />

                                <div
                                    class="relative sticky top-6"
                                >
                                    <div
                                        class="overflow-hidden rounded-[20px] border border-white/[0.08] bg-[#e9eded] shadow-[0_18px_45px_rgba(0,0,0,.18)]"
                                    >
                                        <img
                                            v-if="product.main_media"
                                            :src="product.main_media.url"
                                            :alt="
                                                product.main_media.alt_text
                                                ?? product.name
                                            "
                                            class="aspect-[4/3] w-full object-contain"
                                        >

                                        <div
                                            v-else
                                            class="flex aspect-[4/3] items-center justify-center"
                                        >
                                            <ImageIcon
                                                class="h-8 w-8 text-black/15"
                                            />
                                        </div>
                                    </div>

                                    <p
                                        class="mt-5 text-[9px] font-black uppercase tracking-[0.14em] text-[#ED7E24]"
                                    >
                                        {{ product.code }}
                                    </p>

                                    <h3
                                        class="mt-1 text-xl font-black text-white"
                                    >
                                        {{ product.name }}
                                    </h3>

                                    <p
                                        v-if="product.category"
                                        class="mt-1 text-xs text-white/30"
                                    >
                                        {{ product.category.name }}
                                    </p>

                                    <div
                                        class="mt-5 rounded-2xl border border-white/[0.07] bg-white/[0.025] p-4"
                                    >
                                        <p
                                            class="text-[8px] font-black uppercase tracking-[0.12em] text-white/20"
                                        >
                                            Configuración
                                        </p>

                                        <p
                                            class="mt-1 text-xs font-black text-white/55"
                                        >
                                            {{ product.quote_mode }}
                                        </p>

                                        <p
                                            v-if="product.measurement_unit"
                                            class="mt-1 text-[10px] text-white/25"
                                        >
                                            Unidad:
                                            {{ product.measurement_unit }}
                                        </p>
                                    </div>

                                    <div
                                        class="mt-5 flex items-start gap-3 rounded-2xl border border-[#0FA7B4]/12 bg-[#0FA7B4]/[0.045] p-4"
                                    >
                                        <ShieldCheck
                                            class="mt-0.5 h-4 w-4 shrink-0 text-[#35c4ce]"
                                        />

                                        <p
                                            class="text-[10px] leading-5 text-white/30"
                                        >
                                            Tus datos serán utilizados únicamente para atender esta solicitud comercial.
                                        </p>
                                    </div>
                                </div>
                            </aside>

                            <!-- CONTENIDO -->

                            <div
                                class="min-w-0 p-4 pb-0 sm:p-7 sm:pb-0"
                            >
                                <!-- Producto móvil -->

                                <div
                                    class="mb-6 flex items-center gap-3 rounded-2xl border border-white/[0.07] bg-white/[0.025] p-3 lg:hidden"
                                >
                                    <div
                                        class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-[#e9eded]"
                                    >
                                        <img
                                            v-if="product.main_media"
                                            :src="product.main_media.url"
                                            :alt="product.name"
                                            class="h-full w-full object-cover"
                                        >

                                        <ImageIcon
                                            v-else
                                            class="h-5 w-5 text-black/15"
                                        />
                                    </div>

                                    <div
                                        class="min-w-0"
                                    >
                                        <p
                                            class="truncate text-sm font-black text-white/75"
                                        >
                                            {{ product.name }}
                                        </p>

                                        <p
                                            class="mt-1 text-[9px] font-bold uppercase tracking-[0.09em] text-[#ED7E24]"
                                        >
                                            {{ product.code }}
                                            ·
                                            {{ product.quote_mode }}
                                        </p>
                                    </div>
                                </div>

                                <!-- DATOS CLIENTE -->

                                <section>
                                    <div
                                        class="flex items-center gap-3"
                                    >
                                        <span
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#0FA7B4]/10 text-[#35c4ce]"
                                        >
                                            <UserRound
                                                class="h-4 w-4"
                                            />
                                        </span>

                                        <div>
                                            <p
                                                class="text-sm font-black text-white"
                                            >
                                                Tus datos
                                            </p>

                                            <p
                                                class="mt-0.5 text-[9px] text-white/25"
                                            >
                                                Para poder comunicarnos contigo.
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="mt-5 grid gap-4 sm:grid-cols-2"
                                    >
                                        <label
                                            class="sm:col-span-2"
                                        >
                                            <span
                                                class="mb-2 block text-xs font-black text-white/60"
                                            >
                                                Nombre completo
                                                <span class="text-[#E84657]">*</span>
                                            </span>

                                            <div
                                                class="relative"
                                            >
                                                <UserRound
                                                    class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                                                />

                                                <input
                                                    v-model="form.client_name"
                                                    type="text"
                                                    autocomplete="name"
                                                    maxlength="160"
                                                    placeholder="Tu nombre"
                                                    class="h-12 w-full rounded-xl border border-white/[0.09] bg-black/20 pl-11 pr-4 text-sm text-white outline-none shadow-inner transition placeholder:text-white/18 focus:border-[#0FA7B4]/60 focus:ring-4 focus:ring-[#0FA7B4]/5"
                                                >
                                            </div>

                                            <p
                                                v-if="errorFor('client_name')"
                                                class="mt-2 text-[10px] font-bold text-[#ff7280]"
                                            >
                                                {{ errorFor('client_name') }}
                                            </p>
                                        </label>

                                        <label>
                                            <span
                                                class="mb-2 block text-xs font-black text-white/60"
                                            >
                                                Teléfono
                                                <span class="text-[#E84657]">*</span>
                                            </span>

                                            <div class="relative">
                                                <Phone
                                                    class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                                                />

                                                <input
                                                    v-model="form.phone"
                                                    type="tel"
                                                    inputmode="tel"
                                                    autocomplete="tel"
                                                    maxlength="40"
                                                    placeholder="Ej. 9999-9999"
                                                    class="h-12 w-full rounded-xl border border-white/[0.09] bg-black/20 pl-11 pr-4 text-sm text-white outline-none placeholder:text-white/18 focus:border-[#0FA7B4]/60"
                                                >
                                            </div>

                                            <p
                                                v-if="errorFor('phone')"
                                                class="mt-2 text-[10px] font-bold text-[#ff7280]"
                                            >
                                                {{ errorFor('phone') }}
                                            </p>
                                        </label>

                                        <label>
                                            <span
                                                class="mb-2 block text-xs font-black text-white/60"
                                            >
                                                WhatsApp
                                                <span class="ml-1 text-white/20">
                                                    opcional
                                                </span>
                                            </span>

                                            <div class="relative">
                                                <Phone
                                                    class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                                                />

                                                <input
                                                    v-model="form.whatsapp"
                                                    type="tel"
                                                    inputmode="tel"
                                                    maxlength="40"
                                                    placeholder="Si es diferente"
                                                    class="h-12 w-full rounded-xl border border-white/[0.09] bg-black/20 pl-11 pr-4 text-sm text-white outline-none placeholder:text-white/18 focus:border-[#0FA7B4]/60"
                                                >
                                            </div>
                                        </label>

                                        <label>
                                            <span
                                                class="mb-2 block text-xs font-black text-white/60"
                                            >
                                                Correo
                                                <span class="ml-1 text-white/20">
                                                    opcional
                                                </span>
                                            </span>

                                            <div class="relative">
                                                <Mail
                                                    class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                                                />

                                                <input
                                                    v-model="form.email"
                                                    type="email"
                                                    inputmode="email"
                                                    autocomplete="email"
                                                    maxlength="190"
                                                    placeholder="correo@ejemplo.com"
                                                    class="h-12 w-full rounded-xl border border-white/[0.09] bg-black/20 pl-11 pr-4 text-sm text-white outline-none placeholder:text-white/18 focus:border-[#0FA7B4]/60"
                                                >
                                            </div>

                                            <p
                                                v-if="errorFor('email')"
                                                class="mt-2 text-[10px] font-bold text-[#ff7280]"
                                            >
                                                {{ errorFor('email') }}
                                            </p>
                                        </label>

                                        <label>
                                            <span
                                                class="mb-2 block text-xs font-black text-white/60"
                                            >
                                                Empresa
                                                <span class="ml-1 text-white/20">
                                                    opcional
                                                </span>
                                            </span>

                                            <div class="relative">
                                                <Building2
                                                    class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-white/20"
                                                />

                                                <input
                                                    v-model="form.company"
                                                    type="text"
                                                    autocomplete="organization"
                                                    maxlength="190"
                                                    placeholder="Nombre de empresa"
                                                    class="h-12 w-full rounded-xl border border-white/[0.09] bg-black/20 pl-11 pr-4 text-sm text-white outline-none placeholder:text-white/18 focus:border-[#0FA7B4]/60"
                                                >
                                            </div>
                                        </label>
                                    </div>
                                </section>

                                <div
                                    class="my-7 h-px bg-white/[0.06]"
                                />

                                <!-- PROYECTO -->

                                <section>
                                    <div
                                        class="flex items-center gap-3"
                                    >
                                        <span
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#E84657]/10 text-[#ff7280]"
                                        >
                                            <Sparkles
                                                class="h-4 w-4"
                                            />
                                        </span>

                                        <div>
                                            <p
                                                class="text-sm font-black text-white"
                                            >
                                                Tu proyecto
                                            </p>

                                            <p
                                                class="mt-0.5 text-[9px] text-white/25"
                                            >
                                                Configuración de {{ product.name }}.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-5 grid gap-5">
                                        <!-- CANTIDAD -->

                                        <div>
                                            <span
                                                class="mb-2 block text-xs font-black text-white/60"
                                            >
                                                Cantidad
                                                <span class="text-[#E84657]">*</span>
                                            </span>

                                            <div
                                                class="flex h-12 w-full max-w-[230px] overflow-hidden rounded-xl border border-white/[0.09] bg-black/20 shadow-inner"
                                            >
                                                <button
                                                    type="button"
                                                    class="flex w-12 items-center justify-center text-white/35 transition hover:bg-white/[0.05] hover:text-white"
                                                    @click="decreaseQuantity"
                                                >
                                                    <Minus
                                                        class="h-4 w-4"
                                                    />
                                                </button>

                                                <input
                                                    v-model.number="form.quantity"
                                                    type="number"
                                                    inputmode="numeric"
                                                    min="1"
                                                    step="1"
                                                    class="min-w-0 flex-1 border-x border-white/[0.07] bg-transparent text-center text-sm font-black text-white outline-none"
                                                >

                                                <button
                                                    type="button"
                                                    class="flex w-12 items-center justify-center text-white/35 transition hover:bg-white/[0.05] hover:text-white"
                                                    @click="increaseQuantity"
                                                >
                                                    <Plus
                                                        class="h-4 w-4"
                                                    />
                                                </button>
                                            </div>

                                            <p
                                                v-if="errorFor('quantity')"
                                                class="mt-2 text-[10px] font-bold text-[#ff7280]"
                                            >
                                                {{ errorFor('quantity') }}
                                            </p>
                                        </div>

                                        <!-- CAMPOS DINÁMICOS -->

                                        <div
                                            v-for="field in product.fields"
                                            :key="field.id"
                                        >
                                            <!-- Texto / número -->

                                            <label
                                                v-if="
                                                    [
                                                        'text',
                                                        'number',
                                                    ].includes(
                                                        field.field_type,
                                                    )
                                                "
                                                class="block"
                                            >
                                                <span
                                                    class="mb-2 block text-xs font-black text-white/60"
                                                >
                                                    {{ field.label }}

                                                    <span
                                                        v-if="field.required"
                                                        class="text-[#E84657]"
                                                    >
                                                        *
                                                    </span>

                                                    <span
                                                        v-if="field.unit"
                                                        class="ml-1 font-bold text-white/25"
                                                    >
                                                        ({{ field.unit }})
                                                    </span>
                                                </span>

                                                <input
                                                    :value="fieldValue(field)"
                                                    :type="
                                                        field.field_type ===
                                                        'number'
                                                            ? 'number'
                                                            : 'text'
                                                    "
                                                    :inputmode="
                                                        field.field_type ===
                                                        'number'
                                                            ? 'decimal'
                                                            : undefined
                                                    "
                                                    :min="field.min_value ?? undefined"
                                                    :max="field.max_value ?? undefined"
                                                    :step="field.step ?? undefined"
                                                    :placeholder="field.placeholder ?? ''"
                                                    class="h-12 w-full rounded-xl border border-white/[0.09] bg-black/20 px-4 text-sm text-white outline-none shadow-inner transition placeholder:text-white/18 focus:border-[#0FA7B4]/60 focus:ring-4 focus:ring-[#0FA7B4]/5"
                                                    @input="
                                                        updateValue(
                                                            field,
                                                            $event,
                                                        )
                                                    "
                                                >

                                                <p
                                                    v-if="field.help_text"
                                                    class="mt-2 text-[10px] leading-5 text-white/27"
                                                >
                                                    {{ field.help_text }}
                                                </p>

                                                <p
                                                    v-if="dynamicError(field)"
                                                    class="mt-2 text-[10px] font-bold text-[#ff7280]"
                                                >
                                                    {{ dynamicError(field) }}
                                                </p>
                                            </label>

                                            <!-- Textarea -->

                                            <label
                                                v-else-if="
                                                    field.field_type ===
                                                    'textarea'
                                                "
                                                class="block"
                                            >
                                                <span
                                                    class="mb-2 block text-xs font-black text-white/60"
                                                >
                                                    {{ field.label }}

                                                    <span
                                                        v-if="field.required"
                                                        class="text-[#E84657]"
                                                    >
                                                        *
                                                    </span>
                                                </span>

                                                <textarea
                                                    :value="fieldValue(field)"
                                                    rows="4"
                                                    :placeholder="field.placeholder ?? ''"
                                                    class="w-full resize-y rounded-xl border border-white/[0.09] bg-black/20 px-4 py-3 text-sm leading-6 text-white outline-none shadow-inner transition placeholder:text-white/18 focus:border-[#0FA7B4]/60 focus:ring-4 focus:ring-[#0FA7B4]/5"
                                                    @input="
                                                        updateValue(
                                                            field,
                                                            $event,
                                                        )
                                                    "
                                                />

                                                <p
                                                    v-if="field.help_text"
                                                    class="mt-2 text-[10px] leading-5 text-white/27"
                                                >
                                                    {{ field.help_text }}
                                                </p>

                                                <p
                                                    v-if="dynamicError(field)"
                                                    class="mt-2 text-[10px] font-bold text-[#ff7280]"
                                                >
                                                    {{ dynamicError(field) }}
                                                </p>
                                            </label>

                                            <!-- Lista -->

                                            <label
                                                v-else-if="
                                                    field.field_type ===
                                                    'select'
                                                "
                                                class="block"
                                            >
                                                <span
                                                    class="mb-2 block text-xs font-black text-white/60"
                                                >
                                                    {{ field.label }}

                                                    <span
                                                        v-if="field.required"
                                                        class="text-[#E84657]"
                                                    >
                                                        *
                                                    </span>
                                                </span>

                                                <select
                                                    :value="fieldValue(field)"
                                                    class="h-12 w-full rounded-xl border border-white/[0.09] bg-[#151d1f] px-4 text-sm text-white/75 outline-none shadow-inner transition focus:border-[#0FA7B4]/60"
                                                    @change="
                                                        updateValue(
                                                            field,
                                                            $event,
                                                        )
                                                    "
                                                >
                                                    <option value="">
                                                        Selecciona una opción
                                                    </option>

                                                    <option
                                                        v-for="option in field.options"
                                                        :key="option.id"
                                                        :value="option.value"
                                                    >
                                                        {{ option.label }}
                                                    </option>
                                                </select>

                                                <p
                                                    v-if="field.help_text"
                                                    class="mt-2 text-[10px] leading-5 text-white/27"
                                                >
                                                    {{ field.help_text }}
                                                </p>

                                                <p
                                                    v-if="dynamicError(field)"
                                                    class="mt-2 text-[10px] font-bold text-[#ff7280]"
                                                >
                                                    {{ dynamicError(field) }}
                                                </p>
                                            </label>

                                            <!-- Radio -->

                                            <fieldset
                                                v-else-if="
                                                    field.field_type ===
                                                    'radio'
                                                "
                                            >
                                                <legend
                                                    class="mb-2 text-xs font-black text-white/60"
                                                >
                                                    {{ field.label }}

                                                    <span
                                                        v-if="field.required"
                                                        class="text-[#E84657]"
                                                    >
                                                        *
                                                    </span>
                                                </legend>

                                                <div
                                                    class="grid gap-2 sm:grid-cols-2"
                                                >
                                                    <label
                                                        v-for="option in field.options"
                                                        :key="option.id"
                                                        class="flex cursor-pointer items-center gap-3 rounded-xl border border-white/[0.08] bg-black/15 p-3.5 transition hover:border-[#0FA7B4]/30 hover:bg-[#0FA7B4]/5"
                                                    >
                                                        <input
                                                            type="radio"
                                                            :name="field.field_key"
                                                            :value="option.value"
                                                            :checked="
                                                                fieldValue(
                                                                    field,
                                                                ) ===
                                                                option.value
                                                            "
                                                            class="accent-[#0FA7B4]"
                                                            @change="
                                                                setOptionValue(
                                                                    field,
                                                                    option.value,
                                                                )
                                                            "
                                                        >

                                                        <span
                                                            class="text-xs font-bold text-white/60"
                                                        >
                                                            {{ option.label }}
                                                        </span>
                                                    </label>
                                                </div>

                                                <p
                                                    v-if="dynamicError(field)"
                                                    class="mt-2 text-[10px] font-bold text-[#ff7280]"
                                                >
                                                    {{ dynamicError(field) }}
                                                </p>
                                            </fieldset>

                                            <!-- Checkbox -->

                                            <label
                                                v-else-if="
                                                    field.field_type ===
                                                    'checkbox'
                                                "
                                                class="flex cursor-pointer items-center justify-between gap-4 rounded-xl border border-white/[0.08] bg-black/15 p-4 transition hover:border-[#0FA7B4]/30 hover:bg-[#0FA7B4]/5"
                                            >
                                                <div>
                                                    <p
                                                        class="text-xs font-black text-white/65"
                                                    >
                                                        {{ field.label }}

                                                        <span
                                                            v-if="field.required"
                                                            class="text-[#E84657]"
                                                        >
                                                            *
                                                        </span>
                                                    </p>

                                                    <p
                                                        v-if="field.help_text"
                                                        class="mt-1 text-[10px] leading-5 text-white/27"
                                                    >
                                                        {{ field.help_text }}
                                                    </p>
                                                </div>

                                                <input
                                                    type="checkbox"
                                                    :checked="checkboxValue(field)"
                                                    class="h-4 w-4 shrink-0 accent-[#0FA7B4]"
                                                    @change="
                                                        updateCheckbox(
                                                            field,
                                                            $event,
                                                        )
                                                    "
                                                >
                                            </label>

                                            <!-- ARCHIVOS MÚLTIPLES -->

                                            <div
                                                v-else-if="
                                                    field.field_type ===
                                                    'file'
                                                "
                                                class="block"
                                            >
                                                <div
                                                    class="mb-2 flex flex-wrap items-center justify-between gap-2"
                                                >
                                                    <span
                                                        class="text-xs font-black text-white/60"
                                                    >
                                                        {{ field.label }}

                                                        <span
                                                            v-if="field.required"
                                                            class="text-[#E84657]"
                                                        >
                                                            *
                                                        </span>
                                                    </span>

                                                    <span
                                                        class="rounded-lg bg-[#0FA7B4]/10 px-2.5 py-1 text-[8px] font-black uppercase tracking-[0.08em] text-[#35c4ce]"
                                                    >
                                                        {{
                                                            selectedFiles(
                                                                field,
                                                            ).length
                                                        }}
                                                        /
                                                        {{ fileLimit() }}
                                                        archivos
                                                    </span>
                                                </div>

                                                <label
                                                    class="flex min-h-16 cursor-pointer items-center gap-3 rounded-xl border border-dashed border-[#0FA7B4]/35 bg-[#0FA7B4]/[0.06] px-4 transition hover:border-[#0FA7B4]/65 hover:bg-[#0FA7B4]/10"
                                                >
                                                    <span
                                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#0FA7B4]/10 text-[#35c4ce]"
                                                    >
                                                        <FileUp
                                                            class="h-4 w-4"
                                                        />
                                                    </span>

                                                    <span
                                                        class="min-w-0 flex-1"
                                                    >
                                                        <span
                                                            class="block text-xs font-black text-white/55"
                                                        >
                                                            {{
                                                                selectedFiles(
                                                                    field,
                                                                ).length
                                                                    ? 'Agregar más archivos'
                                                                    : 'Seleccionar archivos'
                                                            }}
                                                        </span>

                                                        <span
                                                            class="mt-1 block text-[9px] leading-4 text-white/25"
                                                        >
                                                            Puedes adjuntar hasta
                                                            {{ fileLimit() }}
                                                            archivo{{
                                                                fileLimit() === 1
                                                                    ? ''
                                                                    : 's'
                                                            }}
                                                            para
                                                            {{ form.quantity }}
                                                            unidad{{
                                                                form.quantity === 1
                                                                    ? ''
                                                                    : 'es'
                                                            }}.
                                                            Máximo 50 MB cada uno.
                                                        </span>
                                                    </span>

                                                    <input
                                                        type="file"
                                                        multiple
                                                        class="sr-only"
                                                        @change="
                                                            updateFiles(
                                                                field,
                                                                $event,
                                                            )
                                                        "
                                                    >
                                                </label>

                                                <!-- LISTA DE ARCHIVOS -->

                                                <div
                                                    v-if="
                                                        selectedFiles(
                                                            field,
                                                        ).length
                                                    "
                                                    class="mt-3 grid gap-2"
                                                >
                                                    <article
                                                        v-for="(
                                                            file,
                                                            index
                                                        ) in selectedFiles(
                                                            field,
                                                        )"
                                                        :key="
                                                            fileIdentity(
                                                                file,
                                                            )
                                                        "
                                                        class="group flex min-w-0 items-center gap-3 rounded-xl border border-white/[0.07] bg-black/15 p-3"
                                                    >
                                                        <span
                                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#0FA7B4]/10 text-[#35c4ce]"
                                                        >
                                                            <FileUp
                                                                class="h-4 w-4"
                                                            />
                                                        </span>

                                                        <div
                                                            class="min-w-0 flex-1"
                                                        >
                                                            <p
                                                                class="truncate text-[10px] font-black text-white/60"
                                                            >
                                                                {{
                                                                    index +
                                                                    1
                                                                }}.
                                                                {{ file.name }}
                                                            </p>

                                                            <p
                                                                class="mt-1 text-[8px] text-white/23"
                                                            >
                                                                {{
                                                                    formatFileSize(
                                                                        file.size,
                                                                    )
                                                                }}
                                                            </p>
                                                        </div>

                                                        <button
                                                            type="button"
                                                            title="Quitar archivo"
                                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-white/[0.06] text-white/22 transition hover:border-[#E84657]/30 hover:bg-[#E84657]/10 hover:text-[#ff7280]"
                                                            @click="
                                                                removeFile(
                                                                    field,
                                                                    index,
                                                                )
                                                            "
                                                        >
                                                            <X
                                                                class="h-3.5 w-3.5"
                                                            />
                                                        </button>
                                                    </article>
                                                </div>

                                                <p
                                                    v-if="field.help_text"
                                                    class="mt-2 text-[10px] leading-5 text-white/27"
                                                >
                                                    {{ field.help_text }}
                                                </p>

                                                <p
                                                    v-if="dynamicError(field)"
                                                    class="mt-2 text-[10px] font-bold text-[#ff7280]"
                                                >
                                                    {{ dynamicError(field) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </section>

                                <div
                                    class="my-7 h-px bg-white/[0.06]"
                                />

                                <!-- Consentimiento -->

                                <label
                                    class="flex cursor-pointer items-start gap-3 rounded-2xl border border-white/[0.07] bg-white/[0.025] p-4"
                                >
                                    <input
                                        v-model="form.privacy_consent"
                                        type="checkbox"
                                        class="mt-0.5 h-4 w-4 shrink-0 accent-[#0FA7B4]"
                                    >

                                    <div>
                                        <p
                                            class="text-xs font-bold leading-5 text-white/48"
                                        >
                                            Acepto que ADN Publicidad utilice estos datos para atender esta solicitud de cotización.
                                        </p>

                                        <p
                                            class="mt-1 text-[9px] leading-4 text-white/20"
                                        >
                                            La información enviada será utilizada para dar seguimiento a tu solicitud.
                                        </p>
                                    </div>
                                </label>

                                <p
                                    v-if="errorFor('privacy_consent')"
                                    class="mt-2 text-[10px] font-bold text-[#ff7280]"
                                >
                                    {{ errorFor('privacy_consent') }}
                                </p>

                                <!-- Enviar -->

                                <div
                                    class="sticky bottom-0 z-10 -mx-4 mt-6 border-t border-white/[0.05] bg-[linear-gradient(to_top,#111819_72%,rgba(17,24,25,.92)_86%,transparent)] px-4 pb-[max(1rem,env(safe-area-inset-bottom))] pt-5 sm:-mx-7 sm:px-7 sm:pb-7 lg:static lg:-mx-0 lg:border-0 lg:bg-none lg:px-0 lg:pb-7"
                                >
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="group flex h-[54px] w-full items-center justify-center gap-2 rounded-xl bg-[#0FA7B4] px-5 text-sm font-black text-white shadow-[0_16px_40px_rgba(15,167,180,.22)] transition duration-300 hover:-translate-y-0.5 hover:bg-[#10b3c1] disabled:cursor-wait disabled:translate-y-0 disabled:opacity-55"
                                    >
                                        <Send
                                            class="h-4 w-4 transition group-hover:translate-x-0.5"
                                        />

                                        {{
                                            form.processing
                                                ? 'Enviando solicitud...'
                                                : `Cotizar ${product.name}`
                                        }}
                                    </button>

                                    <div
                                        class="mt-3 flex items-center justify-center gap-2 text-[9px] text-white/22"
                                    >
                                        <ShieldCheck
                                            class="h-3.5 w-3.5"
                                        />

                                        Tus archivos se almacenan de forma privada.
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.adn-quote-enter-active,
.adn-quote-leave-active {
    transition:
        opacity 280ms ease;
}

.adn-quote-enter-active .quote-dialog,
.adn-quote-leave-active .quote-dialog {
    transition:
        transform 380ms cubic-bezier(0.22, 1, 0.36, 1),
        opacity 280ms ease,
        filter 280ms ease;
}

.adn-quote-enter-from,
.adn-quote-leave-to {
    opacity:
        0;
}

.adn-quote-enter-from .quote-dialog {
    opacity:
        0;

    transform:
        translateY(28px)
        scale(0.965);

    filter:
        blur(6px);
}

.adn-quote-leave-to .quote-dialog {
    opacity:
        0;

    transform:
        translateY(18px)
        scale(0.975);

    filter:
        blur(4px);
}

.quote-scroll {
    scrollbar-width:
        thin;

    scrollbar-color:
        rgba(15, 167, 180, 0.28)
        transparent;
}

.quote-scroll::-webkit-scrollbar {
    width:
        6px;
}

.quote-scroll::-webkit-scrollbar-track {
    background:
        transparent;
}

.quote-scroll::-webkit-scrollbar-thumb {
    border-radius:
        999px;

    background:
        rgba(15, 167, 180, 0.28);
}

@media (max-width: 639px) {
    .adn-quote-enter-from .quote-dialog {
        opacity:
            0;

        transform:
            translateY(100%);

        filter:
            blur(2px);
    }

    .adn-quote-leave-to .quote-dialog {
        opacity:
            0;

        transform:
            translateY(100%);

        filter:
            blur(2px);
    }
}

@media (prefers-reduced-motion: reduce) {
    .adn-quote-enter-active,
    .adn-quote-leave-active,
    .adn-quote-enter-active .quote-dialog,
    .adn-quote-leave-active .quote-dialog {
        transition-duration:
            1ms;
    }
}
</style>