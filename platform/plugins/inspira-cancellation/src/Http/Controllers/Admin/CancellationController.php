<?php

namespace Botble\InspiraCancellation\Http\Controllers\Admin;

use Botble\Base\Http\Controllers\BaseController;
use Botble\InspiraCancellation\Tables\CancellationTable;

class CancellationController extends BaseController
{
    public function index(CancellationTable $table)
    {
        $this->pageTitle(trans('plugins/inspira-cancellation::cancellation.cancellation.list'));

        return $table->renderTable();
    }
}
