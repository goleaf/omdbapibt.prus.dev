<?php

return [
    'profile' => [
        'meta' => [
            'title' => 'Profil',
            'header' => 'Votre profil',
            'subheader' => 'Découvrez les préférences, favoris et liens sociaux qui affinent vos recommandations.',
        ],
        'values' => [
            'not_set' => 'Non défini',
            'enabled' => 'Activé',
            'disabled' => 'Désactivé',
            'subscribed' => 'Abonné',
            'unsubscribed' => 'Désabonné',
            'opted_in' => 'Accepté',
            'opted_out' => 'Refusé',
        ],
        'sections' => [
            'preferences' => [
                'title' => 'Préférences',
                'description' => 'Langue, lecture et paramètres de communication.',
                'empty' => 'Aucune préférence configurée pour le moment.',
                'items' => [
                    'preferred_interface_language' => 'Langue d’interface préférée',
                    'preferred_audio_language' => 'Langue audio préférée',
                    'preferred_subtitle_language' => 'Langue des sous-titres préférée',
                    'content_maturity' => 'Filtre de maturité du contenu',
                    'autoplay_next_episode' => 'Lecture automatique de l’épisode suivant',
                    'autoplay_trailers' => 'Lecture automatique des bandes-annonces en navigation',
                    'newsletter_opt_in' => 'Newsletter produit',
                    'marketing_opt_in' => 'Promotions partenaires',
                ],
            ],
            'favorites' => [
                'title' => 'Favoris',
                'description' => 'Vos genres, histoires et créateurs de référence.',
                'empty' => 'Les favoris apparaîtront lorsque vous les partagerez.',
                'items' => [
                    'favorite_genre' => 'Genre préféré',
                    'favorite_movie' => 'Film préféré',
                    'favorite_tv_show' => 'Série préférée',
                    'favorite_actor' => 'Interprète préféré',
                    'favorite_director' => 'Réalisateur préféré',
                    'favorite_quote' => 'Citation préférée',
                ],
            ],
            'personal' => [
                'title' => 'Informations personnelles',
                'description' => 'Des détails qui personnalisent notifications et recommandations.',
                'empty' => 'Les informations personnelles ne sont pas encore configurées.',
                'items' => [
                    'display_name' => 'Nom d’affichage',
                    'tagline' => 'Slogan',
                    'location' => 'Localisation',
                    'timezone' => 'Fuseau horaire',
                    'birthday' => 'Anniversaire',
                    'bio' => 'Biographie',
                    'discord_handle' => 'Identifiant Discord',
                ],
            ],
            'social' => [
                'title' => 'Liens sociaux',
                'description' => 'Connectez vos profils et espaces créatifs.',
                'empty' => 'Ajoutez vos profils sociaux pour partager votre liste avec vos amis.',
                'items' => [
                    'website_url' => 'Site personnel',
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
