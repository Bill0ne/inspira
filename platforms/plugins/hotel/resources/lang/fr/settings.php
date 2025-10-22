<?php

return [
    'hotel' => 'Hôtel',
    'invoicing' => [
        'company_name' => 'Nom de l\'entreprise',
        'company_address' => 'Adresse de l\'entreprise',
        'company_email' => 'Email de l\'entreprise',
        'company_phone' => 'Téléphone de l\'entreprise',
        'company_logo' => 'Logo de l\'entreprise',
    ],
    'using_custom_font_for_invoice' => 'Utiliser une police personnalisée pour la facture?',
    'invoice_font_family' => 'Famille de police pour facture (Fonctionne uniquement pour les langues latines)',
    'enable_invoice_stamp' => 'Activer le tampon de facture?',
    'invoice_support_arabic_language' => 'Prendre en charge la langue arabe dans la facture?',
    'invoice_code_prefix' => 'Préfixe du code de facture',
    'invoice_settings' => 'Facture',
    'invoice_settings_description' => 'Paramètres des informations de facturation',
    'general' => [
        'title' => 'Général',
        'description' => 'Paramètres généraux pour l\'hôtel',
        'enable_booking' => 'Activer les réservations?',
        'maximum_number_of_guests' => 'Nombre maximum d\'invités',
        'minimum_number_of_guests' => 'Nombre minimum d\'invités',
        'booking_number_format' => [
            'title' => 'Format du numéro de réservation (optionnel)',
            'description' => 'Le numéro de réservation par défaut commence à un nombre spécifique. Vous pouvez personnaliser les nombres de début et de fin pour le numéro de réservation. Par exemple, le numéro de réservation sera affiché comme #:format.',
            'start_with' => 'Nombre de début',
            'end_with' => 'Nombre de fin',
        ],
        'booking_date_format' => 'Format de date de réservation',
        'default_system_date_format' => 'Format de date système par défaut',
        'enable_food_order' => 'Activer la commande de nourriture?',
    ],
    'review' => [
        'title' => 'Avis',
        'description' => 'Paramètres des avis pour l\'hôtel',
        'enable_review_room' => 'Activer les avis?',
        'reviews_per_page' => 'Nombre d\'avis par page?',
    ],
    'currency' => [
        'title' => 'Devises',
        'description' => 'Liste des devises utilisées sur le site',
    ],
    'invoice' => [
        'title' => 'Factures',
        'description' => 'Paramètres des informations de facturation',
        'add_language_support' => 'Ajouter la prise en charge de la langue',
        'only_latin_languages' => 'Uniquement les langues latines',
        'confirm_reset' => 'Confirmer la réinitialisation du modèle de facture?',
        'confirm_message' => 'Voulez-vous vraiment réinitialiser ce modèle de facture aux valeurs par défaut?',
        'continue' => 'Continuer',
    ],
    'invoice_template' => [
        'title' => 'Modèle de facture',
        'description' => 'Paramètres du modèle de facture',
    ],
];
