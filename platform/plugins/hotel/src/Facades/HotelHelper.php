<?php

namespace Botble\Hotel\Facades;

use Botble\Hotel\Supports\HotelSupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isEnableEmailVerification()
 * @method static string|null getSettingPrefix()
 * @method static bool isReviewEnabled()
 * @method static bool isBookingEnabled()
 * @method static array getReviewExtraData()
 * @method static array|string|int|null getSetting(string $key, string|int|bool|null $default = '')
 * @method static bool loadCountriesStatesCitiesFromPluginLocation()
 * @method static string|null getCustomerStylesVersion()
 * @method static string|null getCustomerScriptsVersion()
 * @method static string viewPath(string $view)
 * @method static array getRoomFilters(\Illuminate\Http\Request|array $request)
 * @method static array getRoomBookingParams()
 * @method static mixed|null getCheckoutData(string|null $key = null, string $context = \Botble\Hotel\Supports\HotelSupport::CONTEXT_HOTEL)
 * @method static void saveCheckoutData(array $data, string $context = \Botble\Hotel\Supports\HotelSupport::CONTEXT_HOTEL)
 * @method static void clearCheckoutData(string $context = \Botble\Hotel\Supports\HotelSupport::CONTEXT_HOTEL)
 * @method static string getDateFormat()
 * @method static string getBookingFormDateFormat()
 * @method static \Carbon\Carbon|false dateFromRequest(string $date)
 * @method static array getDateRangeInReport(\Illuminate\Http\Request $request)
 * @method static int getMinimumNumberOfGuests()
 * @method static int getMaximumNumberOfGuests()
 * @method static string getBookingNumber(string|int $id)
 * @method static array getBookingDateFormatOptions()
 * @method static bool isEnableFoodOrder()
 * @method static bool canShowRoomPrices(?\Botble\Hotel\Models\Customer $customer = null)
 * @method static string getRoomPriceInquiryText()
 * @method static float getRoomConfiguredPrice(\Botble\Hotel\Models\Room $room, \Botble\Hotel\Models\Customer|null $customer = null, int $quantity = 1)
 * @method static \Botble\Hotel\Models\Customer | null getCurrentCustomer()
 * @see \Botble\Hotel\Supports\HotelSupport
 */
class HotelHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HotelSupport::class;
    }
}
