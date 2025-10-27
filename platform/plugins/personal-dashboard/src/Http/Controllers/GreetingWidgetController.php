<?php

namespace Botble\PersonalDashboard\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Support\Facades\Auth;

class GreetingWidgetController extends BaseController
{
    public function __invoke(BaseHttpResponse $response): BaseHttpResponse
    {
        $user = Auth::guard()->user();

        return $response->setData(
            view('plugins/personal-dashboard::widgets.greeting', [
                'user' => $user,
            ])->render()
        );
    }
}
