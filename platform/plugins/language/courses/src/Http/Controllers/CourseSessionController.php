<?php

namespace Botble\Courses\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Tables\CourseSessionTable;

class CourseSessionController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/courses::courses.course-session.name')), route('course-session.index'));
    }

    public function index(CourseSessionTable $table)
    {
        Assets::addScriptsDirectly(['vendor/core/plugins/courses/js/script.js']);
        $this->pageTitle(trans('plugins/courses::courses.course.name'));

        return $table->renderTable();
    }

    public function getParticipants(int $id)
    {
        Assets::addScriptsDirectly(['vendor/core/plugins/courses/js/script.js']);
        $session = \Botble\Courses\Models\CourseSession::with(['bookings.customer', 'bookings.payment', 'bookings.address'])->findOrFail($id);

        $participants = $session->bookings->map(function ($booking) {

            $name = trim($booking->customer->name ?? '');

            if (empty($name) && $booking->address) {
                $name = trim(($booking->address->first_name ?? '') . ' ' . ($booking->address->last_name ?? ''));
            }

            if (empty($name)) {
                $name = '$mdash';
            }

            return [
                'name' => $name,
                'email' => $booking->customer->email ?? ($booking->address->email ?? '-'),
                'booking_date' => $booking->created_at->format('Y-m-d'),
                'payment_status' => $booking->payment?->status->toHtml(),
                'booking_id' => $booking->id,
            ];
        });

        return response()->json([
            'html' => view('plugins/courses::sessions.partials.participants-table', compact('participants'))->render(),
        ]);
    }


}
