<?php

namespace Botble\InspiraCancellation\Http\Controllers\Admin;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\InspiraCancellation\Enums\CancellationStatusEnum;
use Botble\InspiraCancellation\Events\CancellationRefundApprovedEvent;
use Botble\InspiraCancellation\Events\CancellationRefundPaidEvent;
use Botble\InspiraCancellation\Events\CancellationRejectedEvent;
use Botble\InspiraCancellation\Models\Cancellation;
use Botble\InspiraCancellation\Services\CancellationRefundService;
use Botble\InspiraCancellation\Services\CancellationService;
use Botble\InspiraCancellation\Tables\CancellationTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class CancellationController extends BaseController
{
    public function index(CancellationTable $table)
    {
        $this->pageTitle(trans('plugins/inspira-cancellation::cancellation.cancellation.list'));

        if (request()->ajax() || request()->expectsJson()) {
            return $table->ajax();
        }

        return $table->renderTable();
    }

    public function list(CancellationTable $table): JsonResponse
    {
        return $table->ajax();
    }

    public function getData(CancellationTable $table): JsonResponse
    {
        return $this->list($table);
    }

    public function approve(
        Cancellation $cancellation,
        CancellationService $cancellationService,
        BaseHttpResponse $response
    ): BaseHttpResponse
    {
        $this->ensureStatus($cancellation, [
            CancellationStatusEnum::PENDING,
            CancellationStatusEnum::PENDING_REFUND,
        ]);

        $cancellation->forceFill([
            'status' => CancellationStatusEnum::APPROVED,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ])->save();

        $cancellationService->markBookingAsCancelled($cancellation);

        event(new CancellationRefundApprovedEvent($cancellation));

        return $response->setMessage(trans('plugins/inspira-cancellation::cancellation.actions.approved_message'));
    }

    public function markPaid(Cancellation $cancellation, BaseHttpResponse $response): BaseHttpResponse
    {
        $this->ensureStatus($cancellation, [
            CancellationStatusEnum::APPROVED,
        ]);

        $cancellation->forceFill([
            'status' => CancellationStatusEnum::PAID,
            'refunded_at' => now(),
            'refunded_by' => Auth::id(),
        ])->save();

        event(new CancellationRefundPaidEvent($cancellation));

        return $response->setMessage(trans('plugins/inspira-cancellation::cancellation.actions.mark_paid_success'));
    }

    public function stripeRefund(
        Cancellation $cancellation,
        CancellationRefundService $refundService,
        BaseHttpResponse $response
    ): BaseHttpResponse {
        $this->ensureStatus($cancellation, [
            CancellationStatusEnum::APPROVED,
        ]);

        $result = $refundService->processStripeRefund($cancellation);

        if (Arr::get($result, 'error')) {
            return $response
                ->setError()
                ->setMessage(Arr::get($result, 'message'));
        }

        $cancellation->forceFill([
            'status' => CancellationStatusEnum::PAID,
            'refunded_at' => now(),
            'refunded_by' => Auth::id(),
        ])->save();

        event(new CancellationRefundPaidEvent($cancellation));

        return $response->setMessage(trans('plugins/inspira-cancellation::cancellation.actions.stripe_paid_success'));
    }

    public function reject(Cancellation $cancellation, BaseHttpResponse $response): BaseHttpResponse
    {
        $this->ensureStatus($cancellation, [
            CancellationStatusEnum::PENDING,
            CancellationStatusEnum::PENDING_REFUND,
        ]);

        $cancellation->forceFill([
            'status' => CancellationStatusEnum::REJECTED,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'refunded_at' => null,
            'refunded_by' => null,
        ])->save();

        event(new CancellationRejectedEvent($cancellation));

        return $response->setMessage(trans('plugins/inspira-cancellation::cancellation.actions.reject_success'));
    }

    protected function ensureStatus(Cancellation $cancellation, array $allowed): void
    {
        $status = $cancellation->status instanceof CancellationStatusEnum
            ? $cancellation->status->getValue()
            : $cancellation->status;

        abort_unless(in_array($status, $allowed, true), 422, trans('plugins/inspira-cancellation::cancellation.actions.invalid_state'));
    }
}
