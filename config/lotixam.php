<?php

return [
    'mail_to' => env('MAIL_TO', 'xavier.espinar@lotixam.fr'),
    'mail_from_address' => env('MAIL_FROM_ADDRESS', env('MAIL_FROM', 'noreply@lotixam.fr')),
    'mail_from_name' => env('MAIL_FROM_NAME', 'Lotixam'),

    /** Nombre max de pièces jointes sur le formulaire /contact (évolutif côté UI). */
    'contact_max_attachments' => max(1, (int) env('CONTACT_MAX_ATTACHMENTS', 20)),
    /** Taille max par fichier (ko), alignée sur l’ancien plafond ~10 Mo. */
    'contact_max_attachment_kb' => max(1, (int) env('CONTACT_MAX_ATTACHMENT_KB', 10240)),
];
