<?php

return [
    'hotel' => 'Hotel',
    'invoicing' => [
        'company_name' => 'Nombre de la empresa',
        'company_address' => 'Dirección de la empresa',
        'company_email' => 'Correo electrónico de la empresa',
        'company_phone' => 'Teléfono de la empresa',
        'company_logo' => 'Logo de la empresa',
    ],
    'using_custom_font_for_invoice' => '¿Usar fuente personalizada para la factura?',
    'invoice_font_family' => 'Familia de fuente para factura (Solo funciona para idiomas latinos)',
    'enable_invoice_stamp' => '¿Habilitar sello de factura?',
    'invoice_support_arabic_language' => '¿Soporte para idioma árabe en la factura?',
    'invoice_code_prefix' => 'Prefijo de código de factura',
    'invoice_settings' => 'Factura',
    'invoice_settings_description' => 'Configuración de información de factura para facturación',
    'general' => [
        'title' => 'General',
        'description' => 'Configuración general para Hotel',
        'enable_booking' => '¿Habilitar reservas?',
        'maximum_number_of_guests' => 'Número máximo de huéspedes',
        'minimum_number_of_guests' => 'Número mínimo de huéspedes',
        'booking_number_format' => [
            'title' => 'Formato de número de reserva (opcional)',
            'description' => 'El número de reserva predeterminado comienza en un número específico. Puede personalizar los números de inicio y fin para el número de reserva. Por ejemplo, el número de reserva se mostrará como #:format.',
            'start_with' => 'Número de inicio',
            'end_with' => 'Número de fin',
        ],
        'booking_date_format' => 'Formato de fecha de reserva',
        'default_system_date_format' => 'Formato de fecha predeterminado del sistema',
        'enable_food_order' => '¿Habilitar pedido de comida?',
    ],
    'review' => [
        'title' => 'Reseñas',
        'description' => 'Configuración de reseñas para Hotel',
        'enable_review_room' => '¿Habilitar reseñas?',
        'reviews_per_page' => '¿Número de reseñas por página?',
    ],
    'currency' => [
        'title' => 'Monedas',
        'description' => 'Lista de monedas utilizadas en el sitio web',
    ],
    'invoice' => [
        'title' => 'Facturas',
        'description' => 'Configuración de información de factura para facturación',
        'add_language_support' => 'Añadir soporte de idioma',
        'only_latin_languages' => 'Solo idiomas latinos',
        'confirm_reset' => '¿Confirmar reinicio de plantilla de factura?',
        'confirm_message' => '¿Realmente desea restablecer esta plantilla de factura a los valores predeterminados?',
        'continue' => 'Continuar',
    ],
    'invoice_template' => [
        'title' => 'Plantilla de factura',
        'description' => 'Configuración para plantilla de factura',
    ],
];
