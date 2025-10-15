<?php

return [
    'nav' => [
        'brand' => [
            'primary' => 'OMDb',
            'secondary' => 'Stream',
        ],
        'links' => [
            'home' => 'Inicio',
            'browse' => 'Explorar',
            'pricing' => 'Precios',
            'components' => 'Componentes UI',
            'account' => 'Cuenta',
            'admin' => 'Administración',
        ],
        'auth' => [
            'login' => 'Iniciar sesión',
            'register' => 'Únete ahora',
            'logout' => 'Cerrar sesión',
        ],
        'theme' => [
            'light' => 'Modo claro',
            'dark' => 'Modo oscuro',
        ],
        'theme_toggle' => 'Cambiar tema',
        'footer' => [
            'terms' => 'Términos',
            'privacy' => 'Privacidad',
            'support' => 'Soporte',
            'copyright' => '© :year OMDb Stream. Todos los derechos reservados.',
        ],
    ],
    'dashboard' => [
        'title' => 'Panel',
        'layout' => [
            'sidebar_heading' => 'Navegación',
            'default_header' => 'Resumen del panel',
        ],
        'nav' => [
            'overview' => 'Resumen',
            'manage_subscription' => 'Administrar suscripción',
        ],
        'welcome_heading' => '¡Bienvenido de nuevo!',
        'welcome_body' => 'Revisa los detalles de tu plan, gestiona la facturación y realiza cambios en tu suscripción en tiempo real.',
        'insights_card' => [
            'title' => 'Información del plan',
            'subscription_status' => 'Estado de la suscripción',
            'trial_days' => 'Días de prueba',
            'next_invoice' => 'Próxima factura',
        ],
        'cards' => [
            'manage_subscription' => 'Administrar suscripción',
            'watchlist' => 'Lista de seguimiento',
        ],
        'trial' => [
            'active_title' => 'Tu prueba gratuita está activa.',
            'active_body' => 'Disfruta de acceso completo hasta :date. Te enviaremos recordatorios antes de que comience la facturación.',
            'cta' => 'Inicia la prueba de :days días',
            'intro_title' => 'Comienza tu prueba gratuita de :days días.',
            'intro_body' => 'Desbloquea cada detalle de películas, filtros premium y recomendaciones curadas mientras evalúas la plataforma.',
            'missing_price' => 'Añade tu identificador de precio de Stripe a :key para habilitar las suscripciones.',
            'cancel_notice' => 'Cancela en cualquier momento antes de que finalice la prueba para evitar cargos.',
        ],
        'subscriber' => [
            'thanks_title' => '¡Gracias por ser suscriptor!',
            'thanks_body' => 'Disfruta de acceso ilimitado a datos detallados, listas y perspectivas personalizadas.',
        ],
        'grace' => [
            'title' => 'Tu suscripción está programada para finalizar.',
            'body' => 'El acceso estará disponible hasta :date. Reactiva el plan en Stripe si cambias de opinión.',
        ],
        'inactive' => [
            'title' => 'Suscripción inactiva.',
            'body' => 'Suscríbete nuevamente desde el portal de facturación para recuperar el acceso premium.',
        ],
    ],
    'filters' => [
        'heading' => 'Filtros avanzados',
        'description' => 'Ajusta tu feed de descubrimiento con géneros, idiomas y años de estreno.',
        'type_label' => 'Tipo',
        'types' => [
            'movies' => 'Películas',
            'shows' => 'Series',
        ],
        'genre_label' => 'Género',
        'year_label' => 'Año',
        'language_label' => 'Idioma',
        'sort_label' => 'Ordenar por',
        'sort_options' => [
            'popularity_desc' => 'Popularidad',
            'vote_average_desc' => 'Valoración',
            'release_date_desc' => 'Más recientes',
            'release_date_asc' => 'Más antiguas',
        ],
        'results_title' => 'Vista previa de resultados',
        'results_summary' => 'Filtrando :type de :genre estrenados en :year.',
        'apply' => 'Aplicar',
    ],
    'people' => [
        'page_title' => 'Detalle de persona',
        'no_biography' => 'Biografía no disponible por ahora.',
        'profile_alt' => 'Retrato de :name',
        'poster_alt' => 'Póster destacado de :name',
        'vitals_heading' => 'Datos clave',
        'born_label' => 'Nacimiento',
        'place_label' => 'Lugar',
        'known_for_label' => 'Reconocido por',
        'popularity_label' => 'Popularidad',
        'biography_heading' => 'Biografía',
        'movies_heading' => 'Películas',
        'tv_heading' => 'TV',
        'credits_heading' => 'Créditos de :type',
        'credit_types' => [
            'cast' => 'Elenco',
            'crew' => 'Equipo técnico',
        ],
    ],
    'admin' => [
        'ui_translations' => [
            'title' => 'Administrador de traducciones de la interfaz',
            'description' => 'Gestiona el contenido localizado de la interfaz en los idiomas admitidos y sincroniza las actualizaciones con la caché en Redis.',
            'actions' => [
                'new' => 'Nueva traducción',
                'refresh' => 'Actualizar caché',
                'refreshing' => 'Actualizando…',
                'save' => 'Guardar traducción',
                'saving' => 'Guardando…',
                'cancel_edit' => 'Cancelar edición',
                'edit' => 'Editar',
                'delete' => 'Eliminar',
                'confirm' => 'Confirmar',
                'cancel' => 'Cancelar',
            ],
            'form' => [
                'heading' => [
                    'create' => 'Crear traducción',
                    'edit' => 'Editar traducción',
                ],
                'instructions' => 'Define el grupo, la clave y los valores localizados. El idioma de respaldo es obligatorio para cada registro.',
                'fields' => [
                    'group' => 'Grupo',
                    'key' => 'Clave',
                    'value' => 'Traducción',
                ],
                'fallback_hint' => 'Este idioma es obligatorio.',
            ],
            'table' => [
                'heading' => 'Traducciones almacenadas',
                'columns' => [
                    'group' => 'Grupo',
                    'key' => 'Clave',
                    'actions' => 'Acciones',
                ],
                'locale_count' => '{1} :count idioma|[2,*] :count idiomas',
                'empty' => 'Aún no se han creado traducciones de la interfaz.',
            ],
            'status' => [
                'saved' => 'Traducción guardada.',
                'updated' => 'Traducción actualizada.',
                'deleted' => 'Traducción eliminada.',
                'cache_refreshed' => 'Caché de traducciones actualizada.',
            ],
            'validation' => [
                'group_required' => 'El campo grupo es obligatorio.',
                'key_required' => 'El campo clave es obligatorio.',
                'key_unique' => 'Ya existe una traducción con este grupo y clave.',
                'value_required' => 'Se requiere un valor de traducción para el idioma :locale.',
            ],
        ],
    ],
];
