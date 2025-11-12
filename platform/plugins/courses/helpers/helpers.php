<?php

use Botble\Courses\Models\Course;
use Botble\Hotel\Models\Customer;

if (! function_exists('course_truncate_price')) {
    function course_truncate_price(float $price, int $decimals = 2): float
    {
        $factor = 10 ** $decimals;

        if ($price >= 0) {
            return floor($price * $factor) / $factor;
        }

        return ceil($price * $factor) / $factor;
    }
}

if (! function_exists('course_format_price')) {
    function course_format_price(
        float $price,
        $currency = null,
        bool $withoutCurrency = false,
        bool $useSymbol = true,
        bool $fullNumber = false
    ): string {
        return format_price(
            course_truncate_price($price),
            $currency,
            $withoutCurrency,
            $useSymbol,
            $fullNumber
        );
    }
}

if (! function_exists('course_price_breakdown')) {
    function course_price_breakdown(Course $course, ?Customer $customer = null): array
    {
        $pricing = $course->resolvePricing($customer);

        $baseNetRaw = (float) ($pricing['base_net'] ?? 0);
        $calculatedNetRaw = (float) ($pricing['calculated_net'] ?? 0);

        $baseNet = course_truncate_price($baseNetRaw);
        $calculatedNet = course_truncate_price($calculatedNetRaw);

        $baseTaxRaw = $course->getTaxAmount($baseNetRaw);
        $calculatedTaxRaw = $course->getTaxAmount($calculatedNetRaw);

        $baseTax = course_truncate_price($baseTaxRaw);
        $calculatedTax = course_truncate_price($calculatedTaxRaw);

        $baseGross = course_truncate_price($baseNetRaw + $baseTaxRaw);
        $calculatedGross = course_truncate_price($calculatedNetRaw + $calculatedTaxRaw);

        $configuratorNet = course_truncate_price($calculatedNet - $baseNet);
        $configuratorTax = course_truncate_price($calculatedTax - $baseTax);
        $configuratorGross = course_truncate_price($calculatedGross - $baseGross);

        $discountNet = course_truncate_price((float) ($pricing['discount_net'] ?? 0));
        $discountGross = course_truncate_price((float) ($pricing['discount_gross'] ?? 0));

        return [
            'base_net' => $baseNet,
            'base_tax' => $baseTax,
            'base_gross' => $baseGross,
            'calculated_net' => $calculatedNet,
            'calculated_tax' => $calculatedTax,
            'calculated_gross' => $calculatedGross,
            'configurator_net' => $configuratorNet,
            'configurator_tax' => $configuratorTax,
            'configurator_gross' => $configuratorGross,
            'discount_net' => $discountNet,
            'discount_gross' => $discountGross,
            'has_discount' => $calculatedNetRaw < $baseNetRaw,
        ];
    }
}
