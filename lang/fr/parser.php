<?php

return [
    'moderation' => [
        'decision' => [
            'heading' => 'Décision',
            'description' => 'Ajoutez du contexte à votre décision. Les notes sont obligatoires lorsque vous rejetez une entrée.',
            'fields' => [
                'notes' => 'Notes',
            ],
            'placeholder' => [
                'notes' => 'Résumez pourquoi ce payload doit être approuvé ou rejeté',
            ],
            'actions' => [
                'approve' => 'Approuver et enregistrer',
                'reject' => 'Rejeter l’entrée',
            ],
        ],
        'validation' => [
            'notes' => [
                'required' => 'Ajoutez une note de rejet avant de continuer.',
                'string' => 'Les notes de rejet doivent être du texte brut.',
                'max' => 'Les notes de rejet ne peuvent pas dépasser :max caractères.',
            ],
        ],
    ],
    'trigger' => [
        'workload_required' => 'Veuillez sélectionner une charge de traitement à lancer.',
        'workload_string' => 'La charge de traitement doit être fournie sous forme de texte.',
        'workload_enum' => 'La charge de traitement sélectionnée n’est pas valide.',
        'workload_attribute' => 'charge de travail du parseur',
    ],
];
