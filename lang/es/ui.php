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
            'about' => 'Acerca de',
            'support' => 'Soporte',
            'components' => 'Componentes de la interfaz de usuario',
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
        'menu' => [
            'label' => 'Navegación',
            'open' => 'Abrir navegación',
            'close' => 'Cerrar navegación',
        ],
        'footer' => [
            'tagline' => 'El compañero de metadatos para flujos de trabajo modernos de cine y TV.',
            'sections' => [
                'product' => [
                    'title' => 'Producto',
                ],
                'company' => [
                    'title' => 'Compañía',
                ],
                'legal' => [
                    'title' => 'Legal',
                    'links' => [
                        'terms' => 'Términos',
                        'privacy' => 'Privacidad',
                    ],
                ],
            ],
            'support' => [
                'title' => '¿Necesitas ayuda?',
                'body' => 'Escríbenos a :email y responderemos en un día hábil.',
                'link_label' => 'Contactar soporte',
            ],
            'copyright' => '© :year OMDb Stream. Todos los derechos reservados.',
        ],
    ],
    'admin' => [
        'panel' => [
            'title' => 'Panel de control administrativo',
            'subtitle' => 'Administra los recursos del catálogo, los metadatos editoriales y las taxonomías del portal público.',
            'actions' => [
                'create' => 'Crear',
                'update' => 'Guardar cambios',
                'clear' => 'Limpiar formulario',
                'reset' => 'Reiniciar formulario',
                'edit' => 'Editar',
                'delete' => 'Eliminar',
            ],
            'fields' => [
                'title' => 'Título',
                'slug' => 'Identificador legible',
                'status' => 'Estado',
                'release_date' => 'Fecha de estreno',
                'vote_average' => 'Valoración media',
                'adult' => 'Marcado como contenido adulto',
                'search' => 'Buscar',
                'name' => 'Nombre',
                'first_air_date' => 'Fecha de primera emisión',
                'department' => 'Departamento',
                'birthday' => 'Fecha de nacimiento',
                'gender' => 'Género',
                'popularity' => 'Popularidad',
                'tmdb_id' => 'ID de TMDb',
                'code' => 'Código',
                'native_name' => 'Nombre nativo',
                'active' => 'Activo',
            ],
            'placeholders' => [
                'movie_title' => 'ej. The Matrix',
                'slug' => 'Déjalo vacío para generar automáticamente',
                'status' => 'Planeada, Estrenada, En producción…',
                'search_movies' => 'Buscar por título o slug…',
                'show_name' => 'ej. True Detective',
                'search_shows' => 'Buscar por nombre de serie…',
                'person_name' => 'ej. Keanu Reeves',
                'department' => 'Actuación, Dirección, Guion…',
                'search_people' => 'Buscar por nombre…',
                'genre_name' => 'ej. Ciencia ficción',
                'tmdb_id' => 'Identificador numérico de TMDb',
                'search_genres' => 'Buscar por género…',
                'language_name' => 'ej. Inglés',
                'native_name' => 'ej. Español',
                'search_languages' => 'Buscar por idioma o código…',
                'country_name' => 'ej. Estados Unidos',
                'search_countries' => 'Buscar por país o código…',
                'search_tags' => 'Buscar por nombre o slug de etiqueta…',
            ],
            'table' => [
                'movie' => 'Película',
                'show' => 'Serie',
                'person' => 'Persona',
                'genre' => 'Género',
                'language' => 'Idioma',
                'country' => 'País',
                'actions' => 'Acciones',
                'empty' => 'No hay registros que coincidan con los filtros.',
            ],
            'tags' => [
                'fields' => [
                    'name_en' => 'Nombre (inglés)',
                    'name_es' => 'Nombre (español)',
                    'name_fr' => 'Nombre (francés)',
                    'type' => 'Tipo de etiqueta',
                ],
                'placeholders' => [
                    'name_en' => 'ej. Ganadora de premios',
                    'name_es' => 'Traducción opcional…',
                    'name_fr' => 'Traducción opcional…',
                    'search' => 'Buscar por nombre o slug de etiqueta…',
                ],
                'types' => [
                    'system' => 'Sistema',
                    'community' => 'Comunidad',
                ],
                'merge' => [
                    'title' => 'Fusionar etiquetas duplicadas',
                    'subtitle' => 'Consolida vocabularios superpuestos y mantén limpias las colecciones.',
                    'source' => 'ID de etiqueta origen',
                    'target' => 'ID de etiqueta destino',
                    'placeholders' => [
                        'source' => 'ID de la etiqueta que se fusionará',
                        'target' => 'ID de la etiqueta que conservarás',
                    ],
                    'action' => 'Fusionar etiquetas',
                ],
                'table' => [
                    'tag' => 'Etiqueta',
                    'type' => 'Tipo',
                    'usage' => 'Uso',
                ],
            ],
            'labels' => [
                'active' => 'Activo',
                'inactive' => 'Inactivo',
            ],
            'sections' => [
                'movies' => [
                    'title' => 'Editor de películas',
                    'subtitle' => 'Gestiona fichas, fechas de estreno y consistencia del catálogo.',
                    'nav' => 'Películas',
                    'heading' => 'Gestionar películas',
                ],
                'tv_shows' => [
                    'title' => 'Editor de series',
                    'subtitle' => 'Controla episodios, emisiones y disponibilidad.',
                    'nav' => 'Series',
                    'heading' => 'Gestionar series',
                ],
                'people' => [
                    'title' => 'Directorio de personas',
                    'subtitle' => 'Mantén el reparto y el equipo con créditos precisos.',
                    'nav' => 'Personas',
                    'heading' => 'Gestionar personas',
                ],
                'genres' => [
                    'title' => 'Taxonomía de géneros',
                    'subtitle' => 'Controla el vocabulario usado en las herramientas de descubrimiento.',
                    'nav' => 'Géneros',
                    'heading' => 'Gestionar géneros',
                ],
                'languages' => [
                    'title' => 'Catálogo de idiomas',
                    'subtitle' => 'Configura idiomas de audio y subtítulos disponibles.',
                    'nav' => 'Idiomas',
                    'heading' => 'Gestionar idiomas',
                ],
                'countries' => [
                    'title' => 'Registro de países',
                    'subtitle' => 'Mantén los datos de origen alineados con los códigos ISO.',
                    'nav' => 'Países',
                    'heading' => 'Gestionar países',
                ],
                'tags' => [
                    'title' => 'Curación de etiquetas',
                    'subtitle' => 'Modera etiquetas de descubrimiento, fusiona duplicados y guía los espacios editoriales.',
                    'nav' => 'Etiquetas',
                    'heading' => 'Gestionar etiquetas',
                ],
            ],
            'people' => [
                'gender_unknown' => 'Sin especificar',
                'gender_female' => 'Femenino',
                'gender_male' => 'Masculino',
                'gender_non_binary' => 'No binario',
            ],
            'relationships' => [
                'title' => 'Taxonomías, etiquetas y localizaciones',
                'subtitle' => 'Asocia géneros, etiquetas curadas, idiomas y países de origen para potenciar el portal.',
                'suggestions' => 'Sugerencias',
                'empty' => 'No hay sugerencias para la búsqueda actual.',
                'genres' => [
                    'label' => 'Géneros',
                    'help' => 'Usa etiquetas de género curadas para mejorar los filtros de descubrimiento y el escaparate.',
                    'none' => 'Aún no hay géneros seleccionados.',
                    'remove' => 'Quitar :name de los géneros seleccionados',
                ],
                'tags' => [
                    'label' => 'Etiquetas',
                    'help' => 'Fija etiquetas editoriales y del sistema para destacar títulos en la parrilla.',
                    'none' => 'Aún no hay etiquetas seleccionadas.',
                    'remove' => 'Quitar :name de las etiquetas seleccionadas',
                ],
                'languages' => [
                    'label' => 'Idiomas',
                    'help' => 'Controla doblajes y subtítulos disponibles para cada título.',
                    'none' => 'Aún no hay idiomas seleccionados.',
                    'remove' => 'Quitar :name de los idiomas seleccionados',
                ],
                'countries' => [
                    'label' => 'Países',
                    'help' => 'Registra el país de producción para reportes y cumplimiento.',
                    'none' => 'Aún no hay países seleccionados.',
                    'remove' => 'Quitar :name de los países seleccionados',
                ],
            ],
        ],
    ],
    'dashboard' => [
        'title' => 'Panel',
        'layout' => [
            'sidebar_heading' => 'Navegación',
            'default_header' => 'Resumen del panel',
        ],
        'nav' => [
            'overview' => 'Resumen',
            'manage_subscription' => 'Administrar suscripción',
        ],
        'welcome_heading' => '¡Bienvenido de nuevo!',
        'welcome_body' => 'Revisa los detalles de tu plan, gestiona la facturación y realiza cambios en tu suscripción en tiempo real.',
        'insights_card' => [
            'title' => 'Información del plan',
            'subscription_status' => 'Estado de la suscripción',
            'trial_days' => 'Días de prueba',
            'next_invoice' => 'Próxima factura',
        ],
        'cards' => [
            'manage_subscription' => 'Administrar suscripción',
            'watchlist' => 'Lista de seguimiento',
        ],
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
        'tv_heading' => 'Televisión',
        'credits_heading' => 'Créditos de :type',
        'credit_types' => [
            'cast' => 'Elenco',
            'crew' => 'Equipo técnico',
        ],
    ],
    'pages' => [
        'about' => [
            'title' => 'Acerca de OMDb Stream',
            'meta_description' => 'Descubre por qué creamos OMDb Stream y conoce al equipo que cuida la experiencia del catálogo.',
            'heading' => 'Conoce al equipo detrás de OMDb Stream',
            'lede' => 'Combinamos fuentes abiertas de datos con curación editorial para ayudar a los fans del cine y la TV a decidir qué ver.',
            'intro' => 'OMDb Stream nació de la curiosidad de cinéfilos que querían herramientas de descubrimiento más precisas. Mezclamos datos de OMDb y TMDb con nuestro propio pipeline editorial para que críticos, curadores y espectadores casuales exploren el catálogo con confianza.',
            'sections' => [
                [
                    'title' => 'Nuestra misión',
                    'paragraphs' => [
                        'Los catálogos en streaming cambian cada semana, pero los metadatos fiables no deberían hacerlo. Organizamos títulos, créditos y disponibilidad para que siempre sepas dónde mirar y qué hace especial a cada historia.',
                        'Desde reseñistas independientes hasta servicios boutique, diseñamos herramientas que se adaptan a cada flujo de trabajo sin saturar con jerga ni paneles complejos.',
                    ],
                ],
                [
                    'title' => 'Lo que construimos',
                    'paragraphs' => [
                        'OMDb Stream equilibra la mirada editorial con la automatización. Tras bambalinas normalizamos títulos, detectamos tendencias y destacamos recomendaciones que se sienten hechas a mano.',
                    ],
                    'items' => [
                        'Una lista unificada que se mantiene sincronizada en todos tus dispositivos.',
                        'Carruseles curados de estrenos, joyas ocultas y favoritos del equipo actualizados a diario.',
                        'Páginas de personas y franquicias que conectan filmografías con contexto, curiosidades e historial de estrenos.',
                    ],
                ],
                [
                    'title' => 'Cómo trabajamos',
                    'paragraphs' => [
                        'Somos un equipo distribuido en tres husos horarios. Colaboramos en sprints enfocados para lanzar mejoras frecuentes sin sacrificar detalles.',
                    ],
                    'items' => [
                        'Un estándar de calidad que prioriza accesibilidad, rendimiento y claridad.',
                        'Alianzas con comunidades de datos públicos y licenciatarios boutique que comparten nuestra pasión por el cine.',
                        'Hojas de ruta transparentes moldeadas por los comentarios de los suscriptores y el uso real de la plataforma.',
                    ],
                ],
                [
                    'title' => 'Hablemos',
                    'paragraphs' => [
                        '¿Tienes una idea de función, una solicitud de corrección o quieres colaborar en un proyecto editorial? Leemos cada mensaje.',
                    ],
                    'cta' => [
                        'label' => 'Escríbenos',
                    ],
                ],
            ],
        ],
        'terms' => [
            'title' => 'Términos del servicio',
            'meta_description' => 'Consulta los términos que rigen tu cuenta, las suscripciones y el uso de las herramientas de catálogo de OMDb Stream.',
            'heading' => 'Términos del servicio',
            'lede' => 'Estos términos explican cómo usar OMDb Stream de forma responsable y qué puedes esperar del servicio.',
            'intro' => 'OMDb Stream ofrece metadatos curados, herramientas y funciones de suscripción impulsadas por OMDb y TMDb. Al crear una cuenta, adquirir una suscripción o utilizar el sitio, aceptas las reglas descritas a continuación además de nuestra Política de privacidad.',
            'sections' => [
                [
                    'title' => '1. Descripción general del acuerdo',
                    'paragraphs' => [
                        'Estos Términos del servicio constituyen un acuerdo vinculante entre tú y OMDb Stream. Se aplican a todas las personas, cuentas y organizaciones que acceden a la plataforma.',
                        'Podemos actualizar los términos para reflejar nuevas funciones o requisitos legales. Cuando los cambios sean sustanciales, publicaremos un aviso en la aplicación o enviaremos un correo al contacto principal de tu cuenta. Si continúas usando OMDb Stream después de la fecha de vigencia, aceptas los términos actualizados.',
                    ],
                ],
                [
                    'title' => '2. Cuentas y elegibilidad',
                    'paragraphs' => [
                        'Debes tener al menos 18 años —o la mayoría de edad en tu jurisdicción— y capacidad para celebrar contratos a fin de usar OMDb Stream. Al registrarte aceptas proporcionar información precisa, actualizada y completa.',
                        'Mantén seguras tus credenciales y avísanos de inmediato si detectas acceso no autorizado. Si invitas a compañeros o concedas acceso a tu organización, eres responsable de su actividad dentro de tu suscripción.',
                    ],
                ],
                [
                    'title' => '3. Suscripciones y facturación',
                    'paragraphs' => [
                        'Los planes de pago se renuevan automáticamente con la periodicidad que selecciones. Nos autorizas, junto con nuestro procesador de pagos, a cargar en el método registrado las cuotas recurrentes, impuestos aplicables y cualquier complemento que actives.',
                        'Puedes cancelar en el portal de facturación en cualquier momento. La cancelación detiene las renovaciones futuras, pero no genera reembolsos proporcionales del periodo en curso salvo que la ley lo exija. Algunas funciones experimentales pueden tener límites de uso o términos adicionales descritos durante el checkout.',
                    ],
                ],
                [
                    'title' => '4. Uso aceptable',
                    'paragraphs' => [
                        'Utiliza OMDb Stream únicamente con fines legales y dentro de los patrones de uso admitidos por nuestras API e interfaz. No intentes extraer datos masivamente, descompilar, sobrecargar ni eludir límites de velocidad, autenticación o controles de seguridad.',
                        'Podemos suspender o terminar el acceso sin previo aviso si haces un uso indebido del servicio, interfieres con otros clientes o incumples leyes aplicables, incluidas las de propiedad intelectual y protección de datos.',
                    ],
                ],
                [
                    'title' => '5. Contenido y datos de terceros',
                    'paragraphs' => [
                        'Nuestro catálogo combina datos de OMDb, TMDb y otros socios con licencia. Aunque procuramos la precisión, la información se proporciona tal cual y puede cambiar sin aviso. Debes verificar los derechos antes de redistribuir metadatos, material gráfico o análisis.',
                        'Debes cumplir los términos de OMDb, TMDb y demás licenciantes aplicables al exportar o integrar sus datos. Si un proveedor revoca el acceso, podremos retirar contenido o funciones procurando afectar lo menos posible tu suscripción.',
                    ],
                ],
                [
                    'title' => '6. Terminación',
                    'paragraphs' => [
                        'Puedes cerrar tu cuenta en cualquier momento desde la configuración. Podemos suspender o cancelar cuentas que incumplan estos términos, no paguen las cuotas o representen riesgos de seguridad o cumplimiento.',
                        'Al finalizar la cuenta, tu derecho a usar OMDb Stream termina de inmediato. Las disposiciones que, por su naturaleza, deban sobrevivir (como indemnizaciones, limitaciones de responsabilidad y resolución de disputas) seguirán vigentes.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Preguntas y contacto',
                'body' => 'Si tienes dudas sobre estos términos, escríbenos a :email o responde cualquier mensaje del equipo de OMDb Stream.',
            ],
            'effective_date' => 'Vigente desde el 1 de mayo de 2024.',
        ],
        'privacy' => [
            'title' => 'Política de privacidad',
            'meta_description' => 'Conoce cómo OMDb Stream recopila, utiliza y protege tu información personal.',
            'heading' => 'Política de privacidad',
            'lede' => 'Respetamos tu privacidad y explicamos nuestras prácticas de datos con claridad.',
            'intro' => 'Esta Política de privacidad describe los datos personales que recopila OMDb Stream, cómo los usamos para operar la plataforma y las opciones que tienes para controlar tu información. Procesamos datos conforme a las normativas de privacidad aplicables y las mejores prácticas del sector.',
            'sections' => [
                [
                    'title' => '1. Información que recopilamos',
                    'paragraphs' => [
                        'Recopilamos la información que proporcionas directamente, los datos que se generan cuando interactúas con el sitio y detalles limitados de terceros de confianza. La información exacta depende de cómo utilices OMDb Stream.',
                    ],
                    'items' => [
                        'Datos de cuenta como nombre, correo electrónico, organización y preferencias de suscripción.',
                        'Información de pago procesada de forma segura por nuestro proveedor de facturación; almacenamos tokens, no los números completos de tarjeta.',
                        'Datos de uso como páginas visitadas, consultas ejecutadas, identificadores de dispositivo y diagnósticos que ayudan a mejorar la confiabilidad.',
                        'Integraciones e importaciones autorizadas, como listas o reseñas sincronizadas desde servicios asociados.',
                    ],
                ],
                [
                    'title' => '2. Cómo usamos la información',
                    'paragraphs' => [
                        'Procesamos datos personales para ofrecer el servicio, personalizar recomendaciones, garantizar la seguridad y comunicarnos contigo sobre tu cuenta.',
                    ],
                    'items' => [
                        'Operar la aplicación, brindar soporte y ofrecer funciones de Livewire y API.',
                        'Procesar pagos, detectar fraude y hacer cumplir los límites de uso asociados a tu plan.',
                        'Enviar mensajes transaccionales, guías de incorporación, novedades de producto y comunicaciones comerciales cuando sea permitido.',
                        'Analizar tendencias agregadas de uso para planificar capacidad, mejorar la relevancia de las búsquedas y elevar la calidad de los datos.',
                    ],
                ],
                [
                    'title' => '3. Compartir y divulgar',
                    'paragraphs' => [
                        'No vendemos tu información personal. Compartimos datos limitados con proveedores que nos ayudan a operar OMDb Stream y solo bajo contratos que les exigen protegerla.',
                        'Podemos divulgar información cuando la ley lo exija, para responder a solicitudes legales válidas o para proteger los derechos, la propiedad o la seguridad de nuestros usuarios y socios.',
                    ],
                ],
                [
                    'title' => '4. Tus opciones y derechos',
                    'paragraphs' => [
                        'Según tu ubicación, puedes tener derechos para acceder, corregir, eliminar o restringir el tratamiento de tus datos personales. Atendemos solicitudes verificadas dentro de los plazos establecidos por la ley aplicable.',
                    ],
                    'items' => [
                        'Actualiza la información del perfil y tus preferencias de comunicación desde la configuración de la cuenta.',
                        'Exporta datos o solicita su eliminación contactando a soporte; autenticaremos la solicitud antes de actuar.',
                        'Cancela el envío de correos comerciales mediante el enlace para darse de baja o ajustando tus preferencias de notificación.',
                    ],
                ],
                [
                    'title' => '5. Conservación y seguridad de datos',
                    'paragraphs' => [
                        'Conservamos los datos personales solo durante el tiempo necesario para prestar el servicio, cumplir obligaciones legales o resolver disputas. Cuando ya no se necesitan, los eliminamos o anonimizamos.',
                        'Implementamos medidas técnicas, administrativas y físicas —incluyendo cifrado en tránsito, controles de acceso y auditorías periódicas— para proteger tu información contra accesos no autorizados.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Dudas sobre privacidad',
                'body' => 'Escríbenos a :email para enviar una solicitud de datos o preguntar cómo tratamos tu información.',
            ],
            'effective_date' => 'Vigente desde el 1 de mayo de 2024.',
        ],
        'support' => [
            'title' => 'Centro de soporte',
            'meta_description' => 'Encuentra recursos de ayuda, guías de facturación y opciones de contacto con el soporte de OMDb Stream.',
            'heading' => 'Centro de soporte',
            'lede' => 'Estamos aquí para ayudarte a lanzar rápido y resolver incidencias sin fricción.',
            'intro' => 'Utiliza estas guías para aprovechar al máximo OMDb Stream. Nuestro equipo de soporte trabaja junto a especialistas de ingeniería y producto para ofrecer respuestas precisas y accionables.',
            'sections' => [
                [
                    'title' => '1. Primeros pasos',
                    'paragraphs' => [
                        'Comienza conectando tus fuentes de metadatos preferidas e invitando a colaboradores desde el panel de cuenta. La lista de verificación de onboarding te guía para habilitar componentes de Livewire, configurar la sincronización de listas y ajustar notificaciones.',
                        'Si migras desde otra herramienta, exporta tu catálogo actual en CSV o JSON e impórtalo desde el panel del analizador. Nuestro equipo puede revisar tu plan de migración para reducir el tiempo de inactividad.',
                    ],
                    'cta' => [
                        'label' => 'Ver la guía de onboarding',
                        'href' => 'https://docs.omdbstream.test/get-started',
                    ],
                ],
                [
                    'title' => '2. Facturación y planes',
                    'paragraphs' => [
                        'Administra métodos de pago, descarga facturas y cambia de plan desde el portal de facturación. Los cambios se aplican de inmediato y los ajustes prorrateados aparecerán en tu próxima factura.',
                        'Contáctanos antes de escalar equipos grandes o alcanzar límites de API para recomendarte el mejor plan y alinear los requisitos de exportación de datos.',
                    ],
                    'items' => [
                        'Actualiza tu método de pago predeterminado y añade tarjetas de respaldo para cuentas compartidas.',
                        'Revisa las próximas fechas de renovación y activa alertas de facturación para tu equipo financiero.',
                        'Solicita facturas con IVA u otros requisitos fiscales directamente desde el portal.',
                    ],
                ],
                [
                    'title' => '3. Solución de problemas técnicos',
                    'paragraphs' => [
                        'La mayoría de los problemas se resuelven limpiando la caché desde las herramientas del panel, revisando la página de estado del sistema o consultando las entregas recientes de webhooks. Nuestra página de estado publica actualizaciones en tiempo real sobre los pipelines de ingesta y la búsqueda.',
                        'Cuando necesites escalar un ticket, incluye identificadores de solicitud, marcas de tiempo y capturas de pantalla relevantes. Este contexto ayuda a ingeniería a reproducir el problema con rapidez.',
                    ],
                ],
                [
                    'title' => '4. Mantente conectado',
                    'paragraphs' => [
                        'Únete a nuestros seminarios web mensuales y al boletín de novedades para conocer las nuevas funciones y alianzas de datos.',
                        'Nos encanta recibir comentarios sobre prioridades de la hoja de ruta y mejoras de flujo de trabajo: tus sugerencias ayudan a dar forma a OMDb Stream.',
                    ],
                    'cta' => [
                        'label' => 'Sugerir una función',
                        'href' => 'mailto:product@omdbstream.test',
                    ],
                ],
            ],
            'contact' => [
                'title' => '¿Necesitas más ayuda?',
                'body' => 'Envía un correo a :email con tu ID de cuenta y un resumen breve. Un especialista de soporte responderá en un día hábil.',
            ],
            'default_cta' => 'Contactar soporte',
        ],
    ],
    'impersonation' => [
        'banner_title' => 'Suplantando a :name',
        'banner_help' => 'Estás navegando el sitio como este usuario. Cuando termines, regresa a tu cuenta de administrador.',
        'stop' => 'Dejar de suplantar',
        'stopped' => 'Sesión de suplantación finalizada. Ahora estás de vuelta en tu cuenta de administrador.',
    ],
];
