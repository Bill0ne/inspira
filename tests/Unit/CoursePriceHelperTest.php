<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class CoursePriceHelperTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../../platform/plugins/courses/helpers/helpers.php';
    }

    public function test_it_rounds_up_when_third_decimal_is_high()
    {
        $this->assertSame(34.0, course_truncate_price(33.9983));
    }

    public function test_it_rounds_down_when_third_decimal_is_low()
    {
        $this->assertSame(33.99, course_truncate_price(33.994));
    }

    public function test_it_rounds_negative_numbers_symmetrically()
    {
        $this->assertSame(-34.0, course_truncate_price(-33.9983));
    }

    public function test_it_handles_higher_precision_net_values()
    {
        $net = 36.9731;
        $gross = course_truncate_price($net * 1.19);

        $this->assertSame(44.0, $gross);
    }
}
