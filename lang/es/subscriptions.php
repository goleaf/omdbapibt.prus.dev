<?php

return [
    'status' => [
        'already_subscribed' => 'Ya tienes una suscripción activa.',
        'cancellation_scheduled' => 'Tu suscripción ha sido cancelada y permanecerá activa hasta el :date.',
        'cancelled' => 'Tu suscripción ha sido cancelada.',
        'resumed' => 'Tu suscripción se ha reanudado correctamente.',
    ],
    'errors' => [
        'price_required' => 'Se requiere un identificador de precio de Stripe para iniciar tu prueba.',
        'missing_subscription_cancel' => 'No tienes una suscripción activa que puedas cancelar.',
        'already_cancelled' => 'Tu suscripción ya está cancelada.',
        'cancel_failed' => 'No pudimos cancelar tu suscripción. Por favor, inténtalo de nuevo.',
        'missing_subscription_resume' => 'No tienes una suscripción activa que puedas reanudar.',
        'not_on_grace_period' => 'Tu suscripción no se puede reanudar porque no está dentro del período de gracia.',
        'resume_failed' => 'No pudimos reanudar tu suscripción. Por favor, inténtalo de nuevo.',
        'invoice_load_failed' => 'No pudimos cargar tu próxima factura.',
        'premium_required' => 'Se requiere una suscripción premium para acceder a esta área.',
        'access_required' => 'Se requiere una suscripción activa para acceder a esta área.',
    ],
];
