<?php

namespace Botble\Hotel\Supports;

use Botble\Hotel\Enums\ReviewStatusEnum;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Models\Room;
use Botble\PriceConfigurator\Enums\TargetTypeEnum;
use Botble\PriceConfigurator\Services\PriceConfiguratorService;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class HotelSupport
{
    public const CONTEXT_HOTEL = 'hotel';

    public const CONTEXT_COURSE = 'course';

    public function isEnableEmailVerification(): bool
    {
        return (bool) $this->getSetting('verify_customer_email', 0);
    }

    public function getSettingPrefix(): ?string
    {
        return config('plugins.hotel.general.prefix');
    }

    public function isReviewEnabled(): bool
    {
        return (bool) setting('hotel_enable_review_room', 1);
    }

    public function isBookingEnabled(): bool
    {
        return (bool) setting('hotel_enable_booking', true);
    }

    public function getReviewExtraData(): array
    {
        if (! $this->isReviewEnabled()) {
            return [];
        }

        return [
            'withCount' => [
                'reviews' => function ($query): void {
                    $query->where('status', ReviewStatusEnum::APPROVED);
                },
            ],
            'withAvg' => ['reviews', 'star'],
        ];
    }

    public function getSetting(string $key, bool|int|string|null $default = ''): array|int|string|null
    {
        return setting($this->getSettingPrefix() . $key, $default);
    }

    public function loadCountriesStatesCitiesFromPluginLocation(): bool
    {
        if (! is_plugin_active('location')) {
            return false;
        }

        return (bool) $this->getSetting('load_countries_states_cities_from_location_plugin', 0);
    }

    public function getCustomerStylesVersion(): ?string
    {
        $path = public_path('vendor/core/plugins/hotel/css/customer.css');

        if (! File::exists($path)) {
            return null;
        }

        return (string) File::lastModified($path);
    }

    public function getCustomerScriptsVersion(): ?string
    {
        $path = public_path('vendor/core/plugins/hotel/js/customer.js');

        if (! File::exists($path)) {
            return null;
        }

        return (string) File::lastModified($path);
    }

    public function viewPath(string $view): string
    {
        $themeView = Theme::getThemeNamespace() . '::views.hotel.' . $view;

        if (view()->exists($themeView)) {
            return $themeView;
        }

        return 'plugins/hotel::themes.' . $view;
    }

    public function getRoomFilters(Request|array $request): array
    {
        if ($request instanceof Request) {
            $request = $request->input();
        }

        $data = [
            'keyword' => Arr::get($request, 'q'),
            'start_date' => Arr::get($request, 'start_date'),
            'end_date' => Arr::get($request, 'end_date'),
            'adults' => Arr::get($request, 'adults', $this->getMinimumNumberOfGuests()),
            'children' => Arr::get($request, 'children', 0),
            'rooms' => Arr::get($request, 'rooms', 1),
            'page' => Arr::get($request, 'page', 1),
            'per_page' => Arr::get($request, 'per_page', 10),
            'room_category_id' => Arr::get($request, 'room_category_id'),
            'min_price' => Arr::get($request, 'min_price'),
            'max_price' => Arr::get($request, 'max_price'),
            'number_of_beds' => Arr::get($request, 'number_of_beds'),
            'min_size' => Arr::get($request, 'min_size'),
            'max_size' => Arr::get($request, 'max_size'),
            'amenities' => Arr::get($request, 'amenities'),
            'is_featured' => Arr::get($request, 'is_featured'),
            'sort_by' => Arr::get($request, 'sort_by'),
            'sort_direction' => Arr::get($request, 'sort_direction', 'asc'),
        ];

        $dateFormat = HotelHelper::getDateFormat();

        try {
            $validator = Validator::make($data, [
                'q' => ['nullable', 'string'],
                'keyword' => ['nullable', 'string'],
                'adults' => [
                    'nullable',
                    'int',
                    'min:' . $this->getMinimumNumberOfGuests(),
                    'max:' . $this->getMaximumNumberOfGuests(),
                ],
                'children' => ['nullable', 'int', 'min:0'],
                'rooms' => ['nullable', 'int', 'min:1'],
                'page' => ['nullable', 'int', 'min:1'],
                'per_page' => ['nullable', 'int', 'min:1'],
                'room_category_id' => ['nullable', 'int', 'exists:ht_room_categories,id'],
                'start_date' => ['nullable', 'string', 'date', 'date_format:' . $dateFormat, 'after_or_equal:today'],
                'end_date' => ['nullable', 'string', 'date', 'date_format:' . $dateFormat, 'after_or_equal:start_date'],
                'room_id' => ['nullable', 'integer', 'exists:hotel_rooms,id'],
                'min_price' => ['nullable', 'numeric', 'min:0'],
                'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
                'number_of_beds' => ['nullable', 'integer', 'min:1'],
                'min_size' => ['nullable', 'numeric', 'min:0'],
                'max_size' => ['nullable', 'numeric', 'min:0', 'gte:min_size'],
                'amenities' => ['nullable', 'array'],
                'amenities.*' => ['integer', 'exists:ht_amenities,id'],
                'is_featured' => ['nullable', 'boolean'],
                'sort_by' => ['nullable', 'string', 'in:price,name,created_at,number_of_beds,size'],
                'sort_direction' => ['nullable', 'string', 'in:asc,desc'],
            ]);

            return $validator->valid();
        } catch (Throwable) {
            return [];
        }
    }

    public function getRoomBookingParams(): array
    {
        $request = request();

        try {
            if ($request->input('start_date') && $request->input('end_date')) {
                $startDate = $this->dateFromRequest($request->input('start_date'));
                $endDate = $this->dateFromRequest($request->input('end_date'));
            } else {
                $startDate = Carbon::now();
                $endDate = (clone $startDate)->addDay();
            }
        } catch (Throwable) {
            $startDate = Carbon::now();
            $endDate = (clone $startDate)->addDay();
        }

        $adults = $request->input('adults', $this->getMinimumNumberOfGuests());
        $children = $request->input('children', 0);
        $rooms = $request->input('rooms', 1);

        $nights = $endDate->diffInHours($startDate);

        return [
            $startDate,
            $endDate,
            $adults,
            $nights,
            $children,
            $rooms,
        ];
    }

    public function getCheckoutData(?string $key = null, string $context = self::CONTEXT_HOTEL): mixed
    {
        $checkoutToken = $this->getCheckoutToken($context);

        if (! $checkoutToken) {
            $checkoutToken = Str::upper(Str::random(32));
            $this->setCheckoutToken($context, $checkoutToken);
        }

        $sessionData = [];

        if ($checkoutToken && session()->has($checkoutToken)) {
            $sessionData = session($checkoutToken);
        }

        if ($key) {
            return $sessionData[$key] ?? null;
        }

        return $sessionData;
    }

    public function saveCheckoutData(array $data, string $context = self::CONTEXT_HOTEL): void
    {
        $checkoutToken = $this->getCheckoutToken($context);

        if (! $checkoutToken) {
            $checkoutToken = Str::upper(Str::random(32));
        }

        $this->setCheckoutToken($context, $checkoutToken);

        $sessionData = $this->getCheckoutData(null, $context);

        $data = array_merge($sessionData, $data);

        session()->put($checkoutToken, $data);
    }

    public function clearCheckoutData(?string $context = null): void
    {
        $contexts = $context ? [$context] : [self::CONTEXT_HOTEL, self::CONTEXT_COURSE];

        foreach ($contexts as $ctx) {
            $tokenKey = $this->resolveCheckoutTokenKey($ctx);
            $token = session($tokenKey);

            if ($token && session()->has($token)) {
                session()->forget($token);
            }

            if ($tokenKey) {
                session()->forget($tokenKey);
            }

            if (session('checkout_context') === $ctx) {
                session()->forget('checkout_token');
                session()->forget('checkout_context');
            }
        }

        if (! $context) {
            session()->forget('checkout_token');
            session()->forget('checkout_context');
        }
    }

    protected function getCheckoutToken(string $context): ?string
    {
        $tokenKey = $this->resolveCheckoutTokenKey($context);
        $token = $tokenKey ? session($tokenKey) : null;

        if ($token) {
            return $token;
        }

        if (session('checkout_context') === $context) {
            return session('checkout_token');
        }

        return null;
    }

    protected function setCheckoutToken(string $context, string $token): void
    {
        $tokenKey = $this->resolveCheckoutTokenKey($context);

        if ($tokenKey) {
            session()->put($tokenKey, $token);
        }

        session()->put('checkout_token', $token);
        session()->put('checkout_context', $context);
    }

    protected function resolveCheckoutTokenKey(string $context): ?string
    {
        return match ($context) {
            self::CONTEXT_COURSE => 'course_checkout_token',
            self::CONTEXT_HOTEL => 'hotel_checkout_token',
            default => null,
        };
    }

    public function getDateFormat(): string
    {
        return (setting('hotel_booking_datetime_format') ?: config('plugins.hotel.hotel.datetime_format')) ?: 'd.m.Y H:i';
    }

    public function getBookingFormDateFormat(): string
    {
        return ($this->getDateFormatDatepicker() ?: config('plugins.hotel.hotel.booking_form_date_format')) ?: 'd.m.Y H:i';
    }

    public function dateFromRequest(string $date): Carbon|false
    {
        return Carbon::createFromFormat($this->getDateFormat(), $date);
    }

    public function getDateRangeInReport(Request $request): array
    {
        $startDate = Carbon::now()->subDays(29);
        $endDate = Carbon::now();

        if ($request->input('date_from')) {
            try {
                $startDate = Carbon::now()->createFromFormat('Y-m-d', $request->input('date_from'));
            } catch (Exception) {
                $startDate = Carbon::now()->subDays(29);
            }
        }

        if ($request->input('date_to')) {
            try {
                $endDate = Carbon::now()->createFromFormat('Y-m-d', $request->input('date_to'));
            } catch (Exception) {
                $endDate = Carbon::now();
            }
        }

        if ($endDate->gt(Carbon::now())) {
            $endDate = Carbon::now();
        }

        if ($startDate->gt($endDate)) {
            $startDate = Carbon::now()->subDays(29);
        }

        $predefinedRange = $request->input('predefined_range', trans('plugins/hotel::booking-report.ranges.last_30_days'));

        return [$startDate, $endDate, $predefinedRange];
    }

    public function getMinimumNumberOfGuests(): int
    {
        return (int) setting('hotel_minimum_number_of_guests', 1);
    }

    public function getMaximumNumberOfGuests(): int
    {
        return (int) setting('hotel_maximum_number_of_guests', 10);
    }

    public function getBookingNumber(string|int $id): string
    {
        $prefix = setting('hotel_booking_number_prefix') ? setting('hotel_booking_number_prefix') . '-' : '';
        $suffix = setting('hotel_booking_number_suffix') ? '-' . setting('hotel_booking_number_suffix') : '';

        return sprintf(
            '#%s%d%s',
            $prefix,
            (int) config('plugins.hotel.hotel.default_number_start_number') + $id,
            $suffix
        );
    }

    public function getBookingDateFormatTemplates(): array
    {
        return [
            [
                'carbon' => 'd-m-Y',
                'datepicker' => 'd.m.Y H:i',
            ],
            [
                'carbon' => 'm-d-Y',
                'datepicker' => 'mm-dd-yyyy hh:ii AA',
            ],
            [
                'carbon' => 'Y-m-d',
                'datepicker' => 'yyyy-mm-dd hh:ii AA',
            ],
            [
                'carbon' => 'd/m/Y',
                'datepicker' => 'dd/mm/yyyy hh:ii AA',
            ],
            [
                'carbon' => 'm/d/Y',
                'datepicker' => 'mm/dd/yyyy hh:ii AA',
            ],
            [
                'carbon' => 'Y/m/d',
                'datepicker' => 'yyyy/mm/dd hh:ii AA',
            ],
        ];
    }


    public function getBookingDateFormatOptions(): array
    {
        $templates = $this->getBookingDateFormatTemplates();

        $options = [];

        $now = Carbon::now();

        $options[''] = trans('plugins/hotel::settings.general.default_system_date_format');

        foreach ($templates as $template) {
            $options[$template['carbon']] = $template['carbon'] . ' (' . $now->format($template['carbon']) . ')';
        }

        return $options;
    }

    public function getDateFormatDatepicker(): ?string
    {
        $dateFormat = $this->getDateFormat();
        $templates = $this->getBookingDateFormatTemplates();

        foreach ($templates as $template) {
            if ($template['carbon'] == $dateFormat) {
                return $template['datepicker'];
            }
        }

        return null;
    }

    public function isEnableFoodOrder(): bool
    {
        return (bool) $this->getSetting('hotel_booking_enabled_food_order', false);
    }

    public function canShowRoomPrices(?Customer $customer = null): bool
    {
        $customer ??= $this->getCurrentCustomer();

        if (! $customer) {
            return false;
        }

        return (bool) $customer->customer_category_id;
    }

    public function getRoomPriceInquiryText(): string
    {
        $contactUrl = 'https://inspira-zentrum.net/de/nimm-kontakt-mit-uns-auf';

        return 'Preise <a href="' . e($contactUrl) . '">hier</a> abfragen';
    }

    public function getRoomConfiguredPrice(Room $room, ?Customer $customer = null, int $quantity = 1): float
    {
        $basePrice = (float) $room->price;

        if (! function_exists('is_plugin_active') || ! is_plugin_active('price-configurator')) {
            return $basePrice;
        }

        if (! $room->getKey()) {
            return $basePrice;
        }

        $customer ??= $this->getCurrentCustomer();
        $quantity = max($quantity, 1);

        try {
            return app(PriceConfiguratorService::class)->calculatePrice(
                $basePrice,
                TargetTypeEnum::ROOM,
                (int) $room->getKey(),
                $customer,
                $quantity
            );
        } catch (Throwable) {
            return $basePrice;
        }
    }

    public function getCurrentCustomer(): ?\Botble\Hotel\Models\Customer
    {
        return \Auth::guard('customer')->user();
    }
}
