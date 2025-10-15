<?php

return [
    'status' => [
        'already_subscribed' => 'Vous avez déjà un abonnement actif.',
        'cancellation_scheduled' => 'Votre abonnement a été annulé et restera actif jusqu'au :date.',
        'cancelled' => 'Votre abonnement a été annulé.',
        'resumed' => 'Votre abonnement a été réactivé avec succès.',
    ],
    'errors' => [
        'price_required' => 'Un identifiant de prix Stripe est requis pour démarrer votre essai.',
        'missing_subscription_cancel' => 'Vous n'avez pas d'abonnement actif à annuler.',
        'already_cancelled' => 'Votre abonnement est déjà annulé.',
        'cancel_failed' => 'Nous n'avons pas pu annuler votre abonnement. Veuillez réessayer.',
        'missing_subscription_resume' => 'Vous n'avez pas d'abonnement actif à reprendre.',
        'not_on_grace_period' => 'Votre abonnement ne peut pas être repris car il n'est pas dans sa période de grâce.',
        'resume_failed' => 'Nous n'avons pas pu reprendre votre abonnement. Veuillez réessayer.',
        'invoice_load_failed' => 'Nous n'avons pas pu charger votre prochaine facture.',
        'premium_required' => 'Un abonnement premium est requis pour accéder à cette zone.',
        'access_required' => 'Un abonnement actif est requis pour accéder à cette zone.',
    ],
];
