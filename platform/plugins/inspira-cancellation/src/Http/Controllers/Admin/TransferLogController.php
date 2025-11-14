<?php

namespace Botble\InspiraCancellation\Http\Controllers\Admin;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\InspiraCancellation\Enums\TransferStatusEnum;
use Botble\InspiraCancellation\Models\TransferLog;
use Botble\InspiraCancellation\Services\TransferService;
use Botble\InspiraCancellation\Tables\TransferLogTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransferLogController extends BaseController
{
    public function index(TransferLogTable $table)
    {
        $this->pageTitle(trans('plugins/inspira-cancellation::cancellation.transfer.list'));

        return $table->renderTable();
    }

    public function list(TransferLogTable $table): JsonResponse
    {
        return $table->ajax();
    }

    public function getData(TransferLogTable $table): JsonResponse
    {
        return $this->list($table);
    }

    public function approve(TransferLog $transfer, TransferService $service, BaseHttpResponse $response): BaseHttpResponse
    {
        $this->ensurePending($transfer);

        $service->approve($transfer, Auth::id());

        return $response->setMessage(trans('plugins/inspira-cancellation::cancellation.transfer.actions.approve_success'));
    }

    public function reject(TransferLog $transfer, TransferService $service, BaseHttpResponse $response): BaseHttpResponse
    {
        $this->ensurePending($transfer);

        $service->reject($transfer, Auth::id());

        return $response->setMessage(trans('plugins/inspira-cancellation::cancellation.transfer.actions.reject_success'));
    }

    protected function ensurePending(TransferLog $transfer): void
    {
        $status = $transfer->status instanceof TransferStatusEnum
            ? $transfer->status->getValue()
            : $transfer->status;

        abort_unless($status === TransferStatusEnum::PENDING, 422, trans('plugins/inspira-cancellation::cancellation.transfer.actions.invalid_state'));
    }
}
