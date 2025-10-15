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
        'nav' => [
            'overview' => 'Resumen',
            'manage_subscription' => 'Administrar suscripción',
        ],
        'welcome_heading' => '¡Bienvenido de nuevo!',
        'welcome_body' => 'Revisa los detalles de tu plan, gestiona la facturación y realiza cambios en tu suscripción en tiempo real.',
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
];
