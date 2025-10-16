<?php

return [
    'page' => [
        'meta' => [
            'title' => 'Soporte',
        ],
        'header' => 'Estamos aquí para ayudarte',
        'subheader' => 'Comparte los detalles de tu incidencia y nuestro equipo responderá en un día hábil.',
    ],
    'form' => [
        'heading' => 'Envíale un mensaje a nuestro equipo de soporte',
        'description' => 'Completa el formulario y te contactaremos por correo con los próximos pasos.',
        'fields' => [
            'name' => [
                'label' => 'Nombre',
                'placeholder' => 'Tu nombre completo',
            ],
            'email' => [
                'label' => 'Correo electrónico',
                'placeholder' => 'nombre@ejemplo.com',
            ],
            'subject' => [
                'label' => 'Asunto',
                'placeholder' => '¿En qué podemos ayudarte?',
            ],
            'message' => [
                'label' => 'Mensaje',
                'placeholder' => 'Incluye detalles para que podamos ayudarte más rápido.',
            ],
        ],
        'actions' => [
            'submit' => 'Enviar solicitud',
        ],
    ],
    'status' => [
        'submitted' => 'Tu mensaje fue enviado. Nuestro equipo de soporte responderá en breve.',
    ],
    'validation' => [
        'name' => [
            'required' => 'Por favor ingresa tu nombre.',
            'string' => 'El nombre debe ser texto.',
            'max' => 'El nombre debe tener menos de 255 caracteres.',
        ],
        'email' => [
            'required' => 'Por favor proporciona un correo electrónico.',
            'string' => 'El correo electrónico debe ser texto.',
            'email' => 'Ingresa un correo electrónico válido.',
            'max' => 'El correo electrónico debe tener menos de 255 caracteres.',
        ],
        'subject' => [
            'required' => 'Por favor agrega un asunto.',
            'string' => 'El asunto debe ser texto.',
            'max' => 'El asunto debe tener menos de 255 caracteres.',
        ],
        'message' => [
            'required' => 'Por favor escribe un mensaje para nuestro equipo.',
            'string' => 'El mensaje debe ser texto.',
            'max' => 'El mensaje debe tener menos de 2000 caracteres.',
        ],
    ],
];
