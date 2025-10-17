<?php

return [
    'nav' => [
        'brand' => [
            'primary' => 'OMDb',
            'secondary' => 'Stream',
        ],
        'links' => [
            'home' => 'Home',
            'browse' => 'Browse',
            'pricing' => 'Pricing',
            'account' => 'Account',
            'admin' => 'Admin',
        ],
        'auth' => [
            'login' => 'Sign in',
            'register' => 'Join now',
            'logout' => 'Logout',
        ],
        'theme' => [
            'light' => 'Light mode',
            'dark' => 'Dark mode',
        ],
        'theme_toggle' => 'Toggle theme',
        'menu' => [
            'label' => 'Navigation',
            'open' => 'Open navigation',
            'close' => 'Close navigation',
        ],
        'search' => [
            'placeholder' => 'Search movies, shows, people...',
            'button' => 'Search',
            'shortcut' => 'Press :key to search',
            'clear' => 'Clear search',
            'no_results' => 'No results found',
            'searching' => 'Searching...',
            'mobile_open' => 'Open search',
            'mobile_close' => 'Close search',
        ],
        'user_menu' => [
            'dropdown_label' => 'User menu',
            'profile' => 'Profile',
            'account' => 'Account',
            'settings' => 'Settings',
            'logout' => 'Sign out',
            'view_profile' => 'View profile',
        ],
        'footer' => [
            'terms' => 'Terms',
            'agreements' => 'Agreements',
            'privacy' => 'Privacy',
            'support' => 'Support',
            'copyright' => '© :year OMDb Stream. All rights reserved.',
        ],
    ],
    'impersonation' => [
        'banner_title' => 'Impersonating :name',
        'banner_help' => 'You are browsing the site as this user. When you are done, return to your admin account.',
        'stop' => 'Stop impersonating',
        'stopped' => 'Impersonation session ended. You are now back on your admin account.',
    ],
    'admin' => [
        'panel' => [
            'title' => 'Admin control panel',
            'subtitle' => 'Manage catalog resources, editorial metadata, and taxonomies that power the public portal.',
            'actions' => [
                'create' => 'Create',
                'update' => 'Save changes',
                'clear' => 'Clear form',
                'reset' => 'Reset form',
                'edit' => 'Edit',
                'delete' => 'Delete',
            ],
            'fields' => [
                'title' => 'Title',
                'slug' => 'Slug',
                'status' => 'Status',
                'release_date' => 'Release date',
                'vote_average' => 'Vote average',
                'adult' => 'Marked as adult content',
                'search' => 'Search',
                'name' => 'Name',
                'first_air_date' => 'First air date',
                'department' => 'Department',
                'birthday' => 'Birthday',
                'gender' => 'Gender',
                'popularity' => 'Popularity',
                'tmdb_id' => 'TMDb ID',
                'code' => 'Code',
                'native_name' => 'Native name',
                'active' => 'Active',
            ],
            'placeholders' => [
                'movie_title' => 'e.g. The Matrix',
                'slug' => 'Leave blank to auto-generate',
                'status' => 'Planned, Released, In Production…',
                'search_movies' => 'Search by title or slug…',
                'show_name' => 'e.g. True Detective',
                'search_shows' => 'Search by show name…',
                'person_name' => 'e.g. Keanu Reeves',
                'department' => 'Acting, Directing, Writing…',
                'search_people' => 'Search by person name…',
                'genre_name' => 'e.g. Science Fiction',
                'tmdb_id' => 'Numeric TMDb identifier',
                'search_genres' => 'Search by genre name…',
                'language_name' => 'e.g. English',
                'native_name' => 'e.g. Español',
                'search_languages' => 'Search by language name or code…',
                'country_name' => 'e.g. United States',
                'search_countries' => 'Search by country name or code…',
                'search_tags' => 'Search by tag name or slug…',
            ],
            'table' => [
                'movie' => 'Movie',
                'show' => 'Series',
                'person' => 'Person',
                'genre' => 'Genre',
                'language' => 'Language',
                'country' => 'Country',
                'actions' => 'Actions',
                'empty' => 'No records match the current filters.',
            ],
            'tags' => [
                'fields' => [
                    'name_en' => 'Name (English)',
                    'name_es' => 'Name (Spanish)',
                    'name_fr' => 'Name (French)',
                    'type' => 'Tag type',
                ],
                'placeholders' => [
                    'name_en' => 'e.g. Award-winning',
                    'name_es' => 'Traducción opcional…',
                    'name_fr' => 'Traduction facultative…',
                    'search' => 'Search by tag name or slug…',
                ],
                'types' => [
                    'system' => 'System',
                    'community' => 'Community',
                ],
                'merge' => [
                    'title' => 'Merge duplicate tags',
                    'subtitle' => 'Consolidate overlapping vocabulary and keep curated rails clean.',
                    'source' => 'Source tag ID',
                    'target' => 'Target tag ID',
                    'placeholders' => [
                        'source' => 'Tag ID to merge from',
                        'target' => 'Tag ID to merge into',
                    ],
                    'action' => 'Merge tags',
                ],
                'table' => [
                    'tag' => 'Tag',
                    'type' => 'Type',
                    'usage' => 'Usage',
                ],
            ],
            'labels' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
            ],
            'sections' => [
                'movies' => [
                    'title' => 'Movie editor',
                    'subtitle' => 'Curate film records, manage release metadata, and keep the library consistent.',
                    'nav' => 'Movies',
                    'heading' => 'Manage movies',
                ],
                'tv_shows' => [
                    'title' => 'Series editor',
                    'subtitle' => 'Track episodic releases, air dates, and availability.',
                    'nav' => 'TV shows',
                    'heading' => 'Manage TV shows',
                ],
                'people' => [
                    'title' => 'People directory',
                    'subtitle' => 'Maintain cast and crew credits for accurate attribution.',
                    'nav' => 'People',
                    'heading' => 'Manage people',
                ],
                'genres' => [
                    'title' => 'Genre taxonomy',
                    'subtitle' => 'Control the genre vocabulary used throughout discovery tools.',
                    'nav' => 'Genres',
                    'heading' => 'Manage genres',
                ],
                'languages' => [
                    'title' => 'Language catalog',
                    'subtitle' => 'Configure available audio and subtitle languages for the catalog.',
                    'nav' => 'Languages',
                    'heading' => 'Manage languages',
                ],
                'countries' => [
                    'title' => 'Country registry',
                    'subtitle' => 'Keep production origin data aligned with ISO codes.',
                    'nav' => 'Countries',
                    'heading' => 'Manage countries',
                ],
                'tags' => [
                    'title' => 'Tag curation',
                    'subtitle' => 'Moderate discovery tags, merge duplicates, and steer merchandising rails.',
                    'nav' => 'Tags',
                    'heading' => 'Manage tags',
                ],
            ],
            'people' => [
                'gender_unknown' => 'Not specified',
                'gender_female' => 'Female',
                'gender_male' => 'Male',
                'gender_non_binary' => 'Non-binary',
            ],
            'relationships' => [
                'title' => 'Taxonomies, tags & locales',
                'subtitle' => 'Attach genres, curated tags, languages, and origin countries to power downstream experiences.',
                'suggestions' => 'Suggestions',
                'empty' => 'No suggestions match the current search.',
                'genres' => [
                    'label' => 'Genres',
                    'help' => 'Use curated genre tags to strengthen discovery filters and storefront rails.',
                    'none' => 'No genres selected yet.',
                    'remove' => 'Remove :name from the selected genres',
                ],
                'tags' => [
                    'label' => 'Tags',
                    'help' => 'Pin editorial and system tags to surface titles in merchandising slots.',
                    'none' => 'No tags selected yet.',
                    'remove' => 'Remove :name from the selected tags',
                ],
                'languages' => [
                    'label' => 'Languages',
                    'help' => 'Track available dubs and subtitle locales for accurate availability tooling.',
                    'none' => 'No languages selected yet.',
                    'remove' => 'Remove :name from the selected languages',
                ],
                'countries' => [
                    'label' => 'Countries',
                    'help' => 'Map production origin for compliance and regional programming insights.',
                    'none' => 'No countries selected yet.',
                    'remove' => 'Remove :name from the selected countries',
                ],
            ],
        ],
    ],
    'dashboard' => [
        'title' => 'Dashboard',
        'layout' => [
            'sidebar_heading' => 'Navigation',
            'default_header' => 'Dashboard overview',
        ],
        'nav' => [
            'overview' => 'Overview',
            'manage_subscription' => 'Manage Subscription',
        ],
        'welcome_heading' => 'Welcome back!',
        'welcome_body' => 'Review your plan details, manage billing, and make changes to your subscription in real time.',
        'insights_card' => [
            'title' => 'Plan insights',
            'subscription_status' => 'Subscription status',
            'trial_days' => 'Trial days',
            'next_invoice' => 'Next invoice',
        ],
        'cards' => [
            'manage_subscription' => 'Manage subscription',
            'watchlist' => 'Watchlist',
        ],
        'trial' => [
            'active_title' => 'Your free trial is active.',
            'active_body' => 'Enjoy full access until :date. We\'ll send reminders before billing begins.',
            'cta' => 'Start :days-day trial',
            'intro_title' => 'Start your :days-day free trial.',
            'intro_body' => 'Unlock every movie detail, premium filters, and curated recommendations while you evaluate the platform.',
            'missing_price' => 'Add your Stripe price identifier to :key to enable subscriptions.',
            'cancel_notice' => 'Cancel any time before the trial ends to avoid charges.',
        ],
        'subscriber' => [
            'thanks_title' => 'Thanks for being a subscriber!',
            'thanks_body' => 'Enjoy unlimited access to detailed data, watchlists, and personalized insights.',
        ],
        'grace' => [
            'title' => 'Your subscription is scheduled to end.',
            'body' => 'Access remains available until :date. Resume the plan in Stripe if you change your mind.',
        ],
        'inactive' => [
            'title' => 'Subscription inactive.',
            'body' => 'Re-subscribe anytime from the billing portal to regain premium access.',
        ],
    ],
    'filters' => [
        'heading' => 'Advanced filters',
        'description' => 'Tune your discovery feed with genres, languages, and release years.',
        'type_label' => 'Type',
        'types' => [
            'movies' => 'Movies',
            'shows' => 'TV Shows',
        ],
        'genre_label' => 'Genre',
        'year_label' => 'Year',
        'language_label' => 'Language',
        'sort_label' => 'Sort by',
        'sort_options' => [
            'popularity_desc' => 'Popularity',
            'vote_average_desc' => 'Rating',
            'release_date_desc' => 'Newest',
            'release_date_asc' => 'Oldest',
        ],
        'results_title' => 'Results preview',
        'results_summary' => 'Filtering :genre :type released in :year.',
        'apply' => 'Apply',
    ],
    'people' => [
        'page_title' => 'Person detail',
        'no_biography' => 'No biography available yet.',
        'profile_alt' => ':name profile portrait',
        'poster_alt' => 'Key art poster for :name',
        'vitals_heading' => 'Vitals',
        'born_label' => 'Born',
        'place_label' => 'Place',
        'known_for_label' => 'Known for',
        'popularity_label' => 'Popularity',
        'biography_heading' => 'Biography',
        'movies_heading' => 'Movies',
        'tv_heading' => 'TV',
        'credits_heading' => ':type credits',
        'credit_types' => [
            'cast' => 'Cast',
            'crew' => 'Crew',
        ],
    ],
    'pages' => [
        'terms' => [
            'title' => 'Terms of Service',
            'meta_description' => 'Read the rules that apply to your OMDb Stream account.',
            'heading' => 'Terms of Service',
            'lede' => 'These terms explain how the service works and what you can expect from us.',
            'intro' => 'By using OMDb Stream you agree to follow these terms and our Privacy Policy. Please review them so you know your responsibilities and ours.',
            'sections' => [
                [
                    'title' => '1. Agreement overview',
                    'paragraphs' => [
                        'These Terms of Service create a contract between you and OMDb Stream for all use of the site.',
                        'We may update them when laws change or we add features. We will notify you about major updates, and using the service after that notice means you accept the new terms.',
                    ],
                ],
                [
                    'title' => '2. Accounts and eligibility',
                    'paragraphs' => [
                        'You must be at least 18 years old and able to enter a contract. Provide accurate account details and keep them up to date.',
                        'Keep your login credentials safe. You are responsible for the actions of teammates or partners you invite to your subscription.',
                    ],
                ],
                [
                    'title' => '3. Subscriptions and billing',
                    'paragraphs' => [
                        'Paid plans renew automatically at the schedule you choose. Charges may include subscription fees, taxes, and add-ons you enable.',
                        'You can cancel at any time from the billing page. Refunds are handled according to local laws or any commitments we make during checkout.',
                    ],
                ],
                [
                    'title' => '4. Acceptable use',
                    'paragraphs' => [
                        'Use OMDb Stream only for lawful purposes and within the limits of our APIs and interface. Do not scrape, overload, or bypass security or rate limits.',
                        'We may suspend or end access if you misuse the service, harm other customers, or violate the law.',
                    ],
                ],
                [
                    'title' => '5. Ending service',
                    'paragraphs' => [
                        'You can close your account from the settings page. We may suspend or end access if payments fail or these terms are broken.',
                        'Sections that cover payments, usage limits, and dispute terms remain in effect after your account closes.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Questions & contact',
                'body' => 'Email :email if you have questions about these terms or need help understanding them.',
            ],
            'effective_date' => 'Effective May 1, 2024.',
        ],
        'privacy' => [
            'title' => 'Privacy Policy',
            'meta_description' => 'See how OMDb Stream collects and uses your information.',
            'heading' => 'Privacy Policy',
            'lede' => 'We explain what we collect, why we keep it, and how you can control it.',
            'intro' => 'This Privacy Policy describes the personal data OMDb Stream processes to run the service and the choices you have. We follow applicable privacy laws and industry best practices.',
            'sections' => [
                [
                    'title' => '1. Information we collect',
                    'paragraphs' => [
                        'We collect details you share with us, data created when you use the site, and information from trusted partners. What we collect depends on how you use OMDb Stream.',
                    ],
                    'items' => [
                        'Account details like your name, email address, organization, and subscription settings.',
                        'Payment data handled by our billing provider—we store secure tokens, not full card numbers.',
                        'Usage data such as pages viewed, searches run, device information, and diagnostics that improve reliability.',
                        'Integrations and imports you connect, including watchlists or reviews from partner services.',
                    ],
                ],
                [
                    'title' => '2. How we use information',
                    'paragraphs' => [
                        'We use personal data to run the product, keep it secure, personalize parts of the experience, and stay in touch.',
                    ],
                    'items' => [
                        'Operating the application, providing support, and powering Livewire and API features.',
                        'Processing payments, detecting fraud, and enforcing plan limits.',
                        'Sending transactional emails, onboarding tips, product updates, and marketing messages when allowed.',
                        'Analyzing aggregated usage trends to plan capacity and improve data quality.',
                    ],
                ],
                [
                    'title' => '3. Sharing and disclosure',
                    'paragraphs' => [
                        'We do not sell your personal information. We share limited data with vendors who help us run OMDb Stream under contracts that protect your privacy.',
                        'We may disclose information when required by law or to protect the rights, property, or safety of our users and partners.',
                    ],
                ],
                [
                    'title' => '4. Your choices and rights',
                    'paragraphs' => [
                        'Depending on where you live, you may have rights to access, change, delete, or limit how we use your personal data. We honor verified requests within required timelines.',
                    ],
                    'items' => [
                        'Update your profile information and notification preferences from account settings.',
                        'Request exports or deletion by contacting support—we confirm requests before taking action.',
                        'Opt out of marketing emails through the unsubscribe link or your notification settings.',
                    ],
                ],
                [
                    'title' => '5. Data retention and security',
                    'paragraphs' => [
                        'We keep data only as long as needed to run the service, meet legal duties, or resolve disputes. When it is no longer required, we delete or anonymize it.',
                        'We protect your information with encryption, access controls, and regular reviews to prevent unauthorized access.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Privacy questions',
                'body' => 'Email :email to ask about your data or submit a privacy request.',
            ],
            'effective_date' => 'Effective May 1, 2024.',
        ],
        'support' => [
            'title' => 'Support Center',
            'meta_description' => 'Get help with setup, billing, and technical questions for OMDb Stream.',
            'heading' => 'Support Center',
            'lede' => 'Get quick help with setup, billing, and technical questions.',
            'intro' => 'Browse these short guides for common answers or contact us when you need a person to step in.',
            'sections' => [
                [
                    'title' => '1. Getting started',
                    'paragraphs' => [
                        'Create your account, invite teammates, and connect data sources from the Settings page.',
                        'Follow the quick-start checklist to import your library and set notification preferences that match your workflow.',
                    ],
                    'cta' => [
                        'label' => 'Open the quick-start guide',
                        'href' => 'https://docs.omdbstream.test/get-started',
                    ],
                ],
                [
                    'title' => '2. Billing and plans',
                    'paragraphs' => [
                        'Open the billing page to change plans, update payment methods, or download invoices whenever you need them.',
                        'Let us know before a big launch so we can recommend limits that fit your team and keep data exports smooth.',
                    ],
                    'items' => [
                        'Switch between monthly and yearly plans without contacting support.',
                        'Add backup payment methods to prevent service interruptions.',
                        'Download receipts and invoices on demand for your records.',
                    ],
                ],
                [
                    'title' => '3. Fixing common issues',
                    'paragraphs' => [
                        'Check the system status page and clear cached data from the dashboard if something looks off.',
                        'When you contact us, include request IDs, timestamps, and screenshots so engineering can reproduce the issue faster.',
                    ],
                ],
                [
                    'title' => '4. Stay in touch',
                    'paragraphs' => [
                        'Join our product newsletter or monthly Q&A sessions to hear about new features and data partners.',
                        'Share feedback or feature ideas any time—your suggestions help shape OMDb Stream.',
                    ],
                    'cta' => [
                        'label' => 'Send feedback',
                        'href' => 'mailto:product@omdbstream.test',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Need extra help?',
                'body' => 'Email :email with your account ID and a short summary. We reply within one business day.',
            ],
            'default_cta' => 'Contact support',
        ],
    ],
];
