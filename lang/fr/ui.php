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
            'meta_description' => 'Consultez les conditions qui encadrent votre compte, votre abonnement et l’usage des outils de catalogue OMDb Stream.',
            'heading' => 'Conditions d’utilisation',
            'lede' => 'Ces conditions précisent comment utiliser OMDb Stream de manière responsable et ce que vous pouvez attendre du service.',
            'intro' => 'OMDb Stream fournit des métadonnées sélectionnées, des outils et des fonctionnalités d’abonnement propulsés par OMDb et TMDb. En créant un compte, en achetant un abonnement ou en utilisant le site, vous acceptez les règles ci-dessous ainsi que notre Politique de confidentialité.',
            'sections' => [
                [
                    'title' => '1. Aperçu de l’accord',
                    'paragraphs' => [
                        'Ces conditions d’utilisation constituent un contrat contraignant entre vous et OMDb Stream. Elles s’appliquent à tous les visiteurs, titulaires de compte et organisations qui accèdent à la plateforme.',
                        'Nous pouvons mettre à jour les conditions pour refléter de nouvelles fonctionnalités ou des obligations légales. Lorsque les changements sont importants, nous afficherons un avis dans l’application ou contacterons le responsable principal de votre compte. La poursuite de l’utilisation d’OMDb Stream après la date d’effet vaut acceptation des conditions révisées.',
                    ],
                ],
                [
                    'title' => '2. Comptes et éligibilité',
                    'paragraphs' => [
                        'Vous devez avoir au moins 18 ans — ou l’âge légal dans votre juridiction — et être en mesure de conclure un contrat pour utiliser OMDb Stream. Lors de l’inscription vous vous engagez à fournir des informations exactes, à jour et complètes.',
                        'Gardez vos identifiants confidentiels et avertissez-nous immédiatement en cas d’accès non autorisé. Si vous invitez des collaborateurs ou accordez des droits à votre organisation, vous êtes responsable de leurs actions sur votre abonnement.',
                    ],
                ],
                [
                    'title' => '3. Abonnements et facturation',
                    'paragraphs' => [
                        'Les formules payantes se renouvellent automatiquement selon la fréquence choisie. Vous autorisez OMDb Stream et son prestataire de paiement à débiter le moyen enregistré pour les frais récurrents, les taxes applicables et toute option supplémentaire activée.',
                        'Vous pouvez annuler à tout moment depuis le portail de facturation. L’annulation met fin aux renouvellements futurs mais n’entraîne pas de remboursement au prorata pour la période en cours sauf obligation légale contraire. Certaines fonctionnalités expérimentales peuvent comporter des limites d’usage ou des conditions supplémentaires indiquées lors de la commande.',
                    ],
                ],
                [
                    'title' => '4. Utilisation acceptable',
                    'paragraphs' => [
                        'Utilisez OMDb Stream uniquement dans un cadre légal et selon les usages prévus par nos API et notre interface. N’essayez pas d’extraire massivement des données, de procéder à de l’ingénierie inverse, de surcharger la plateforme ou de contourner les limites, l’authentification ou les dispositifs de sécurité.',
                        'Nous pouvons suspendre ou résilier l’accès sans préavis si vous détournez le service, perturbez d’autres clients ou enfreignez des lois applicables, notamment en matière de propriété intellectuelle et de protection des données.',
                    ],
                ],
                [
                    'title' => '5. Contenu et données de tiers',
                    'paragraphs' => [
                        'Notre catalogue regroupe des données provenant d’OMDb, de TMDb et d’autres partenaires licenciés. Bien que nous visons l’exactitude, les informations sont fournies telles quelles et peuvent évoluer sans préavis. Vous devez vérifier les droits avant de redistribuer des métadonnées, visuels ou analyses.',
                        'Vous devez respecter les conditions d’OMDb, de TMDb et de tout autre concédant lors de l’exportation ou de l’intégration de leurs données. Si un fournisseur retire l’accès, nous pourrons supprimer du contenu ou des fonctionnalités en limitant l’impact sur votre abonnement.',
                    ],
                ],
                [
                    'title' => '6. Résiliation',
                    'paragraphs' => [
                        'Vous pouvez fermer votre compte à tout moment depuis les paramètres. Nous pouvons suspendre ou résilier les comptes qui enfreignent ces conditions, ne règlent pas les frais ou présentent des risques de sécurité ou de conformité.',
                        'À la résiliation, votre droit d’utiliser OMDb Stream cesse immédiatement. Les dispositions qui doivent perdurer par nature (indemnisation, limites de responsabilité, résolution des litiges) restent en vigueur.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Questions et contact',
                'body' => 'Pour toute question sur ces conditions, écrivez-nous à :email ou répondez à l’un des messages envoyés par l’équipe OMDb Stream.',
            ],
            'effective_date' => 'En vigueur depuis le 1er mai 2024.',
        ],
        'privacy' => [
            'title' => 'Politique de confidentialité',
            'meta_description' => 'Découvrez comment OMDb Stream collecte, utilise et protège vos informations personnelles.',
            'heading' => 'Politique de confidentialité',
            'lede' => 'Nous respectons votre vie privée et détaillons nos pratiques de traitement des données.',
            'intro' => 'La présente Politique de confidentialité décrit les données personnelles collectées par OMDb Stream, la manière dont nous les utilisons pour faire fonctionner la plateforme et les choix dont vous disposez pour contrôler vos informations. Nous traitons les données conformément aux réglementations applicables et aux meilleures pratiques du secteur.',
            'sections' => [
                [
                    'title' => '1. Données collectées',
                    'paragraphs' => [
                        'Nous collectons les informations que vous fournissez directement, les données générées lorsque vous interagissez avec le site ainsi que certains éléments provenant de tiers de confiance. Les données exactes varient selon votre usage d’OMDb Stream.',
                    ],
                    'items' => [
                        'Informations de compte telles que nom, adresse e-mail, organisation et préférences d’abonnement.',
                        'Données de paiement traitées en toute sécurité par notre prestataire de facturation ; nous conservons des jetons et non les numéros complets de carte.',
                        'Données d’usage comprenant pages consultées, recherches effectuées, identifiants d’appareil et diagnostics aidant à améliorer la fiabilité.',
                        'Intégrations et importations que vous autorisez, comme des listes de suivi ou des critiques synchronisées depuis des services partenaires.',
                    ],
                ],
                [
                    'title' => '2. Utilisation des données',
                    'paragraphs' => [
                        'Nous traitons les données personnelles pour fournir le service, personnaliser les recommandations, garantir la sécurité et communiquer avec vous à propos de votre compte.',
                    ],
                    'items' => [
                        'Exploiter l’application, assurer l’assistance client et alimenter les fonctionnalités Livewire et API.',
                        'Traiter les paiements, détecter la fraude et appliquer les limites d’usage associées à votre formule.',
                        'Envoyer des messages transactionnels, des conseils d’onboarding, des mises à jour produit et, lorsque la loi le permet, des communications marketing.',
                        'Analyser des tendances agrégées pour planifier la capacité, améliorer la pertinence de la recherche et renforcer la qualité des données.',
                    ],
                ],
                [
                    'title' => '3. Partage et divulgation',
                    'paragraphs' => [
                        'Nous ne vendons pas vos informations personnelles. Nous partageons des données limitées avec des prestataires qui nous aident à exploiter OMDb Stream et uniquement dans le cadre de contrats qui les obligent à protéger vos informations.',
                        'Nous pouvons divulguer des données lorsque la loi l’exige, pour répondre à des demandes légales recevables ou pour protéger les droits, la propriété ou la sécurité de nos utilisateurs et partenaires.',
                    ],
                ],
                [
                    'title' => '4. Vos choix et droits',
                    'paragraphs' => [
                        'Selon votre localisation, vous pouvez disposer de droits d’accès, de rectification, d’effacement ou de limitation du traitement de vos données personnelles. Nous répondons aux demandes vérifiées dans les délais prévus par la réglementation applicable.',
                    ],
                    'items' => [
                        'Mettre à jour les informations de profil et les préférences de communication depuis les paramètres du compte.',
                        'Exporter vos données ou demander leur suppression en contactant le support ; nous vérifierons votre identité avant toute action.',
                        'Vous désabonner des e-mails marketing via le lien prévu ou en ajustant vos préférences de notification.',
                    ],
                ],
                [
                    'title' => '5. Conservation et sécurité',
                    'paragraphs' => [
                        'Nous conservons les données personnelles uniquement le temps nécessaire pour fournir le service, respecter nos obligations légales ou résoudre des litiges. Lorsqu’elles ne sont plus nécessaires, nous les supprimons ou les anonymisons.',
                        'Nous mettons en œuvre des mesures techniques, administratives et physiques — chiffrement en transit, contrôles d’accès, audits réguliers — afin de protéger vos informations contre les accès non autorisés.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Questions sur la confidentialité',
                'body' => 'Contactez notre équipe confidentialité à :email pour toute demande liée aux données ou pour comprendre comment nous les traitons.',
            ],
            'effective_date' => 'En vigueur depuis le 1er mai 2024.',
        ],
        'support' => [
            'title' => 'Centre d’assistance',
            'meta_description' => 'Accédez aux ressources d’aide, aux conseils de facturation et aux options de contact du support OMDb Stream.',
            'heading' => 'Centre d’assistance',
            'lede' => 'Nous vous aidons à démarrer rapidement et à résoudre les incidents sans friction.',
            'intro' => 'Ces guides vous permettent de tirer le meilleur parti d’OMDb Stream. Notre équipe support collabore avec les spécialistes produit et ingénierie pour fournir des réponses fiables et actionnables.',
            'sections' => [
                [
                    'title' => '1. Bien démarrer',
                    'paragraphs' => [
                        'Commencez par connecter vos sources de métadonnées préférées et inviter vos collaborateurs depuis le tableau de bord. La checklist d’onboarding vous guide pour activer les composants Livewire, configurer la synchronisation des listes et ajuster les notifications.',
                        'Si vous migrez depuis un autre outil, exportez votre catalogue au format CSV ou JSON et importez-le via le tableau de bord du parseur. Notre équipe peut examiner votre plan de migration afin de limiter les interruptions.',
                    ],
                    'cta' => [
                        'label' => 'Consulter le guide d’onboarding',
                        'href' => 'https://docs.omdbstream.test/get-started',
                    ],
                ],
                [
                    'title' => '2. Facturation et offres',
                    'paragraphs' => [
                        'Gérez vos moyens de paiement, téléchargez vos factures et changez d’offre depuis le portail de facturation. Les modifications sont immédiates et les ajustements au prorata apparaissent sur la facture suivante.',
                        'Contactez-nous avant d’étendre des équipes importantes ou d’atteindre les plafonds API afin de recommander l’offre la plus adaptée et d’anticiper vos besoins d’export de données.',
                    ],
                    'items' => [
                        'Mettre à jour votre moyen de paiement principal et ajouter des cartes de secours pour les comptes partagés.',
                        'Consulter les dates de renouvellement à venir et activer des alertes de facturation pour votre équipe finance.',
                        'Demander des factures conformes (TVA, mentions légales) directement depuis le portail.',
                    ],
                ],
                [
                    'title' => '3. Dépannage technique',
                    'paragraphs' => [
                        'La plupart des incidents se résolvent en vidant les données mises en cache via les outils du tableau de bord, en vérifiant la page d’état du système ou en examinant les livraisons récentes de webhooks. Notre page d’état publie des mises à jour en temps réel sur les pipelines d’ingestion et la recherche.',
                        'Si vous devez escalader un ticket, joignez les identifiants de requête, les horodatages et des captures d’écran pertinentes. Ces informations aident l’ingénierie à reproduire rapidement le problème.',
                    ],
                ],
                [
                    'title' => '4. Rester informé',
                    'paragraphs' => [
                        'Participez à nos webinaires mensuels et à notre newsletter pour suivre les nouveautés produit et les nouveaux partenariats data.',
                        'Nous apprécions vos retours sur les priorités de feuille de route et les améliorations de workflow : vos suggestions contribuent à façonner OMDb Stream.',
                    ],
                    'cta' => [
                        'label' => 'Proposer une fonctionnalité',
                        'href' => 'mailto:product@omdbstream.test',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Besoin d’aide supplémentaire ?',
                'body' => 'Envoyez un e-mail à :email avec votre identifiant de compte et un bref résumé. Un spécialiste support vous répondra sous un jour ouvré.',
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
