<?php

return [
    'nav' => [
        'brand' => [
            'primary' => 'OMDb',
            'secondary' => 'Stream',
        ],
        'links' => [
            'home' => 'Главная',
            'browse' => 'Каталог',
            'pricing' => 'Тарифы',
            'account' => 'Аккаунт',
            'admin' => 'Админ',
        ],
        'auth' => [
            'login' => 'Войти',
            'register' => 'Регистрация',
            'logout' => 'Выйти',
        ],
        'theme' => [
            'light' => 'Светлая тема',
            'dark' => 'Тёмная тема',
        ],
        'theme_toggle' => 'Переключить тему',
        'menu' => [
            'label' => 'Навигация',
            'open' => 'Открыть навигацию',
            'close' => 'Закрыть навигацию',
        ],
        'search' => [
            'placeholder' => 'Ищите фильмы, сериалы и людей...',
            'button' => 'Поиск',
            'shortcut' => 'Нажмите :key, чтобы искать',
            'clear' => 'Очистить поиск',
            'no_results' => 'Ничего не найдено',
            'searching' => 'Поиск...',
            'mobile_open' => 'Открыть поиск',
            'mobile_close' => 'Закрыть поиск',
        ],
        'user_menu' => [
            'dropdown_label' => 'Меню пользователя',
            'profile' => 'Профиль',
            'account' => 'Аккаунт',
            'settings' => 'Настройки',
            'logout' => 'Выйти',
            'view_profile' => 'Просмотреть профиль',
        ],
        'footer' => [
            'terms' => 'Условия',
            'agreements' => 'Соглашения',
            'privacy' => 'Конфиденциальность',
            'support' => 'Поддержка',
            'copyright' => '© :year OMDb Stream. Все права защищены.',
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
            'title' => 'Условия обслуживания',
            'meta_description' => 'Ознакомьтесь с правилами, которые применяются к вашему аккаунту OMDb Stream.',
            'heading' => 'Условия обслуживания',
            'lede' => 'Эти условия объясняют, как работает сервис и чего вы можете ожидать.',
            'intro' => 'Используя OMDb Stream, вы соглашаетесь с этими условиями и нашей Политикой конфиденциальности. Ознакомьтесь с ними, чтобы понимать свои обязанности и наши.',
            'sections' => [
                [
                    'title' => '1. Обзор соглашения',
                    'paragraphs' => [
                        'Эти условия обслуживания представляют собой договор между вами и OMDb Stream для любого использования сайта.',
                        'Мы можем обновлять условия при изменении законодательства или появлении новых функций. Мы уведомим о важных изменениях, и продолжение использования после уведомления означает принятие новых условий.',
                    ],
                ],
                [
                    'title' => '2. Аккаунты и требования',
                    'paragraphs' => [
                        'Вам должно быть не менее 18 лет, и вы должны иметь право заключать договор. Указывайте точные данные и своевременно обновляйте их.',
                        'Храните свои учётные данные в безопасности. Вы несёте ответственность за действия приглашённых коллег и партнёров.',
                    ],
                ],
                [
                    'title' => '3. Подписки и оплата',
                    'paragraphs' => [
                        'Платные планы продлеваются автоматически в соответствии с выбранным графиком. Платежи могут включать стоимость подписки, налоги и подключённые дополнительные опции.',
                        'Вы можете отменить подписку в любое время на странице оплаты. Возвраты оформляются в соответствии с местными законами или обещаниями, данными при оформлении.',
                    ],
                ],
                [
                    'title' => '4. Допустимое использование',
                    'paragraphs' => [
                        'Используйте OMDb Stream только в законных целях и в пределах, предусмотренных нашими API и интерфейсом. Не извлекайте данные массово и не обходите ограничения по безопасности или скорости.',
                        'Мы можем приостановить или прекратить доступ, если вы используете сервис неправильно, наносите вред другим клиентам или нарушаете закон.',
                    ],
                ],
                [
                    'title' => '5. Завершение обслуживания',
                    'paragraphs' => [
                        'Вы можете закрыть аккаунт в настройках. Мы можем приостановить или прекратить доступ при отсутствии оплаты или нарушении этих условий.',
                        'Положения об оплате, ограничениях и разрешении споров сохраняют силу после закрытия аккаунта.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Вопросы и контакты',
                'body' => 'Напишите на :email, если у вас есть вопросы об этих условиях или нужна помощь с их пониманием.',
            ],
            'effective_date' => 'Действует с 1 мая 2024 года.',
        ],
        'privacy' => [
            'title' => 'Политика конфиденциальности',
            'meta_description' => 'Узнайте, как OMDb Stream собирает и использует вашу информацию.',
            'heading' => 'Политика конфиденциальности',
            'lede' => 'Мы объясняем, какие данные собираем, зачем их храним и как вы можете ими управлять.',
            'intro' => 'Эта политика описывает персональные данные, которые OMDb Stream обрабатывает для работы сервиса, и ваши возможности управления. Мы соблюдаем применимые законы о конфиденциальности и лучшие отраслевые практики.',
            'sections' => [
                [
                    'title' => '1. Какие данные мы собираем',
                    'paragraphs' => [
                        'Мы собираем сведения, которые вы передаёте, данные, возникающие при использовании сайта, а также информацию от проверенных партнёров. Состав зависит от того, как вы пользуетесь OMDb Stream.',
                    ],
                    'items' => [
                        'Данные аккаунта: имя, адрес электронной почты, организация и настройки подписки.',
                        'Платёжные данные, обрабатываемые нашим биллинговым партнёром; мы храним защищённые токены, а не полные номера карт.',
                        'Информация об использовании: посещённые страницы, выполненные запросы, сведения об устройстве и диагностика для повышения стабильности.',
                        'Интеграции и импорты, которые вы подключаете, включая списки или отзывы из сторонних сервисов.',
                    ],
                ],
                [
                    'title' => '2. Как мы используем данные',
                    'paragraphs' => [
                        'Мы используем персональные данные, чтобы поддерживать продукт, обеспечивать безопасность, настраивать отдельные элементы и общаться с вами.',
                    ],
                    'items' => [
                        'Запуск приложения, поддержка пользователей и работа функций Livewire и API.',
                        'Обработка платежей, выявление мошенничества и соблюдение ограничений выбранного тарифа.',
                        'Отправка транзакционных писем, советов по началу работы, новостей продукта и, при наличии разрешения, маркетинговых сообщений.',
                        'Анализ агрегированных тенденций использования для планирования мощностей и улучшения качества данных.',
                    ],
                ],
                [
                    'title' => '3. Передача и раскрытие',
                    'paragraphs' => [
                        'Мы не продаём ваши персональные данные. Мы делимся ограничённой информацией с подрядчиками, которые помогают работать OMDb Stream, по договорам, защищающим конфиденциальность.',
                        'Мы можем раскрывать данные, когда этого требует закон, либо чтобы защитить права, имущество или безопасность пользователей и партнёров.',
                    ],
                ],
                [
                    'title' => '4. Ваши выбор и права',
                    'paragraphs' => [
                        'В зависимости от места проживания вы можете иметь права на доступ, изменение, удаление или ограничение обработки данных. Мы выполняем подтверждённые запросы в установленные сроки.',
                    ],
                    'items' => [
                        'Обновляйте информацию профиля и настройки уведомлений в аккаунте.',
                        'Запрашивайте выгрузку или удаление данных через поддержку — мы подтверждаем каждую заявку перед действиями.',
                        'Отказывайтесь от маркетинговых писем через ссылку для отписки или настройки уведомлений.',
                    ],
                ],
                [
                    'title' => '5. Хранение и защита данных',
                    'paragraphs' => [
                        'Мы храним данные только столько, сколько нужно для работы сервиса, выполнения правовых обязанностей или разрешения споров. Когда данные больше не нужны, мы удаляем или анонимизируем их.',
                        'Мы защищаем информацию с помощью шифрования, управления доступом и регулярных проверок, чтобы предотвратить несанкционированный доступ.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Вопросы о конфиденциальности',
                'body' => 'Напишите на :email, чтобы задать вопросы о ваших данных или отправить запрос на конфиденциальность.',
            ],
            'effective_date' => 'Действует с 1 мая 2024 года.',
        ],
        'support' => [
            'title' => 'Центр поддержки',
            'meta_description' => 'Получите помощь по настройке, оплате и техническим вопросам OMDb Stream.',
            'heading' => 'Центр поддержки',
            'lede' => 'Мы быстро поможем с настройкой, оплатой и техническими задачами.',
            'intro' => 'Посмотрите короткие инструкции ниже или напишите нам, если нужна помощь специалиста.',
            'sections' => [
                [
                    'title' => '1. Первые шаги',
                    'paragraphs' => [
                        'Создайте аккаунт, пригласите коллег и подключите источники данных на странице «Настройки».',
                        'Пройдите чек-лист быстрого запуска, чтобы импортировать библиотеку и настроить уведомления под свои процессы.',
                    ],
                    'cta' => [
                        'label' => 'Открыть руководство по быстрому старту',
                        'href' => 'https://docs.omdbstream.test/get-started',
                    ],
                ],
                [
                    'title' => '2. Тарифы и оплата',
                    'paragraphs' => [
                        'Откройте страницу оплаты, чтобы сменить тариф, обновить способ оплаты или скачать счета в любое время.',
                        'Сообщите нам заранее о крупных запусках, чтобы мы предложили подходящие лимиты и подготовили экспорт данных.',
                    ],
                    'items' => [
                        'Переключайтесь между ежемесячной и годовой оплатой без обращения в поддержку.',
                        'Добавляйте резервные способы оплаты, чтобы избежать перерывов в сервисе.',
                        'Скачивайте квитанции и счета по требованию для своей отчётности.',
                    ],
                ],
                [
                    'title' => '3. Решение распространённых проблем',
                    'paragraphs' => [
                        'Проверьте страницу статуса системы и очистите кеш в панели, если что-то работает некорректно.',
                        'Обращаясь в поддержку, указывайте идентификаторы запросов, отметки времени и скриншоты — это помогает быстрее воспроизвести проблему.',
                    ],
                ],
                [
                    'title' => '4. Будем на связи',
                    'paragraphs' => [
                        'Подпишитесь на продуктовую рассылку или ежемесячные сессии вопросов и ответов, чтобы узнавать о новинках.',
                        'Делитесь отзывами и идеями в любое время — ваши предложения помогают развивать OMDb Stream.',
                    ],
                    'cta' => [
                        'label' => 'Отправить отзыв',
                        'href' => 'mailto:product@omdbstream.test',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Нужна дополнительная помощь?',
                'body' => 'Напишите на :email, укажите ID аккаунта и кратко опишите ситуацию. Мы ответим в течение одного рабочего дня.',
            ],
            'default_cta' => 'Связаться с поддержкой',
        ],
    ],
];
