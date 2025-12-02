@php
    $decimalSeparator = $currencyFormat['decimal_separator'] ?? '.';
    $thousandSeparator = $currencyFormat['thousand_separator'] ?? ',';
    $taxDisplay = number_format($selectedTaxPercentage ?? 0, 2, $decimalSeparator, $thousandSeparator);
@endphp
<div class="text-muted small course-price-helper" data-tax-rates='@json($taxRates)' data-currency='@json($currencyFormat)' data-price-field="#price" data-tax-field="#tax_id">
    <div><strong>{{ __('Mehrwertsteuer:') }}</strong> <span data-course-price-tax>{{ $taxDisplay }}%</span></div>
    <div><strong>{{ __('Voraussichtlicher Bruttopreis:') }}</strong> <span data-course-price-gross>{{ $grossPreview }}</span></div>
</div>
