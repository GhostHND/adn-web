import {
    BarChart3,
    BookOpenText,
    Boxes,
    BriefcaseBusiness,
    FileImage,
    FolderTree,
    House,
    Layers3,
    Megaphone,
    MessageSquareText,
    PackageSearch,
    Settings,
    ShieldCheck,
} from '@lucide/vue';

import type {
    Component,
} from 'vue';

export interface EditorNavigationItem {
    label: string;
    icon: Component;
    href: string | null;
    enabled: boolean;
    indent?: boolean;
    match?: 'exact' | 'prefix';
}

export interface EditorNavigationSection {
    label: string;
    items: EditorNavigationItem[];
}

export const editorNavigation:
    EditorNavigationSection[] =
[
    {
        label:
            'Principal',

        items: [
            {
                label:
                    'Dashboard',

                icon:
                    BarChart3,

                href:
                    '/editor-preview',

                enabled:
                    true,

                match:
                    'exact',
            },
        ],
    },

    {
        label:
            'Sitio web',

        items: [
            {
                label:
                    'Inicio',

                icon:
                    House,

                href:
                    '/editor-preview/paginas/inicio/editar',

                enabled:
                    true,

                match:
                    'prefix',
            },

            {
                label:
                    'Sobre nosotros',

                icon:
                    BookOpenText,

                href:
                    '/editor-preview/paginas/sobre-nosotros/editar',

                enabled:
                    true,

                match:
                    'prefix',
            },

            {
                label:
                    'Servicios',

                icon:
                    Layers3,

                href:
                    '/editor-preview/paginas/servicios/editar',

                enabled:
                    true,

                match:
                    'prefix',
            },

            {
                label:
                    'Catálogo',

                icon:
                    Boxes,

                href:
                    '/editor-preview/catalogo',

                enabled:
                    true,

                match:
                    'exact',
            },

            {
                label:
                    'Productos',

                icon:
                    PackageSearch,

                href:
                    '/editor-preview/catalogo/productos',

                enabled:
                    true,

                indent:
                    true,

                match:
                    'prefix',
            },

            {
                label:
                    'Categorías',

                icon:
                    FolderTree,

                href:
                    '/editor-preview/catalogo/categorias',

                enabled:
                    true,

                indent:
                    true,

                match:
                    'prefix',
            },

            {
                label:
                    'Portafolio',

                icon:
                    BriefcaseBusiness,

                href:
                    '/editor-preview/portafolio/proyectos',

                enabled:
                    true,

                match:
                    'prefix',
            },

            {
                label:
                    'Categorías portafolio',

                icon:
                    FolderTree,

                href:
                    '/editor-preview/portafolio/categorias',

                enabled:
                    true,

                indent:
                    true,

                match:
                    'prefix',
            },

            {
                label:
                    'Contacto',

                icon:
                    MessageSquareText,

                href:
                    null,

                enabled:
                    false,
            },
        ],
    },

    {
        label:
            'Contenido',

        items: [
            {
                label:
                    'Multimedia',

                icon:
                    FileImage,

                href:
                    '/editor-preview/multimedia',

                enabled:
                    true,

                match:
                    'prefix',
            },

            {
                label:
                    'Publicidad',

                icon:
                    Megaphone,

                href:
                    '/editor-preview/publicidad',

                enabled:
                    true,

                match:
                    'prefix',
            },
        ],
    },

    {
        label:
            'Sistema',

        items: [
            {
                label:
                    'Configuración',

                icon:
                    Settings,

                href:
                    '/editor-preview/configuracion',

                enabled:
                    true,

                match:
                    'prefix',
            },

            {
                label:
                    'Integración',

                icon:
                    ShieldCheck,

                href:
                    null,

                enabled:
                    false,
            },
        ],
    },
];