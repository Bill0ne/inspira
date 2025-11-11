<?php

namespace Botble\InspiraCancellation\Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\InspiraCancellation\Models\CancellationRule;

class InspiraCancellationSeeder extends BaseSeeder
{
    public function run(): void
    {
        CancellationRule::query()->truncate();

        $courseRules = [
            [
                'type' => 'course',
                'from_days' => 7,
                'to_days' => null,
                'refund_percent' => 100,
                'description' => 'Kostenfreie Stornierung bis 7 Tage vor Kursbeginn.',
                'order' => 1,
            ],
            [
                'type' => 'course',
                'from_days' => 3,
                'to_days' => 6,
                'refund_percent' => 50,
                'description' => 'Bei Stornierung 6–3 Tage vor Beginn werden 50 % berechnet.',
                'order' => 2,
            ],
            [
                'type' => 'course',
                'from_days' => null,
                'to_days' => 2,
                'refund_percent' => 0,
                'description' => 'Ab 2 Tagen vor Beginn ist keine Rückerstattung möglich.',
                'order' => 3,
            ],
        ];

        $roomRules = [
            [
                'type' => 'room',
                'from_days' => 45,
                'to_days' => null,
                'refund_percent' => 100,
                'description' => 'Bis 45 Tage vor Termin kostenfrei stornierbar.',
                'order' => 1,
            ],
            [
                'type' => 'room',
                'from_days' => 30,
                'to_days' => 44,
                'refund_percent' => 50,
                'description' => '44–30 Tage vor Termin werden 50 % berechnet.',
                'order' => 2,
            ],
            [
                'type' => 'room',
                'from_days' => 15,
                'to_days' => 29,
                'refund_percent' => 30,
                'description' => '29–15 Tage vor Termin werden 70 % berechnet.',
                'order' => 3,
            ],
            [
                'type' => 'room',
                'from_days' => null,
                'to_days' => 14,
                'refund_percent' => 0,
                'description' => 'Ab 14 Tagen vor Termin ist keine Erstattung möglich.',
                'order' => 4,
            ],
        ];

        foreach (array_merge($courseRules, $roomRules) as $index => $rule) {
            CancellationRule::query()->create(array_merge($rule, [
                'active' => true,
                'order' => $index + 1,
            ]));
        }
    }
}
