<?php

namespace Botble\InspiraCancellation\Http\Controllers\Admin;

use Botble\Base\Http\Controllers\BaseController;
use Botble\InspiraCancellation\Tables\TransferLogTable;

class TransferLogController extends BaseController
{
    public function index(TransferLogTable $table)
    {
        $this->pageTitle(trans('plugins/inspira-cancellation::cancellation.transfer.list'));

        return $table->renderTable();
    }
}
