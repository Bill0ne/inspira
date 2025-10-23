<?php

namespace Botble\Modern\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ModernController extends BaseController
{
    public function greeting(): View
    {
        $user = Auth::guard()->user();

        return view('plugins/modern::widgets.greeting', [
            'userName' => $user?->name,
        ]);
    }
}
