<?php

namespace Botble\InspiraCancellation\Http\Controllers\Admin;

use Botble\Base\Http\Controllers\BaseController;
use Botble\InspiraCancellation\Tables\TransferLogTable;
use Illuminate\Http\JsonResponse;

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
}
