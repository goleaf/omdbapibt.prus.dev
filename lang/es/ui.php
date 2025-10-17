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
            'terms' => 'Términos',
            'agreements' => 'Acuerdos',
            'privacy' => 'Privacidad',
            'support' => 'Soporte',
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
        'terms' => [
            'title' => 'Términos del servicio',
            'meta_description' => 'Lee las reglas que se aplican a tu cuenta de OMDb Stream.',
            'heading' => 'Términos del servicio',
            'lede' => 'Estos términos explican cómo funciona el servicio y qué puedes esperar.',
            'intro' => 'Al usar OMDb Stream aceptas estos términos y nuestra Política de privacidad. Léelos para conocer tus responsabilidades y las nuestras.',
            'sections' => [
                [
                    'title' => '1. Descripción del acuerdo',
                    'paragraphs' => [
                        'Estos Términos del servicio crean un contrato entre tú y OMDb Stream para todo uso del sitio.',
                        'Podemos actualizarlos cuando cambien las leyes o agreguemos funciones. Te avisaremos sobre cambios importantes y seguir usando el servicio después del aviso significa que aceptas los nuevos términos.',
                    ],
                ],
                [
                    'title' => '2. Cuentas y elegibilidad',
                    'paragraphs' => [
                        'Debes tener al menos 18 años y capacidad para firmar contratos. Proporciona datos correctos y mantenlos actualizados.',
                        'Mantén seguras tus credenciales. Eres responsable de lo que hagan los colaboradores o socios que invites.',
                    ],
                ],
                [
                    'title' => '3. Suscripciones y facturación',
                    'paragraphs' => [
                        'Los planes de pago se renuevan automáticamente según el calendario que elijas. Los cargos pueden incluir cuotas, impuestos y complementos que actives.',
                        'Puedes cancelar en cualquier momento desde la página de facturación. Los reembolsos se gestionan según las leyes locales o los compromisos indicados durante la compra.',
                    ],
                ],
                [
                    'title' => '4. Uso aceptable',
                    'paragraphs' => [
                        'Usa OMDb Stream solo con fines legales y dentro de los límites de nuestras API e interfaz. No intentes extraer, sobrecargar ni eludir la seguridad o los límites de uso.',
                        'Podemos suspender o finalizar el acceso si usas mal el servicio, perjudicas a otras personas o incumples la ley.',
                    ],
                ],
                [
                    'title' => '5. Finalización del servicio',
                    'paragraphs' => [
                        'Puedes cerrar tu cuenta desde la página de configuración. Podemos suspender o terminar el acceso si fallan los pagos o se infringen estos términos.',
                        'Las secciones sobre pagos, límites de uso y disputas siguen vigentes después de cerrar tu cuenta.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Preguntas y contacto',
                'body' => 'Escríbenos a :email si tienes preguntas sobre estos términos o necesitas ayuda para entenderlos.',
            ],
            'effective_date' => 'Vigente desde el 1 de mayo de 2024.',
        ],
        'privacy' => [
            'title' => 'Política de privacidad',
            'meta_description' => 'Consulta cómo OMDb Stream recopila y usa tu información.',
            'heading' => 'Política de privacidad',
            'lede' => 'Explicamos qué recopilamos, por qué lo conservamos y cómo puedes controlarlo.',
            'intro' => 'Esta Política de privacidad describe los datos personales que procesa OMDb Stream para operar el servicio y las opciones que tienes. Seguimos las leyes de privacidad aplicables y las mejores prácticas del sector.',
            'sections' => [
                [
                    'title' => '1. Información que recopilamos',
                    'paragraphs' => [
                        'Recopilamos los datos que compartes, la información generada cuando usas el sitio y datos de socios de confianza. Lo que reunimos depende de cómo utilices OMDb Stream.',
                    ],
                    'items' => [
                        'Datos de cuenta como tu nombre, correo electrónico, organización y preferencias de suscripción.',
                        'Información de pago gestionada por nuestro proveedor; almacenamos tokens seguros, no números completos.',
                        'Datos de uso como páginas vistas, búsquedas realizadas, información del dispositivo y diagnósticos que mejoran la fiabilidad.',
                        'Integraciones e importaciones que conectas, incluidas listas o reseñas de servicios asociados.',
                    ],
                ],
                [
                    'title' => '2. Cómo usamos la información',
                    'paragraphs' => [
                        'Usamos los datos personales para operar el producto, mantenerlo seguro, personalizar partes de la experiencia y comunicarnos contigo.',
                    ],
                    'items' => [
                        'Operar la aplicación, brindar soporte y alimentar funciones de Livewire y de la API.',
                        'Procesar pagos, detectar fraude y hacer cumplir los límites del plan.',
                        'Enviar correos transaccionales, guías de inicio, novedades y mensajes comerciales cuando esté permitido.',
                        'Analizar tendencias de uso agregadas para planificar capacidad y mejorar la calidad de los datos.',
                    ],
                ],
                [
                    'title' => '3. Compartir y divulgar',
                    'paragraphs' => [
                        'No vendemos tu información personal. Compartimos datos limitados con proveedores que operan OMDb Stream bajo contratos que protegen tu privacidad.',
                        'Podemos divulgar información cuando la ley lo exija o para proteger los derechos, la propiedad o la seguridad de nuestros usuarios y socios.',
                    ],
                ],
                [
                    'title' => '4. Tus opciones y derechos',
                    'paragraphs' => [
                        'Según dónde vivas, puedes tener derechos para acceder, modificar, eliminar o limitar el uso de tus datos personales. Atendemos las solicitudes verificadas dentro de los plazos requeridos.',
                    ],
                    'items' => [
                        'Actualiza tu perfil y las preferencias de notificación desde la configuración de la cuenta.',
                        'Solicita exportaciones o eliminación contactando al soporte; confirmamos cada solicitud antes de actuar.',
                        'Cancela los correos comerciales mediante el enlace de baja o tus ajustes de notificaciones.',
                    ],
                ],
                [
                    'title' => '5. Conservación y seguridad de datos',
                    'paragraphs' => [
                        'Conservamos los datos solo el tiempo necesario para operar el servicio, cumplir obligaciones legales o resolver disputas. Cuando ya no se necesitan, los eliminamos o los anonimizamos.',
                        'Protegemos tu información con cifrado, controles de acceso y revisiones periódicas para evitar accesos no autorizados.',
                    ],
                ],
            ],
            'contact' => [
                'title' => 'Preguntas sobre privacidad',
                'body' => 'Escríbenos a :email para preguntar por tus datos o enviar una solicitud de privacidad.',
            ],
            'effective_date' => 'Vigente desde el 1 de mayo de 2024.',
        ],
        'support' => [
            'title' => 'Centro de soporte',
            'meta_description' => 'Obtén ayuda con la configuración, la facturación y las preguntas técnicas de OMDb Stream.',
            'heading' => 'Centro de soporte',
            'lede' => 'Recibe ayuda rápida con la configuración, la facturación y los problemas técnicos.',
            'intro' => 'Consulta estas guías breves para encontrar respuestas comunes o contáctanos cuando necesites atención personalizada.',
            'sections' => [
                [
                    'title' => '1. Primeros pasos',
                    'paragraphs' => [
                        'Crea tu cuenta, invita a tus compañeros y conecta las fuentes de datos desde la página de Configuración.',
                        'Sigue la lista de inicio rápido para importar tu biblioteca y ajustar las preferencias de notificaciones.',
                    ],
                    'cta' => [
                        'label' => 'Abrir la guía de inicio rápido',
                        'href' => 'https://docs.omdbstream.test/get-started',
                    ],
                ],
                [
                    'title' => '2. Facturación y planes',
                    'paragraphs' => [
                        'Abre la página de facturación para cambiar de plan, actualizar métodos de pago o descargar facturas cuando lo necesites.',
                        'Avísanos antes de un gran lanzamiento para que podamos recomendar límites acordes a tu equipo y mantener exportaciones fluidas.',
                    ],
                    'items' => [
                        'Cambia entre planes mensuales y anuales sin contactar al soporte.',
                        'Agrega métodos de pago de respaldo para evitar interrupciones.',
                        'Descarga recibos y facturas bajo demanda para tus registros.',
                    ],
                ],
                [
                    'title' => '3. Resolver problemas comunes',
                    'paragraphs' => [
                        'Revisa la página de estado del sistema y borra los datos en caché desde el panel si notas algo extraño.',
                        'Al contactarnos, incluye identificadores de solicitud, marcas de tiempo y capturas para que ingeniería reproduzca el problema más rápido.',
                    ],
                ],
                [
                    'title' => '4. Mantente en contacto',
                    'paragraphs' => [
                        'Únete a nuestro boletín de producto o a las sesiones mensuales de preguntas y respuestas para enterarte de las novedades.',
                        'Comparte comentarios o ideas de funciones en cualquier momento; tus sugerencias ayudan a dar forma a OMDb Stream.',
                    ],
                    'cta' => [
                        'label' => 'Enviar comentarios',
                        'href' => 'mailto:product@omdbstream.test',
                    ],
                ],
            ],
            'contact' => [
                'title' => '¿Necesitas más ayuda?',
                'body' => 'Escríbenos a :email con tu ID de cuenta y un resumen breve. Respondemos en un día hábil.',
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
