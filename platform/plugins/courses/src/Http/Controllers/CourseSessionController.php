<?php

namespace Botble\Courses\Http\Controllers;

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
        $this->pageTitle(trans('plugins/courses::courses.course.name'));

        return $table->renderTable();
    }

    public function getSessionBookings($sessionId)
    {
        $session = \Botble\Courses\Models\CourseSession::query()->with(['bookings.customer', 'bookings.address', 'bookings.payment'])->findOrFail($sessionId);

        return view('plugins/courses::participants', [
            'session' => $session,
            'bookings' => $session->activeBookings,
        ]);
    }

}
