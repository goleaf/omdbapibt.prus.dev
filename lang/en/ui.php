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
            'components' => 'UI components',
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
        'footer' => [
            'terms' => 'Terms',
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
            ],
            'people' => [
                'gender_unknown' => 'Not specified',
                'gender_female' => 'Female',
                'gender_male' => 'Male',
                'gender_non_binary' => 'Non-binary',
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
            'meta_description' => 'Review the terms that govern your OMDb Stream account, subscriptions, and use of our catalog tools.',
            'heading' => 'Terms of Service',
            'lede' => 'These terms explain how to use OMDb Stream responsibly and what you can expect from our service.',
            'intro' => 'OMDb Stream delivers curated metadata, tooling, and subscription features powered by OMDb and TMDb. By creating an account, purchasing a subscription, or using the site, you agree to the rules outlined below in addition to our Privacy Policy.',
            'sections' => [
                [
                    'title' => '1. Agreement overview',
                    'paragraphs' => [
                        'These Terms of Service form a binding agreement between you and OMDb Stream. They apply to all visitors, account holders, and organizations that access the platform.',
                        'We may update the terms to reflect new features or legal requirements. When changes are material, we will post an in-app notice or email the primary contact on your account. Your continued use of OMDb Stream after the effective date means you accept the updated terms.',
                    ],
                ],
                [
                    'title' => '2. Accounts and eligibility',
                    'paragraphs' => [
                        'You must be at least 18 years old—or the age of majority in your jurisdiction—and capable of entering into contracts to use OMDb Stream. When you sign up you agree to provide accurate, current, and complete registration details.',
                        'Keep your login credentials secure and notify us immediately if you suspect unauthorized access. If you invite teammates or grant organization access, you are responsible for their activity on your subscription.',
                    ],
                ],
                [
                    'title' => '3. Subscriptions and billing',
                    'paragraphs' => [
                        'Paid plans renew automatically at the interval you select. You authorize us and our payment processor to charge the payment method on file for recurring fees, applicable taxes, and any add-ons you activate.',
                        'You can cancel from the billing portal at any time. Cancellation stops future renewals, but does not issue pro-rated refunds for the current term unless required by law. Some experimental features may have separate usage limits or service-level terms described during checkout.',
                    ],
                ],
                [
                    'title' => '4. Acceptable use',
                    'paragraphs' => [
                        'Use OMDb Stream only for lawful purposes and within the usage patterns supported by our APIs and interface. Do not attempt to scrape, reverse engineer, overload, or circumvent rate limits, authentication, or security controls.',
                        'We may suspend or terminate access without notice if you misuse the service, interfere with other customers, or violate applicable laws, including intellectual property and data protection regulations.',
                    ],
                ],
                [
                    'title' => '5. Content and third-party data',
                    'paragraphs' => [
                        'Our catalog combines data from OMDb, TMDb, and other licensed partners. While we strive for accuracy, the information is provided as-is and may change without notice. You are responsible for verifying rights before redistributing any metadata, artwork, or analyses downstream.',
                        'You must comply with the terms of OMDb, TMDb, and any other applicable licensors when exporting or embedding their data. If a provider revokes access, we may remove content or features with minimal disruption to your subscription.',
                    ],
                ],
                [
                    'title' => '6. Termination',
                    'paragraphs' => [
                        'You may close your account at any time from the account settings page. We may terminate or suspend accounts that violate these terms, fail to pay fees, or present security or compliance risks.',
                        'Upon termination, your right to use OMDb Stream ends immediately. Provisions that by their nature should survive (such as indemnities, limitations of liability, and dispute terms) will remain in effect.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Questions & contact',
                'body' => 'If you have questions about these terms, contact us at :email or reply to any message you receive from the OMDb Stream team.',
            ],
            'effective_date' => 'Effective May 1, 2024.',
        ],
        'privacy' => [
            'title' => 'Privacy Policy',
            'meta_description' => 'Learn how OMDb Stream collects, uses, and protects your personal information.',
            'heading' => 'Privacy Policy',
            'lede' => 'We respect your privacy and explain our data practices with clarity.',
            'intro' => 'This Privacy Policy describes the personal data OMDb Stream collects, how we use it to power the platform, and the choices you have to control your information. We process data according to applicable privacy regulations and industry best practices.',
            'sections' => [
                [
                    'title' => '1. Information we collect',
                    'paragraphs' => [
                        'We collect information that you provide directly, data created when you interact with the site, and limited details from trusted third parties. The exact data depends on how you use OMDb Stream.',
                    ],
                    'items' => [
                        'Account details such as your name, email address, organization, and subscription preferences.',
                        'Payment information processed securely by our billing provider; we store tokens, not full card numbers.',
                        'Usage data including pages visited, queries executed, device identifiers, and diagnostics that help improve reliability.',
                        'Integrations and imports you authorize, such as watchlists or reviews synced from partner services.',
                    ],
                ],
                [
                    'title' => '2. How we use information',
                    'paragraphs' => [
                        'We process personal data to deliver the service, personalize recommendations, ensure security, and communicate with you about your account.',
                    ],
                    'items' => [
                        'Operating the application, providing customer support, and powering Livewire and API features.',
                        'Processing payments, detecting fraud, and enforcing usage limits tied to your subscription tier.',
                        'Sending transactional messages, onboarding tips, product updates, and marketing communications where permitted.',
                        'Analyzing aggregated usage trends to plan capacity, improve search relevance, and enhance data quality.',
                    ],
                ],
                [
                    'title' => '3. Sharing and disclosure',
                    'paragraphs' => [
                        'We do not sell your personal information. We share limited data with service providers who help us operate OMDb Stream, and only under contracts that require them to protect your information.',
                        'We may disclose information when required by law, to respond to valid legal requests, or to protect the rights, property, or safety of our users and partners.',
                    ],
                ],
                [
                    'title' => '4. Your choices and rights',
                    'paragraphs' => [
                        'Depending on your location, you may have rights to access, correct, delete, or restrict the processing of your personal data. We honor verified requests within the timelines set by applicable law.',
                    ],
                    'items' => [
                        'Update profile information and communication preferences from your account settings.',
                        'Export data or request deletion by contacting support; we will authenticate the request before taking action.',
                        'Opt out of marketing emails by using the unsubscribe link or adjusting your notification preferences.',
                    ],
                ],
                [
                    'title' => '5. Data retention and security',
                    'paragraphs' => [
                        'We retain personal data only as long as needed to provide the service, comply with legal obligations, or resolve disputes. When data is no longer required, we delete it or anonymize it.',
                        'We implement technical, administrative, and physical safeguards—including encryption in transit, access controls, and regular audits—to protect your information against unauthorized access.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Privacy questions',
                'body' => 'Reach our privacy team at :email to submit a data request or ask how we handle your information.',
            ],
            'effective_date' => 'Effective May 1, 2024.',
        ],
        'support' => [
            'title' => 'Support Center',
            'meta_description' => 'Find help resources, billing guidance, and contact options for OMDb Stream support.',
            'heading' => 'Support Center',
            'lede' => 'We are here to help you launch quickly and solve issues without friction.',
            'intro' => 'Use these guides to get the most from OMDb Stream. Our support team partners with engineering and product specialists so you always receive accurate, actionable answers.',
            'sections' => [
                [
                    'title' => '1. Getting started',
                    'paragraphs' => [
                        'Start by connecting your preferred metadata sources and inviting collaborators from the account dashboard. The onboarding checklist walks you through enabling Livewire components, configuring watchlist sync, and setting notification preferences.',
                        'If you are migrating from another tool, export your existing catalog as CSV or JSON and import it through the parser dashboard. Our team can review your migration plan to minimize downtime.',
                    ],
                    'cta' => [
                        'label' => 'View the onboarding guide',
                        'href' => 'https://docs.omdbstream.test/get-started',
                    ],
                ],
                [
                    'title' => '2. Billing and plans',
                    'paragraphs' => [
                        'Manage payment methods, download invoices, and switch plans from the billing portal. Changes take effect immediately and pro-rated adjustments appear on your next invoice.',
                        'Contact us before scaling large teams or hitting API usage caps so we can recommend the best tier and align on data export requirements.',
                    ],
                    'items' => [
                        'Update your default payment method and set backup cards for shared accounts.',
                        'Review upcoming renewal dates and enable billing alerts for your finance team.',
                        'Request VAT or tax-compliant invoices directly from the portal.',
                    ],
                ],
                [
                    'title' => '3. Technical troubleshooting',
                    'paragraphs' => [
                        'Most issues can be resolved by clearing cached data via the dashboard tools, checking the system status page, or reviewing recent webhook deliveries. Our status page publishes real-time updates for ingestion pipelines and search.',
                        'When you need to escalate a ticket, include relevant request IDs, timestamps, and screenshots. This context helps engineering reproduce the problem quickly.',
                    ],
                ],
                [
                    'title' => '4. Staying connected',
                    'paragraphs' => [
                        'Join our monthly product webinars and changelog newsletter to stay informed about new features and data partnerships.',
                        'We welcome feedback on roadmap priorities and workflow improvements—your suggestions help shape OMDb Stream.',
                    ],
                    'cta' => [
                        'label' => 'Suggest a feature',
                        'href' => 'mailto:product@omdbstream.test',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Need extra help?',
                'body' => 'Email :email with your account ID and a short summary. A support specialist will follow up within one business day.',
            ],
            'default_cta' => 'Contact support',
        ],
    ],
];
