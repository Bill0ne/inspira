<?php

namespace Botble\InspiraCancellation\Http\Controllers\Admin;

use Botble\Base\Http\Controllers\BaseController;
use Botble\InspiraCancellation\Tables\CancellationTable;
use Illuminate\Http\JsonResponse;

class CancellationController extends BaseController
{
    public function index(CancellationTable $table)
    {
        $this->pageTitle(trans('plugins/inspira-cancellation::cancellation.cancellation.list'));

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
}
