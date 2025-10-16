<?php

return [
    'page' => [
        'meta' => [
            'title' => 'Support',
        ],
        'header' => 'We\'re here to help',
        'subheader' => 'Share the details of your issue and our team will respond within one business day.',
    ],
    'form' => [
        'heading' => 'Send our support team a note',
        'description' => 'Fill out the form below and we\'ll follow up by email with next steps.',
        'fields' => [
            'name' => [
                'label' => 'Name',
                'placeholder' => 'Your full name',
            ],
            'email' => [
                'label' => 'Email',
                'placeholder' => 'name@example.com',
            ],
            'subject' => [
                'label' => 'Subject',
                'placeholder' => 'What can we help you with?',
            ],
            'message' => [
                'label' => 'Message',
                'placeholder' => 'Add any helpful details so we can get you answers faster.',
            ],
        ],
        'actions' => [
            'submit' => 'Submit request',
        ],
    ],
    'status' => [
        'submitted' => 'Your message was sent. A member of our support team will reply shortly.',
    ],
    'validation' => [
        'name' => [
            'required' => 'Please enter your name.',
            'string' => 'The name must be text.',
            'max' => 'Names must be fewer than 255 characters.',
        ],
        'email' => [
            'required' => 'Please provide an email address.',
            'string' => 'Email addresses must be text.',
            'email' => 'Enter a valid email address.',
            'max' => 'Email addresses must be fewer than 255 characters.',
        ],
        'subject' => [
            'required' => 'Please add a subject.',
            'string' => 'The subject must be text.',
            'max' => 'Subjects must be fewer than 255 characters.',
        ],
        'message' => [
            'required' => 'Please include a message for our team.',
            'string' => 'The message must be text.',
            'max' => 'Messages must be fewer than 2000 characters.',
        ],
    ],
];
