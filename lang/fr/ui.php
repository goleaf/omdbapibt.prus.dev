<?php

return [
    'nav' => [
        'brand' => [
            'primary' => 'OMDb',
            'secondary' => 'Stream',
        ],
        'links' => [
            'home' => 'Accueil',
            'browse' => 'Explorer',
            'pricing' => 'Tarifs',
            'components' => 'Composants UI',
            'account' => 'Compte',
            'admin' => 'Admin',
        ],
        'auth' => [
            'login' => 'Connexion',
            'register' => 'Rejoindre',
            'logout' => 'Déconnexion',
        ],
        'theme' => [
            'light' => 'Mode clair',
            'dark' => 'Mode sombre',
        ],
        'theme_toggle' => 'Changer de thème',
        'footer' => [
            'terms' => 'Conditions',
            'privacy' => 'Confidentialité',
            'support' => 'Support',
            'copyright' => '© :year OMDb Stream. Tous droits réservés.',
        ],
    ],
    'dashboard' => [
        'title' => 'Tableau de bord',
        'layout' => [
            'sidebar_heading' => 'Navigation',
            'default_header' => 'Vue d’ensemble du tableau de bord',
        ],
        'nav' => [
            'overview' => 'Aperçu',
            'manage_subscription' => 'Gérer l’abonnement',
        ],
        'welcome_heading' => 'Bon retour parmi nous !',
        'welcome_body' => 'Consultez les détails de votre offre, gérez la facturation et ajustez votre abonnement en temps réel.',
        'insights_card' => [
            'title' => 'Aperçu de l’offre',
            'subscription_status' => 'Statut de l’abonnement',
            'trial_days' => 'Jours d’essai',
            'next_invoice' => 'Prochaine facture',
        ],
        'cards' => [
            'manage_subscription' => 'Gérer l’abonnement',
            'watchlist' => 'Liste de suivi',
        ],
        'trial' => [
            'active_title' => 'Votre essai gratuit est actif.',
            'active_body' => 'Profitez d’un accès complet jusqu’au :date. Nous enverrons des rappels avant le début de la facturation.',
            'cta' => 'Commencer l’essai de :days jours',
            'intro_title' => 'Lancez votre essai gratuit de :days jours.',
            'intro_body' => 'Débloquez chaque détail de film, des filtres premium et des recommandations personnalisées pendant votre évaluation.',
            'missing_price' => 'Ajoutez votre identifiant de tarif Stripe à :key pour activer les abonnements.',
            'cancel_notice' => 'Annulez à tout moment avant la fin de l’essai pour éviter des frais.',
        ],
        'subscriber' => [
            'thanks_title' => 'Merci pour votre abonnement !',
            'thanks_body' => 'Profitez d’un accès illimité aux données détaillées, aux listes et aux analyses personnalisées.',
        ],
        'grace' => [
            'title' => 'Votre abonnement touche à sa fin.',
            'body' => 'L’accès reste disponible jusqu’au :date. Reprenez l’offre dans Stripe si vous changez d’avis.',
        ],
        'inactive' => [
            'title' => 'Abonnement inactif.',
            'body' => 'Réabonnez-vous depuis le portail de facturation pour retrouver l’accès premium.',
        ],
    ],
    'filters' => [
        'heading' => 'Filtres avancés',
        'description' => 'Affinez votre flux de découverte par genres, langues et années de sortie.',
        'type_label' => 'Type',
        'types' => [
            'movies' => 'Films',
            'shows' => 'Séries',
        ],
        'genre_label' => 'Genre',
        'year_label' => 'Année',
        'language_label' => 'Langue',
        'sort_label' => 'Trier par',
        'sort_options' => [
            'popularity_desc' => 'Popularité',
            'vote_average_desc' => 'Note',
            'release_date_desc' => 'Plus récents',
            'release_date_asc' => 'Plus anciens',
        ],
        'results_title' => 'Aperçu des résultats',
        'results_summary' => 'Filtrage de :type :genre sortis en :year.',
        'apply' => 'Appliquer',
    ],
    'people' => [
        'page_title' => 'Fiche de la personne',
        'no_biography' => 'Biographie encore indisponible.',
        'profile_alt' => 'Portrait de :name',
        'poster_alt' => 'Affiche principale de :name',
        'vitals_heading' => 'Informations clés',
        'born_label' => 'Naissance',
        'place_label' => 'Lieu',
        'known_for_label' => 'Connu pour',
        'popularity_label' => 'Popularité',
        'biography_heading' => 'Biographie',
        'movies_heading' => 'Films',
        'tv_heading' => 'Télévision',
        'credits_heading' => 'Crédits :type',
        'credit_types' => [
            'cast' => 'Distribution',
            'crew' => 'Équipe technique',
        ],
    ],
    'admin' => [
        'ui_translations' => [
            'title' => 'Gestionnaire de traductions UI',
            'description' => 'Gérez les textes localisés de l’interface pour toutes les langues prises en charge et synchronisez les mises à jour vers le cache Redis.',
            'actions' => [
                'new' => 'Nouvelle traduction',
                'refresh' => 'Rafraîchir le cache',
                'refreshing' => 'Rafraîchissement…',
                'save' => 'Enregistrer la traduction',
                'saving' => 'Enregistrement…',
                'cancel_edit' => 'Annuler la modification',
                'edit' => 'Modifier',
                'delete' => 'Supprimer',
                'confirm' => 'Confirmer',
                'cancel' => 'Annuler',
            ],
            'form' => [
                'heading' => [
                    'create' => 'Créer une traduction',
                    'edit' => 'Modifier la traduction',
                ],
                'instructions' => 'Définissez le groupe, la clé et les valeurs localisées. La langue de secours est obligatoire pour chaque entrée.',
                'fields' => [
                    'group' => 'Groupe',
                    'key' => 'Clé',
                    'value' => 'Traduction',
                ],
                'fallback_hint' => 'Cette langue est obligatoire.',
            ],
            'table' => [
                'heading' => 'Traductions enregistrées',
                'columns' => [
                    'group' => 'Groupe',
                    'key' => 'Clé',
                    'actions' => 'Actions',
                ],
                'locale_count' => '{1} :count langue|[2,*] :count langues',
                'empty' => 'Aucune traduction d’interface n’a encore été créée.',
            ],
            'status' => [
                'saved' => 'Traduction enregistrée.',
                'updated' => 'Traduction mise à jour.',
                'deleted' => 'Traduction supprimée.',
                'cache_refreshed' => 'Cache des traductions rafraîchi.',
            ],
            'validation' => [
                'group_required' => 'Le champ groupe est obligatoire.',
                'key_required' => 'Le champ clé est obligatoire.',
                'key_unique' => 'Une traduction avec ce groupe et cette clé existe déjà.',
                'value_required' => 'Une valeur de traduction est requise pour la langue :locale.',
            ],
        ],
    ],
];
