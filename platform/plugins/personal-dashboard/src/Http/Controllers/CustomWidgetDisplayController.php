<?php

namespace Botble\PersonalDashboard\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\PersonalDashboard\Models\PersonalDashboardCustomWidget;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Throwable;

class CustomWidgetDisplayController extends BaseController
{
    public function show(string $widgetKey, BaseHttpResponse $response)
    {
        $widget = PersonalDashboardCustomWidget::query()
            ->where('key', $widgetKey)
            ->where('is_active', true)
            ->firstOrFail();

        $data = [];

        if ($handler = $widget->handler) {
            try {
                $data = App::call($handler, ['widget' => $widget]);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        $view = $widget->view_path ?: 'plugins/personal-dashboard::widgets.custom-card';

        if (! View::exists($view)) {
            $view = 'plugins/personal-dashboard::widgets.custom-card';
        }

        return $response->setData(
            view($view, [
                'widget' => $widget,
                'data' => is_array($data) ? $data : [],
            ])->render()
        );
    }
}
