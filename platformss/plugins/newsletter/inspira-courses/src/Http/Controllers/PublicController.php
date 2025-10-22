<?php

namespace Botble\InspiraCourses\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Botble\InspiraCourses\Models\{Course, CourseSession, CoursePurchase};

class PublicController extends Controller
{
    public function index()
    {
        $courses = Course::with(['sessions','instructor'])->latest()->get();
        return view('inspira-courses::courses.index', compact('courses'));
    }

    public function show(string $slug)
    {
        $course = Course::where('slug', $slug)->with(['sessions','instructor'])->firstOrFail();
        return view('inspira-courses::courses.show', compact('course'));
    }

    public function postCheckout(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:insp_course_sessions,id',
            'qty' => 'required|integer|min:1',
            'payment_method' => 'nullable|string',
        ]);

        $session = CourseSession::findOrFail($request->integer('session_id'));
        $qty = (int) $request->integer('qty', 1);

        abort_if($session->remainingSeats() < $qty, 400, __('Not enough seats'));

        $amount = (float) $session->price * $qty;
        $currency = strtoupper(get_application_currency()->title);

        $purchase = CoursePurchase::create([
            'session_id' => $session->id,
            'customer_id' => optional(auth('customer')->user())->getKey(),
            'qty' => $qty,
            'amount' => $amount,
            'currency' => $currency,
            'transaction_id' => Str::upper(Str::random(32)),
            'status' => 'pending',
        ]);

        if (function_exists('is_plugin_active') && is_plugin_active('payment')) {
            $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [
                'amount'   => $amount,
                'currency' => $currency,
                'order_id' => $purchase->id,
                'description' => 'Course Ticket: ' . ($session->course->name ?? 'Course'),
                'return_url'  => route('public.courses.success', $purchase->transaction_id),
                'callback_url'=> route('public.courses.success', $purchase->transaction_id),
                'customer_id' => optional(auth('customer')->user())->getKey(),
            ], $request);

            $method = $request->input('payment_method');

            switch ($method) {
                case \Botble\Payment\Enums\PaymentMethodEnum::COD:
                    $svc = app(\Botble\Payment\Services\Gateways\CodPaymentService::class);
                    $chargeId = $svc->execute($paymentData);
                    $message = trans('plugins/payment::payment.payment_pending');
                    break;

                case \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER:
                    $svc = app(\Botble\Payment\Services\Gateways\BankTransferPaymentService::class);
                    $chargeId = $svc->execute($paymentData);
                    $message = trans('plugins/payment::payment.payment_pending');
                    break;

                default:
                    $data = apply_filters(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [
                        'amount' => $amount,
                        'currency' => $currency,
                        'type' => $method,
                        'charge_id' => null,
                        'checkoutUrl' => null,
                        'error' => false,
                        'message' => null,
                    ], $request);

                    if ($checkoutUrl = Arr::get($data, 'checkoutUrl')) {
                        return redirect()->to($checkoutUrl);
                    }

                    $chargeId = Arr::get($data, 'charge_id');
                    $message  = Arr::get($data, 'message');
                    break;
            }

            if (empty($chargeId)) {
                return redirect()->back()->with('error', $message ?: __('Checkout error!'));
            }

            $purchase->payment_id = $chargeId;
            $purchase->status = 'paid';
            $purchase->save();

            $session->increment('seats_sold', $qty);

            return redirect()->route('public.courses.success', $purchase->transaction_id)
                ->with('success', __('Booking successfully!'));
        }

        $session->increment('seats_sold', $qty);
        $purchase->status = 'paid';
        $purchase->save();

        return redirect()->route('public.courses.success', $purchase->transaction_id);
    }

    public function success(string $tx)
    {
        $purchase = CoursePurchase::where('transaction_id', $tx)
            ->with('session.course')
            ->firstOrFail();

        return view('inspira-courses::courses.success', compact('purchase'));
    }
}
