<?php

return [
    'hotel' => 'فندق',
    'invoicing' => [
        'company_name' => 'اسم الشركة',
        'company_address' => 'عنوان الشركة',
        'company_email' => 'البريد الإلكتروني للشركة',
        'company_phone' => 'هاتف الشركة',
        'company_logo' => 'شعار الشركة',
    ],
    'using_custom_font_for_invoice' => 'استخدام خط مخصص للفاتورة؟',
    'invoice_font_family' => 'عائلة خط الفاتورة (تعمل فقط للغة اللاتينية)',
    'enable_invoice_stamp' => 'تمكين ختم الفاتورة؟',
    'invoice_support_arabic_language' => 'دعم اللغة العربية في الفاتورة؟',
    'invoice_code_prefix' => 'بادئة رمز الفاتورة',
    'invoice_settings' => 'الفاتورة',
    'invoice_settings_description' => 'إعدادات معلومات الفاتورة للفواتير',
    'general' => [
        'title' => 'عام',
        'description' => 'الإعدادات العامة للفندق',
        'enable_booking' => 'تمكين الحجز؟',
        'maximum_number_of_guests' => 'الحد الأقصى لعدد الضيوف',
        'minimum_number_of_guests' => 'الحد الأدنى لعدد الضيوف',
        'booking_number_format' => [
            'title' => 'تنسيق رقم الحجز (اختياري)',
            'description' => 'يبدأ رقم الحجز الافتراضي برقم محدد. يمكنك تخصيص أرقام البداية والنهاية لرقم الحجز. على سبيل المثال، سيتم عرض رقم الحجز على أنه #:format.',
            'start_with' => 'رقم البداية',
            'end_with' => 'رقم النهاية',
        ],
        'booking_date_format' => 'تنسيق تاريخ الحجز',
        'default_system_date_format' => 'تنسيق التاريخ الافتراضي للنظام',
        'enable_food_order' => 'تمكين طلب الطعام؟',
    ],
    'review' => [
        'title' => 'التقييمات',
        'description' => 'إعدادات التقييم للفندق',
        'enable_review_room' => 'تمكين التقييم؟',
        'reviews_per_page' => 'عدد التقييمات في الصفحة؟',
    ],
    'currency' => [
        'title' => 'العملات',
        'description' => 'قائمة العملات المستخدمة في الموقع',
    ],
    'invoice' => [
        'title' => 'الفواتير',
        'description' => 'إعدادات معلومات الفاتورة للفواتير',
        'add_language_support' => 'إضافة دعم اللغة',
        'only_latin_languages' => 'اللغات اللاتينية فقط',
        'confirm_reset' => 'تأكيد إعادة تعيين قالب الفاتورة؟',
        'confirm_message' => 'هل تريد حقًا إعادة تعيين قالب الفاتورة هذا إلى الإعدادات الافتراضية؟',
        'continue' => 'متابعة',
    ],
    'invoice_template' => [
        'title' => 'قالب الفاتورة',
        'description' => 'إعدادات قالب الفاتورة',
    ],
];
