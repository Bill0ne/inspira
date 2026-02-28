<div class="table-responsive">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th class="text-start">{{ trans('plugins/courses::courses.course.name') }}</th>
                <td>{{ $session->course?->name ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-start">{{ trans('plugins/courses::courses.course-session.start_date') }}</th>
                <td>{{ BaseHelper::formatDateTime($session->start_date) }}</td>
            </tr>
            <tr>
                <th class="text-start">{{ trans('plugins/courses::courses.course-session.end_date') }}</th>
                <td>{{ BaseHelper::formatDateTime($session->end_date) }}</td>
            </tr>
            <tr>
                <th class="text-start">{{ trans('plugins/courses::courses.course-session.available_seats') }}</th>
                <td>{{ $session->available_seats ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-start">{{ trans('plugins/courses::courses.course-session.booked_count') }}</th>
                <td>{{ $session->booked_count ?? 0 }}</td>
            </tr>
        </tbody>
    </table>
</div>
