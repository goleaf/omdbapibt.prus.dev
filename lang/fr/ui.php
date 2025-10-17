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
            'components' => 'Composants de l’interface utilisateur',
            'account' => 'Compte',
            'admin' => 'Administration',
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
        'menu' => [
            'label' => 'Navigation',
            'open' => 'Ouvrir la navigation',
            'close' => 'Fermer la navigation',
        ],
        'search' => [
            'placeholder' => 'Rechercher des films, séries, personnes...',
            'button' => 'Rechercher',
            'shortcut' => 'Appuyez sur :key pour rechercher',
            'clear' => 'Effacer la recherche',
            'no_results' => 'Aucun résultat trouvé',
            'searching' => 'Recherche en cours...',
            'mobile_open' => 'Ouvrir la recherche',
            'mobile_close' => 'Fermer la recherche',
        ],
        'user_menu' => [
            'dropdown_label' => 'Menu utilisateur',
            'profile' => 'Profil',
            'account' => 'Compte',
            'settings' => 'Paramètres',
            'logout' => 'Déconnexion',
            'view_profile' => 'Voir le profil',
        ],
        'footer' => [
            'terms' => 'Conditions',
            'agreements' => 'Accords',
            'privacy' => 'Confidentialité',
            'support' => 'Assistance',
            'copyright' => '© :year OMDb Stream. Tous droits réservés.',
        ],
    ],
    'admin' => [
        'panel' => [
            'title' => 'Panneau d’administration',
            'subtitle' => 'Gérez les ressources du catalogue, les métadonnées éditoriales et les taxonomies exposées sur le portail.',
            'actions' => [
                'create' => 'Créer',
                'update' => 'Enregistrer',
                'clear' => 'Vider le formulaire',
                'reset' => 'Réinitialiser',
                'edit' => 'Modifier',
                'delete' => 'Supprimer',
            ],
            'fields' => [
                'title' => 'Titre',
                'slug' => 'Identifiant lisible',
                'status' => 'Statut',
                'release_date' => 'Date de sortie',
                'vote_average' => 'Note moyenne',
                'adult' => 'Marqué comme contenu adulte',
                'search' => 'Recherche',
                'name' => 'Nom',
                'first_air_date' => 'Première diffusion',
                'department' => 'Département',
                'birthday' => 'Date de naissance',
                'gender' => 'Genre',
                'popularity' => 'Popularité',
                'tmdb_id' => 'ID TMDb',
                'code' => 'Code',
                'native_name' => 'Nom natif',
                'active' => 'Actif',
            ],
            'placeholders' => [
                'movie_title' => 'ex. The Matrix',
                'slug' => 'Laisser vide pour générer automatiquement',
                'status' => 'Prévu, Sorti, En production…',
                'search_movies' => 'Rechercher par titre ou slug…',
                'show_name' => 'ex. True Detective',
                'search_shows' => 'Rechercher une série…',
                'person_name' => 'ex. Keanu Reeves',
                'department' => 'Interprétation, Réalisation, Scénario…',
                'search_people' => 'Rechercher une personne…',
                'genre_name' => 'ex. Science-fiction',
                'tmdb_id' => 'Identifiant numérique TMDb',
                'search_genres' => 'Rechercher un genre…',
                'language_name' => 'ex. Anglais',
                'native_name' => 'ex. Français',
                'search_languages' => 'Rechercher une langue ou un code…',
                'country_name' => 'ex. États-Unis',
                'search_countries' => 'Rechercher un pays ou un code…',
                'search_tags' => 'Rechercher une étiquette ou un slug…',
            ],
            'table' => [
                'movie' => 'Film',
                'show' => 'Série',
                'person' => 'Personne',
                'genre' => 'Genre',
                'language' => 'Langue',
                'country' => 'Pays',
                'actions' => 'Actions',
                'empty' => 'Aucun enregistrement ne correspond aux filtres.',
            ],
            'tags' => [
                'fields' => [
                    'name_en' => 'Nom (anglais)',
                    'name_es' => 'Nom (espagnol)',
                    'name_fr' => 'Nom (français)',
                    'type' => 'Type d’étiquette',
                ],
                'placeholders' => [
                    'name_en' => 'ex. Primée',
                    'name_es' => 'Traduction optionnelle…',
                    'name_fr' => 'Traduction optionnelle…',
                    'search' => 'Rechercher une étiquette ou un slug…',
                ],
                'types' => [
                    'system' => 'Système',
                    'community' => 'Communauté',
                ],
                'merge' => [
                    'title' => 'Fusionner les étiquettes en double',
                    'subtitle' => 'Rassemblez les vocabulaires qui se chevauchent et gardez les rails éditoriaux propres.',
                    'source' => 'ID de l’étiquette source',
                    'target' => 'ID de l’étiquette cible',
                    'placeholders' => [
                        'source' => 'ID à fusionner',
                        'target' => 'ID à conserver',
                    ],
                    'action' => 'Fusionner les étiquettes',
                ],
                'table' => [
                    'tag' => 'Étiquette',
                    'type' => 'Type',
                    'usage' => 'Utilisation',
                ],
            ],
            'labels' => [
                'active' => 'Actif',
                'inactive' => 'Inactif',
            ],
            'sections' => [
                'movies' => [
                    'title' => 'Éditeur de films',
                    'subtitle' => 'Maintenez les fiches, les dates de sortie et la cohérence du catalogue.',
                    'nav' => 'Films',
                    'heading' => 'Gérer les films',
                ],
                'tv_shows' => [
                    'title' => 'Éditeur de séries',
                    'subtitle' => 'Suivez les diffusions d’épisodes et leur disponibilité.',
                    'nav' => 'Séries',
                    'heading' => 'Gérer les séries',
                ],
                'people' => [
                    'title' => 'Annuaire des personnes',
                    'subtitle' => 'Gardez des crédits précis pour la distribution et l’équipe technique.',
                    'nav' => 'Personnes',
                    'heading' => 'Gérer les personnes',
                ],
                'genres' => [
                    'title' => 'Taxonomie des genres',
                    'subtitle' => 'Contrôlez le vocabulaire utilisé dans la découverte.',
                    'nav' => 'Genres',
                    'heading' => 'Gérer les genres',
                ],
                'languages' => [
                    'title' => 'Catalogue des langues',
                    'subtitle' => 'Configurez les langues audio et sous-titres disponibles.',
                    'nav' => 'Langues',
                    'heading' => 'Gérer les langues',
                ],
                'countries' => [
                    'title' => 'Registre des pays',
                    'subtitle' => 'Alignez les données d’origine sur les codes ISO.',
                    'nav' => 'Pays',
                    'heading' => 'Gérer les pays',
                ],
                'tags' => [
                    'title' => 'Curation des étiquettes',
                    'subtitle' => 'Modérez les étiquettes de découverte, fusionnez les doublons et pilotez les rails de mise en avant.',
                    'nav' => 'Étiquettes',
                    'heading' => 'Gérer les étiquettes',
                ],
            ],
            'people' => [
                'gender_unknown' => 'Non spécifié',
                'gender_female' => 'Féminin',
                'gender_male' => 'Masculin',
                'gender_non_binary' => 'Non binaire',
            ],
            'relationships' => [
                'title' => 'Taxonomies, étiquettes et langues',
                'subtitle' => 'Associez genres, étiquettes éditoriales, langues et pays d’origine à chaque fiche.',
                'suggestions' => 'Suggestions',
                'empty' => 'Aucune suggestion ne correspond à la recherche.',
                'genres' => [
                    'label' => 'Genres',
                    'help' => 'Utilisez des genres éditoriaux pour enrichir les filtres de découverte.',
                    'none' => 'Aucun genre sélectionné.',
                    'remove' => 'Retirer :name des genres sélectionnés',
                ],
                'tags' => [
                    'label' => 'Étiquettes',
                    'help' => 'Épinglez des étiquettes système ou éditoriales pour mettre des titres en avant.',
                    'none' => 'Aucune étiquette sélectionnée.',
                    'remove' => 'Retirer :name des étiquettes sélectionnées',
                ],
                'languages' => [
                    'label' => 'Langues',
                    'help' => 'Suivez les doublages et sous-titres disponibles pour chaque titre.',
                    'none' => 'Aucune langue sélectionnée.',
                    'remove' => 'Retirer :name des langues sélectionnées',
                ],
                'countries' => [
                    'label' => 'Pays',
                    'help' => 'Renseignez le pays de production pour la conformité et la programmation.',
                    'none' => 'Aucun pays sélectionné.',
                    'remove' => 'Retirer :name des pays sélectionnés',
                ],
            ],
        ],
    ],
    'dashboard' => [
        'title' => 'Tableau de bord',
        'layout' => [
            'sidebar_heading' => 'Menu',
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
        'type_label' => 'Catégorie',
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
    'pages' => [
        'terms' => [
            'title' => 'Conditions d’utilisation',
            'meta_description' => 'Lisez les règles qui s’appliquent à votre compte OMDb Stream.',
            'heading' => 'Conditions d’utilisation',
            'lede' => 'Ces conditions expliquent le fonctionnement du service et ce que vous pouvez attendre.',
            'intro' => 'En utilisant OMDb Stream, vous acceptez ces conditions ainsi que notre Politique de confidentialité. Prenez un moment pour comprendre vos responsabilités et les nôtres.',
            'sections' => [
                [
                    'title' => '1. Présentation de l’accord',
                    'paragraphs' => [
                        'Ces conditions d’utilisation forment un contrat entre vous et OMDb Stream pour toute utilisation du site.',
                        'Nous pouvons les mettre à jour lorsque la loi change ou que de nouvelles fonctionnalités arrivent. Nous vous informerons des changements importants et continuer à utiliser le service après cet avis vaut acceptation des nouvelles conditions.',
                    ],
                ],
                [
                    'title' => '2. Comptes et éligibilité',
                    'paragraphs' => [
                        'Vous devez avoir au moins 18 ans et être capable de conclure un contrat. Fournissez des informations exactes et maintenez-les à jour.',
                        'Protégez vos identifiants. Vous êtes responsable des actions des collaborateurs ou partenaires que vous invitez sur votre abonnement.',
                    ],
                ],
                [
                    'title' => '3. Abonnements et facturation',
                    'paragraphs' => [
                        'Les formules payantes se renouvellent automatiquement selon la cadence choisie. Les frais peuvent inclure abonnements, taxes et options que vous activez.',
                        'Vous pouvez annuler à tout moment depuis la page de facturation. Les remboursements sont traités conformément aux lois locales ou aux engagements pris lors de la commande.',
                    ],
                ],
                [
                    'title' => '4. Utilisation acceptable',
                    'paragraphs' => [
                        'Utilisez OMDb Stream uniquement à des fins légales et dans les limites prévues par nos API et notre interface. N’extrayez pas massivement de données et ne contournez pas la sécurité ou les limites de débit.',
                        'Nous pouvons suspendre ou mettre fin à l’accès si vous détournez le service, causez un préjudice à d’autres clients ou enfreignez la loi.',
                    ],
                ],
                [
                    'title' => '5. Fin du service',
                    'paragraphs' => [
                        'Vous pouvez fermer votre compte depuis les paramètres. Nous pouvons suspendre ou résilier l’accès en cas de paiement manquant ou de non-respect de ces conditions.',
                        'Les dispositions concernant les paiements, les limitations d’usage et le règlement des litiges restent applicables après la fermeture du compte.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Questions et contact',
                'body' => 'Écrivez-nous à :email si vous avez des questions sur ces conditions ou besoin d’explications supplémentaires.',
            ],
            'effective_date' => 'En vigueur depuis le 1er mai 2024.',
        ],
        'privacy' => [
            'title' => 'Politique de confidentialité',
            'meta_description' => 'Découvrez comment OMDb Stream collecte et utilise vos informations.',
            'heading' => 'Politique de confidentialité',
            'lede' => 'Nous expliquons ce que nous collectons, pourquoi nous le conservons et comment garder le contrôle.',
            'intro' => 'Cette Politique de confidentialité décrit les données personnelles qu’OMDb Stream traite pour faire fonctionner le service et les choix qui s’offrent à vous. Nous respectons les lois applicables et les bonnes pratiques du secteur.',
            'sections' => [
                [
                    'title' => '1. Données collectées',
                    'paragraphs' => [
                        'Nous collectons les informations que vous nous transmettez, les données générées lorsque vous utilisez le site ainsi que des éléments provenant de partenaires de confiance. Le détail dépend de votre usage d’OMDb Stream.',
                    ],
                    'items' => [
                        'Informations de compte (nom, adresse e-mail, organisation, préférences d’abonnement).',
                        'Données de paiement gérées par notre prestataire ; nous conservons des jetons sécurisés et non les numéros complets.',
                        'Données d’usage (pages consultées, recherches effectuées, informations sur l’appareil, diagnostics améliorant la fiabilité).',
                        'Intégrations et importations que vous connectez, notamment des listes ou avis issus de services partenaires.',
                    ],
                ],
                [
                    'title' => '2. Utilisation des données',
                    'paragraphs' => [
                        'Nous utilisons les données personnelles pour exploiter le produit, garantir sa sécurité, personnaliser certains éléments et communiquer avec vous.',
                    ],
                    'items' => [
                        'Exploiter l’application, fournir l’assistance et alimenter les fonctionnalités Livewire et API.',
                        'Traiter les paiements, détecter la fraude et appliquer les limites liées à votre formule.',
                        'Envoyer des e-mails transactionnels, des conseils de démarrage, des nouveautés produit et, lorsque c’est autorisé, des messages marketing.',
                        'Analyser des tendances agrégées pour planifier la capacité et améliorer la qualité des données.',
                    ],
                ],
                [
                    'title' => '3. Partage et divulgation',
                    'paragraphs' => [
                        'Nous ne vendons pas vos informations personnelles. Nous partageons seulement des données limitées avec des prestataires qui exploitent OMDb Stream dans le cadre de contrats protégeant votre confidentialité.',
                        'Nous pouvons divulguer des informations lorsque la loi l’exige ou pour protéger les droits, les biens ou la sécurité de nos utilisateurs et partenaires.',
                    ],
                ],
                [
                    'title' => '4. Vos choix et droits',
                    'paragraphs' => [
                        'Selon votre localisation, vous pouvez disposer de droits d’accès, de rectification, d’effacement ou de limitation du traitement de vos données personnelles. Nous répondons aux demandes vérifiées dans les délais requis.',
                    ],
                    'items' => [
                        'Mettre à jour vos informations de profil et vos préférences de notification depuis les paramètres du compte.',
                        'Demander une exportation ou la suppression en contactant le support ; nous confirmons chaque demande avant d’agir.',
                        'Vous désabonner des e-mails marketing via le lien dédié ou vos préférences de notification.',
                    ],
                ],
                [
                    'title' => '5. Conservation et sécurité',
                    'paragraphs' => [
                        'Nous conservons les données uniquement le temps nécessaire pour fournir le service, respecter nos obligations légales ou résoudre des litiges. Lorsqu’elles ne sont plus utiles, nous les supprimons ou les anonymisons.',
                        'Nous protégeons vos informations par chiffrement, contrôles d’accès et revues régulières afin d’éviter tout accès non autorisé.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Questions sur la confidentialité',
                'body' => 'Écrivez à :email pour poser vos questions sur les données ou soumettre une demande de confidentialité.',
            ],
            'effective_date' => 'En vigueur depuis le 1er mai 2024.',
        ],
        'support' => [
            'title' => 'Centre d’assistance',
            'meta_description' => 'Obtenez de l’aide sur la configuration, la facturation et les questions techniques d’OMDb Stream.',
            'heading' => 'Centre d’assistance',
            'lede' => 'Recevez rapidement de l’aide pour la configuration, la facturation et les problèmes techniques.',
            'intro' => 'Consultez ces guides courts pour trouver les réponses fréquentes ou contactez-nous si vous avez besoin d’un accompagnement humain.',
            'sections' => [
                [
                    'title' => '1. Bien démarrer',
                    'paragraphs' => [
                        'Créez votre compte, invitez vos collègues et connectez les sources de données depuis la page Paramètres.',
                        'Suivez la checklist de démarrage rapide pour importer votre bibliothèque et ajuster vos préférences de notification.',
                    ],
                    'cta' => [
                        'label' => 'Ouvrir le guide de démarrage rapide',
                        'href' => 'https://docs.omdbstream.test/get-started',
                    ],
                ],
                [
                    'title' => '2. Facturation et formules',
                    'paragraphs' => [
                        'Ouvrez la page de facturation pour changer de formule, mettre à jour vos moyens de paiement ou télécharger des factures à tout moment.',
                        'Prévenez-nous avant un lancement important afin que nous puissions recommander des limites adaptées et garantir des exports fluides.',
                    ],
                    'items' => [
                        'Passer d’une formule mensuelle à une formule annuelle sans contacter le support.',
                        'Ajouter des moyens de paiement de secours pour éviter les interruptions.',
                        'Télécharger reçus et factures à la demande pour vos dossiers.',
                    ],
                ],
                [
                    'title' => '3. Résoudre les problèmes courants',
                    'paragraphs' => [
                        'Consultez la page d’état du système et videz le cache depuis le tableau de bord si quelque chose semble anormal.',
                        'Lorsque vous nous écrivez, ajoutez les identifiants de requête, les horodatages et des captures d’écran pour aider l’équipe à reproduire le problème.',
                    ],
                ],
                [
                    'title' => '4. Rester en contact',
                    'paragraphs' => [
                        'Inscrivez-vous à notre newsletter produit ou à nos sessions mensuelles de questions-réponses pour suivre les nouveautés.',
                        'Partagez vos retours ou idées de fonctionnalités à tout moment ; vos suggestions contribuent à l’évolution d’OMDb Stream.',
                    ],
                    'cta' => [
                        'label' => 'Envoyer un retour',
                        'href' => 'mailto:product@omdbstream.test',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Besoin d’aide supplémentaire ?',
                'body' => 'Écrivez à :email avec l’identifiant de votre compte et un court résumé. Nous répondons sous un jour ouvré.',
            ],
            'default_cta' => 'Contacter le support',
        ],
    ],
    'impersonation' => [
        'banner_title' => 'Usurpation de :name',
        'banner_help' => 'Vous naviguez sur le site en tant que cet utilisateur. Lorsque vous avez terminé, retournez à votre compte administrateur.',
        'stop' => 'Arrêter l\'usurpation',
        'stopped' => 'Session d\'usurpation terminée. Vous êtes maintenant de retour sur votre compte administrateur.',
    ],
];
