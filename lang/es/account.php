<?php

return [
    'profile' => [
        'meta' => [
            'title' => 'Perfil',
            'header' => 'Tu perfil',
            'subheader' => 'Consulta las preferencias, favoritos y conexiones sociales que influyen en tus recomendaciones.',
        ],
        'values' => [
            'not_set' => 'Sin configurar',
            'enabled' => 'Activado',
            'disabled' => 'Desactivado',
            'subscribed' => 'Suscrito',
            'unsubscribed' => 'No suscrito',
            'opted_in' => 'Con consentimiento',
            'opted_out' => 'Sin consentimiento',
        ],
        'sections' => [
            'preferences' => [
                'title' => 'Preferencias',
                'description' => 'Idioma, reproducción y ajustes de comunicaciones.',
                'empty' => 'Aún no has configurado tus preferencias.',
                'items' => [
                    'preferred_interface_language' => 'Idioma preferido de la interfaz',
                    'preferred_audio_language' => 'Idioma preferido del audio',
                    'preferred_subtitle_language' => 'Idioma preferido de los subtítulos',
                    'content_maturity' => 'Filtro de madurez de contenido',
                    'autoplay_next_episode' => 'Reproducción automática del siguiente episodio',
                    'autoplay_trailers' => 'Reproducción automática de tráilers al explorar',
                    'newsletter_opt_in' => 'Boletín del producto',
                    'marketing_opt_in' => 'Promociones de socios',
                ],
            ],
            'favorites' => [
                'title' => 'Favoritos',
                'description' => 'Tus géneros, historias y creadores de cabecera.',
                'empty' => 'Los favoritos aparecerán cuando los compartas.',
                'items' => [
                    'favorite_genre' => 'Género favorito',
                    'favorite_movie' => 'Película favorita',
                    'favorite_tv_show' => 'Serie favorita',
                    'favorite_actor' => 'Intérprete favorito',
                    'favorite_director' => 'Director favorito',
                    'favorite_quote' => 'Frase favorita',
                ],
            ],
            'personal' => [
                'title' => 'Información personal',
                'description' => 'Detalles que personalizan notificaciones y recomendaciones.',
                'empty' => 'Los datos personales aún no están configurados.',
                'items' => [
                    'display_name' => 'Nombre para mostrar',
                    'tagline' => 'Lema',
                    'location' => 'Ubicación',
                    'timezone' => 'Zona horaria',
                    'birthday' => 'Cumpleaños',
                    'bio' => 'Biografía',
                    'discord_handle' => 'Usuario de Discord',
                ],
            ],
            'social' => [
                'title' => 'Enlaces sociales',
                'description' => 'Conecta tus perfiles y espacios creativos.',
                'empty' => 'Añade perfiles sociales para compartir tu lista con amistades.',
                'items' => [
                    'website_url' => 'Sitio web personal',
                    'twitter_url' => 'Twitter',
                    'instagram_url' => 'Instagram',
                    'tiktok_url' => 'TikTok',
                    'youtube_url' => 'YouTube',
                    'letterboxd_url' => 'Letterboxd',
                ],
            ],
        ],
    ],
];
