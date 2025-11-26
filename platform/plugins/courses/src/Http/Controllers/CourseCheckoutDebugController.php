<?php

namespace Botble\Courses\Http\Controllers;

use Botble\Courses\Services\CourseCheckoutDebugService;
use Botble\Courses\Services\CourseCheckoutStateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CourseCheckoutDebugController extends Controller
{
    public function state(
        Request $request,
        CourseCheckoutDebugService $debugService,
        CourseCheckoutStateService $checkoutStateService
    ): JsonResponse {
        $snapshot = $debugService->buildSnapshot($request);

        return response()->json([
            'success' => true,
            'state' => $snapshot['state'] ?? $checkoutStateService->buildState($request),
            'debug' => $snapshot['debug'] ?? [],
        ]);
    }
}
