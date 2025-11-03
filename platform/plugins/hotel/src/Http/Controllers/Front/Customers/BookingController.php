<?php

namespace Botble\Hotel\Http\Controllers\Front\Customers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Facades\InvoiceHelper;
use Botble\Hotel\Models\Booking;
use Botble\Hotel\Models\Invoice;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingController extends BaseController
{
    public function __construct()
    {
        $customerCssVersion = HotelHelper::getCustomerStylesVersion();
        $customerScriptVersion = HotelHelper::getCustomerScriptsVersion();

        Theme::asset()
            ->add('customer-style', 'vendor/core/plugins/hotel/css/customer.css', [], [], $customerCssVersion);

        Theme::asset()
            ->container('footer')
            ->add('customer-js', 'vendor/core/plugins/hotel/js/customer.js', ['jquery'], [], $customerScriptVersion)
            ->add('utilities-js', 'vendor/core/plugins/hotel/js/utilities.js', ['jquery'])
            ->add('cropper-js', 'vendor/core/core/base/libraries/cropper.min.js', ['jquery'])
            ->add('avatar-js', 'vendor/core/plugins/hotel/js/avatar.js', ['jquery']);
    }

    public function index(Request $request)
    {
        SeoHelper::setTitle(__('Bookings'));

        $customerId = auth('customer')->id();

        $roomBookings = Booking::query()
            ->where('customer_id', $customerId)
            ->with(['room.room', 'invoice'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($booking) => [
                'type' => 'room',
                'model' => $booking,
                'created_at' => $booking->created_at,
            ]);

        $combinedBookings = $roomBookings;

        if (is_plugin_active('courses') && class_exists(\Botble\Courses\Models\CourseBooking::class)) {
            $courseBookings = \Botble\Courses\Models\CourseBooking::query()
                ->where('customer_id', $customerId)
                ->with([
                    'course' => function ($query) {
                        $query->with(['instructor', 'category']);
                    },
                    'session',
                    'invoice',
                ])
                ->orderByDesc('created_at')
                ->get()
                ->map(fn ($booking) => [
                    'type' => 'course',
                    'model' => $booking,
                    'created_at' => $booking->created_at,
                ]);

            $combinedBookings = $combinedBookings->merge($courseBookings);
        }

        $combinedBookings = $combinedBookings
            ->sortByDesc('created_at')
            ->values();

        $perPage = 5;
        $currentPage = max((int) $request->input('page', 1), 1);

        $bookings = new LengthAwarePaginator(
            $combinedBookings->forPage($currentPage, $perPage)->values(),
            $combinedBookings->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        Theme::breadcrumb()
            ->add(__('Bookings'), route('customer.bookings'));

        return Theme::scope(
            'hotel.customers.bookings.list',
            compact('bookings'),
            'plugins/hotel::themes.customers.bookings.list'
        )->render();
    }

    public function show(int|string $id)
    {
        $booking = Booking::query()
            ->with('invoice')
            ->where([
                'transaction_id' => $id,
                'customer_id' => auth('customer')->id(),
            ])
            ->firstOrFail();

        SeoHelper::setTitle(__('Booking Information'));

        Theme::breadcrumb()
            ->add(
                __('Booking Information'),
                route('customer.bookings.show', $id)
            );

        return Theme::scope(
            'hotel.customers.bookings.detail',
            compact('booking'),
            'plugins/hotel::themes.customers.bookings.detail'
        )->render();
    }

    public function getGenerateInvoice(int|string $invoiceId, Request $request)
    {
        $invoice = Invoice::query()->findOrFail($invoiceId);

        abort_unless($this->canViewInvoice($invoice), 404);

        if ($request->input('type') === 'print') {
            return InvoiceHelper::streamInvoice($invoice);
        }

        return InvoiceHelper::downloadInvoice($invoice);
    }

    protected function canViewInvoice(Invoice $invoice): bool
    {
        return auth('customer')->id() == $invoice->payment->customer_id;
    }
}
