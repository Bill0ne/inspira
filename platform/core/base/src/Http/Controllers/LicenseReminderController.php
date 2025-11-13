<?php

namespace Botble\Base\Http\Controllers;

use Botble\Base\Supports\Core;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LicenseReminderController extends BaseController
{
    public function index(Request $request): View
    {
        $this->pageTitle(trans('core/base::system.license.title'));

        $redirectUrl = $request->input('redirect_url', $request->headers->get('referer'));

        return view('core/base::system.unlicensed', compact('redirectUrl'));
    }

    public function skip(Request $request, Core $core): RedirectResponse
    {
        $core->skipLicenseReminder();

        $redirectUrl = $request->input('redirect_url');

        if (! $redirectUrl) {
            $redirectUrl = rescue(fn () => route('dashboard.index'), url('/'));
        }

        return redirect()->to($redirectUrl);
    }
}
