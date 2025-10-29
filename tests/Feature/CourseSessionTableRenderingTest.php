<?php

namespace Tests\Feature;

use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseSession;
use Botble\Courses\Services\CoursePerformanceService;
use Botble\Courses\Tables\CourseSessionTable;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class CourseSessionTableRenderingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        App::setLocale('de');

        $stub = new FakeCoursePerformanceService([
            1 => [
                'views' => 1280,
                'views_source' => 'analytics',
                'analytics_days' => 30,
                'booked_seats' => 6,
                'remaining_seats' => 4,
                'max_seats' => 10,
                'engaged_participants' => 4,
                'engagement_percent' => 66.7,
                'conversion_percent' => 42.5,
                'score' => 84.2,
            ],
            2 => [
                'views' => 0,
                'views_source' => 'none',
                'analytics_days' => 30,
                'booked_seats' => 0,
                'remaining_seats' => null,
                'max_seats' => null,
                'engaged_participants' => 0,
                'engagement_percent' => 0.0,
                'conversion_percent' => 0.0,
                'score' => 0.0,
            ],
        ], 1500, 30);

        App::instance(CoursePerformanceService::class, $stub);
    }

    public function test_card_renders_compact_overview(): void
    {
        $table = App::make(CourseSessionTable::class);
        $session = $this->makeSession(1);

        $html = $this->invokeProtected($table, 'renderSessionOverview', [$session]);

        $this->assertStringContainsString('course-session-card', $html);
        $this->assertStringContainsString('course-session-title', $html);
        $this->assertStringContainsString('Musik', $html);
        $this->assertStringContainsString('Andrea Schulz', $html);
        $this->assertStringContainsString('course-session-thumbnail--fallback', $html);
    }

    public function test_views_badge_displays_placeholder_when_missing(): void
    {
        $table = App::make(CourseSessionTable::class);
        $session = $this->makeSession(2);

        $html = $this->invokeProtected($table, 'renderViews', [$session]);

        $this->assertStringContainsString('course-session-badge', $html);
        $this->assertStringContainsString('—', $html);
    }

    public function test_date_and_time_columns_render_in_german_format(): void
    {
        $table = App::make(CourseSessionTable::class);
        $session = $this->makeSession(1);

        $dateHtml = $this->invokeProtected($table, 'renderDate', [$session]);
        $timeHtml = $this->invokeProtected($table, 'renderTime', [$session]);

        $this->assertStringContainsString('13.10.2025', $dateHtml);
        $this->assertStringContainsString('Montag', $dateHtml);
        $this->assertStringContainsString('19:30 – 20:30', $timeHtml);
        $this->assertStringContainsString('course-session-time', $timeHtml);
    }

    public function test_capacity_column_prefers_session_seats_then_service(): void
    {
        $table = App::make(CourseSessionTable::class);
        $sessionWithSeats = $this->makeSession(1);
        $sessionWithSeats->available_seats = 15;

        $capacityHtml = $this->invokeProtected($table, 'renderCapacity', [$sessionWithSeats]);
        $this->assertStringContainsString('15', $capacityHtml);

        $sessionNoSeats = $this->makeSession(2);
        $sessionNoSeats->available_seats = null;

        $capacityFallback = $this->invokeProtected($table, 'renderCapacity', [$sessionNoSeats]);
        $this->assertStringContainsString('—', $capacityFallback);
    }

    public function test_hover_and_selection_styles_are_present(): void
    {
        $css = file_get_contents(base_path('public/vendor/core/plugins/courses/css/course-session-table.css'));

        $this->assertStringContainsString('tbody tr:hover', $css);
        $this->assertStringContainsString('tbody tr.selected', $css);
        $this->assertStringContainsString('course-session-button:hover', $css);
        $this->assertStringContainsString('course-session-score', $css);
    }

    public function test_stylesheet_registration_is_versioned(): void
    {
        $table = App::make(CourseSessionTable::class);

        $stylesheet = $this->invokeProtected($table, 'versionedStylesheet');

        $this->assertStringContainsString('course-session-table.css?v=', $stylesheet);
    }

    public function test_participants_button_is_accessible_and_styled(): void
    {
        $table = App::make(CourseSessionTable::class);
        $session = $this->makeSession(1);

        $html = $this->invokeProtected($table, 'renderParticipants', [$session]);

        $this->assertStringContainsString('course-session-button', $html);
        $this->assertStringContainsString('view-participants-btn', $html);
        $this->assertStringContainsString('Teilnehmerliste', $html);
    }

    protected function makeSession(int $id): CourseSession
    {
        $course = new Course();
        $course->setRawAttributes([
            'id' => 21,
            'name' => 'Singing mit Andrea',
            'thumbnail' => null,
            'price' => 19.99,
        ], true);

        $course->setRelation('category', (object) ['name' => 'Musik']);
        $course->setRelation('instructor', (object) ['name' => 'Andrea Schulz']);

        $session = new CourseSession();
        $session->setRawAttributes([
            'id' => $id,
            'course_id' => 21,
            'available_seats' => 10,
            'start_date' => Carbon::parse('2025-10-13 19:30:00'),
            'end_date' => Carbon::parse('2025-10-13 20:30:00'),
            'created_at' => Carbon::parse('2024-02-01 10:00:00'),
        ], true);

        $session->setRelation('course', $course);

        return $session;
    }

    protected function invokeProtected(object $object, string $method, array $arguments = []): string
    {
        $reflection = new \ReflectionClass($object);
        $reflectionMethod = $reflection->getMethod($method);
        $reflectionMethod->setAccessible(true);

        return (string) $reflectionMethod->invokeArgs($object, $arguments);
    }
}

class FakeCoursePerformanceService extends CoursePerformanceService
{
    public function __construct(
        private array $map,
        private int $maxViews,
        private int $analyticsDays
    ) {
    }

    public function forSession(CourseSession $session): array
    {
        return $this->map[$session->getKey()] ?? [
            'views' => 0,
            'views_source' => 'none',
            'analytics_days' => $this->analyticsDays,
            'booked_seats' => 0,
            'remaining_seats' => 0,
            'max_seats' => null,
            'engaged_participants' => 0,
            'engagement_percent' => 0.0,
            'conversion_percent' => 0.0,
            'score' => 0.0,
        ];
    }

    public function maxReferenceViews(): int
    {
        return $this->maxViews;
    }

    public function analyticsDays(): int
    {
        return $this->analyticsDays;
    }
}
