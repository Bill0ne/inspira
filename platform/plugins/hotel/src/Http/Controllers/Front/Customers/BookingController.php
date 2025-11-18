<?php

namespace Botble\Hotel\Http\Controllers\Front\Customers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Hotel\Facades\HotelHelper;
use Botble\Hotel\Facades\InvoiceHelper;
use Botble\Hotel\Models\Booking;
use Botble\Hotel\Models\Invoice;
use Botble\InspiraCancellation\Enums\CancellationStatusEnum;
use Botble\InspiraCancellation\Enums\TransferStatusEnum;
use Botble\InspiraCancellation\Models\Cancellation;
use Botble\InspiraCancellation\Models\TransferLog;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

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
            ->with(['room.room', 'invoice', 'payment'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($booking) => [
                'type' => 'room',
                'model' => $booking,
                'created_at' => $booking->created_at,
            ])
            ->values()
            ->toBase();

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
                    'payment',
                ])
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($booking) {
                    $invoiceRelation = $booking->invoice;

                    if (is_array($invoiceRelation)) {
                        $invoiceId = data_get($invoiceRelation, 'id');

                        $booking->setRelation('invoice', $invoiceId ? Invoice::query()->find($invoiceId) : null);
                    }

                    return [
                        'type' => 'course',
                        'model' => $booking,
                        'created_at' => $booking->created_at,
                    ];
                })
                ->values()
                ->toBase();

            $combinedBookings = $combinedBookings->merge($courseBookings);
        }

        $combinedBookings = $combinedBookings
            ->sortByDesc('created_at')
            ->values();

        $perPage = 5;
        $currentPage = max((int) $request->input('page', 1), 1);

        $currentItems = $combinedBookings->forPage($currentPage, $perPage)->values();
        $meta = $this->buildBookingMeta($currentItems);

        $currentItems = $currentItems
            ->map(function ($item) use ($meta) {
                if (! isset($item['type'], $item['model'])) {
                    return $item;
                }

                $key = $this->getBookingKey($item['type'], $item['model']->getKey());
                $item['meta'] = $meta[$key] ?? [];

                return $item;
            })
            ->values();

        $bookings = new LengthAwarePaginator(
            $currentItems,
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

    protected function buildBookingMeta(Collection $bookings): array
    {
        if ($bookings->isEmpty()) {
            return [];
        }

        $cancellations = class_exists(Cancellation::class)
            ? $this->mapCancellations($bookings)
            : [];

        $transfers = class_exists(TransferLog::class)
            ? $this->mapTransfers($bookings)
            : [];

        $meta = [];

        foreach ($bookings as $item) {
            if (! isset($item['type'], $item['model'])) {
                continue;
            }

            $type = $item['type'];
            $model = $item['model'];
            $key = $this->getBookingKey($type, $model->getKey());

            $meta[$key] = [
                'status' => $this->determineStatusMeta(
                    $type,
                    $model,
                    $cancellations[$key] ?? null,
                    $transfers[$key] ?? null
                ),
                'actions' => $this->determineActionPermissions(
                    $type,
                    $model,
                    $cancellations[$key] ?? null
                ),
            ];
        }

        return $meta;
    }

    protected function mapCancellations(Collection $bookings): array
    {
        $map = [];

        $grouped = $bookings->groupBy('type');

        foreach ($grouped as $type => $entries) {
            $ids = $entries
                ->map(fn ($item) => $item['model']->getKey())
                ->filter()
                ->unique()
                ->values();

            if ($ids->isEmpty()) {
                continue;
            }

            Cancellation::query()
                ->where('booking_type', $type)
                ->whereIn('booking_id', $ids)
                ->orderByDesc('id')
                ->get()
                ->each(function (Cancellation $cancellation) use (&$map, $type): void {
                    $key = $this->getBookingKey($type, $cancellation->booking_id);

                    if (! array_key_exists($key, $map)) {
                        $map[$key] = $cancellation;
                    }
                });
        }

        return $map;
    }

    protected function mapTransfers(Collection $bookings): array
    {
        $courseEntries = $bookings->where('type', 'course');

        if ($courseEntries->isEmpty()) {
            return [];
        }

        $ids = $courseEntries
            ->map(fn ($item) => $item['model']->getKey())
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $map = [];

        TransferLog::query()
            ->where('booking_type', 'course')
            ->whereIn('booking_id', $ids)
            ->orderByDesc('id')
            ->get()
            ->each(function (TransferLog $log) use (&$map): void {
                $key = $this->getBookingKey($log->booking_type, $log->booking_id);

                if (! array_key_exists($key, $map)) {
                    $map[$key] = $log;
                }
            });

        return $map;
    }

    protected function determineStatusMeta(string $type, Model $booking, ?Cancellation $cancellation, ?TransferLog $transfer): array
    {
        if ($cancellation && $this->hasActiveCancellation($cancellation)) {
            $status = $this->getCancellationStatusValue($cancellation);

            if (in_array($status, [CancellationStatusEnum::PENDING, CancellationStatusEnum::PENDING_REFUND], true)) {
                return $this->formatStatus(__('Stornierung beantragt'), 'stornierung-beantragt');
            }

            if ($status === CancellationStatusEnum::APPROVED) {
                return $this->formatStatus(__('Stornierung freigegeben'), 'stornierung-freigegeben');
            }

            if ($status === CancellationStatusEnum::PAID) {
                return $this->formatStatus(__('Storniert'), 'storniert');
            }
        }

        if ($transfer && $type === 'course') {
            $transferStatus = $this->getTransferStatusValue($transfer);

            if ($transferStatus === TransferStatusEnum::PENDING) {
                return $this->formatStatus(__('Übertragung angefragt'), 'uebertragung-angefragt');
            }

            if ($transferStatus === TransferStatusEnum::APPROVED) {
                return $this->formatStatus(__('Übertragen'), 'uebertragen');
            }
        }

        if ($this->hasBookingExpired($type, $booking)) {
            return $this->formatStatus(__('Abgelaufen'), 'abgelaufen');
        }

        if ($this->isBookingCancelled($booking)) {
            return $this->formatStatus(__('Storniert'), 'storniert');
        }

        if ($this->isAwaitingPayment($booking)) {
            return $this->formatStatus(__('Warten auf Zahlung'), 'warten-auf-zahlung');
        }

        if ($this->isBookingPaid($booking)) {
            return $this->formatStatus(__('Gebucht'), 'gebucht');
        }

        $status = $booking->status instanceof BookingStatusEnum
            ? $booking->status->getValue()
            : (string) $booking->status;

        $label = $booking->status instanceof BookingStatusEnum
            ? $booking->status->label()
            : $status;

        return $this->formatStatus($label, $status ?: 'status');
    }

    protected function determineActionPermissions(string $type, Model $booking, ?Cancellation $cancellation): array
    {
        $isPaid = $this->isBookingPaid($booking);
        $isExpired = $this->hasBookingExpired($type, $booking);
        $hasCancellation = $this->hasActiveCancellation($cancellation) || $this->isBookingCancelled($booking);

        $canCancel = $isPaid && ! $isExpired && ! $hasCancellation;
        $canTransfer = $canCancel && $type === 'course';

        return compact('canCancel', 'canTransfer');
    }

    protected function hasActiveCancellation(?Cancellation $cancellation): bool
    {
        if (! $cancellation) {
            return false;
        }

        return in_array(
            $this->getCancellationStatusValue($cancellation),
            [
                CancellationStatusEnum::PENDING,
                CancellationStatusEnum::PENDING_REFUND,
                CancellationStatusEnum::APPROVED,
                CancellationStatusEnum::PAID,
            ],
            true
        );
    }

    protected function getCancellationStatusValue(Cancellation $cancellation): string
    {
        $status = $cancellation->status;

        return $status instanceof CancellationStatusEnum ? $status->getValue() : (string) $status;
    }

    protected function getTransferStatusValue(TransferLog $log): string
    {
        $status = $log->status;

        return $status instanceof TransferStatusEnum ? $status->getValue() : (string) $status;
    }

    protected function hasBookingExpired(string $type, Model $booking): bool
    {
        $eventDate = $this->resolveEventDate($type, $booking);

        return $eventDate ? $eventDate->isPast() : false;
    }

    protected function resolveEventDate(string $type, Model $booking): ?Carbon
    {
        try {
            if ($type === 'course') {
                $session = $booking->session ?? null;
                $date = $session?->end_date ?: $session?->start_date;
            } else {
                $room = $booking->room ?? null;
                $date = $room?->end_date ?: $room?->start_date;
            }

            return $date ? Carbon::parse($date) : null;
        } catch (Throwable) {
            return null;
        }
    }

    protected function isBookingCancelled(Model $booking): bool
    {
        return $this->getBookingStatusValue($booking) === BookingStatusEnum::CANCELLED;
    }

    protected function isAwaitingPayment(Model $booking): bool
    {
        if ($this->isBookingPaid($booking)) {
            return false;
        }

        return in_array(
            $this->getBookingStatusValue($booking),
            [
                BookingStatusEnum::AWAITING_PAYMENT,
                BookingStatusEnum::PENDING,
            ],
            true
        );
    }

    protected function isBookingPaid(Model $booking): bool
    {
        $paymentStatus = $this->resolvePaymentStatus($booking);

        if ($paymentStatus) {
            return in_array(
                $paymentStatus,
                [
                    PaymentStatusEnum::COMPLETED,
                    PaymentStatusEnum::REFUNDING,
                    PaymentStatusEnum::REFUNDED,
                ],
                true
            );
        }

        return in_array(
            $this->getBookingStatusValue($booking),
            [
                BookingStatusEnum::COMPLETED,
                BookingStatusEnum::PROCESSING,
            ],
            true
        );
    }

    protected function resolvePaymentStatus(Model $booking): ?string
    {
        $payment = $booking->relationLoaded('payment') ? $booking->getRelation('payment') : ($booking->payment ?? null);

        if (! $payment || ! $payment->getKey()) {
            return null;
        }

        $status = $payment->status;

        return $status instanceof PaymentStatusEnum ? $status->getValue() : ($status ?: null);
    }

    protected function getBookingStatusValue(Model $booking): string
    {
        $status = $booking->status;

        return $status instanceof BookingStatusEnum ? $status->getValue() : (string) $status;
    }

    protected function formatStatus(string $label, string $slug): array
    {
        return [
            'label' => $label,
            'slug' => Str::slug($slug ?: 'status'),
        ];
    }

    protected function getBookingKey(string $type, int|string $id): string
    {
        return sprintf('%s:%s', $type, $id);
    }
}
