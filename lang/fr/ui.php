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
        'nav' => [
            'overview' => 'Aperçu',
            'manage_subscription' => 'Gérer l’abonnement',
        ],
        'welcome_heading' => 'Bon retour parmi nous !',
        'welcome_body' => 'Consultez les détails de votre offre, gérez la facturation et ajustez votre abonnement en temps réel.',
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
];
