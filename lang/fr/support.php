<?php

return [
    'page' => [
        'meta' => [
            'title' => 'Assistance',
        ],
        'header' => 'Nous sommes là pour vous aider',
        'subheader' => 'Donnez-nous les détails de votre demande et notre équipe vous répondra sous un jour ouvrable.',
    ],
    'form' => [
        'heading' => 'Envoyez un message à notre équipe d\'assistance',
        'description' => 'Remplissez le formulaire ci-dessous et nous vous répondrons par e-mail avec la marche à suivre.',
        'fields' => [
            'name' => [
                'label' => 'Nom',
                'placeholder' => 'Votre nom complet',
            ],
            'email' => [
                'label' => 'E-mail',
                'placeholder' => 'nom@exemple.com',
            ],
            'subject' => [
                'label' => 'Objet',
                'placeholder' => 'Comment pouvons-nous vous aider ?',
            ],
            'message' => [
                'label' => 'Message',
                'placeholder' => 'Ajoutez des détails pour que nous puissions répondre plus rapidement.',
            ],
        ],
        'actions' => [
            'submit' => 'Envoyer la demande',
        ],
    ],
    'status' => [
        'submitted' => 'Votre message a été envoyé. Un membre de notre équipe vous répondra sous peu.',
    ],
    'validation' => [
        'name' => [
            'required' => 'Veuillez saisir votre nom.',
            'string' => 'Le nom doit être un texte.',
            'max' => 'Le nom doit contenir moins de 255 caractères.',
        ],
        'email' => [
            'required' => 'Veuillez indiquer une adresse e-mail.',
            'string' => 'L\'adresse e-mail doit être un texte.',
            'email' => 'Veuillez saisir une adresse e-mail valide.',
            'max' => 'L\'adresse e-mail doit contenir moins de 255 caractères.',
        ],
        'subject' => [
            'required' => 'Veuillez ajouter un objet.',
            'string' => 'L\'objet doit être un texte.',
            'max' => 'L\'objet doit contenir moins de 255 caractères.',
        ],
        'message' => [
            'required' => 'Veuillez écrire un message pour notre équipe.',
            'string' => 'Le message doit être un texte.',
            'max' => 'Le message doit contenir moins de 2000 caractères.',
        ],
    ],
];
