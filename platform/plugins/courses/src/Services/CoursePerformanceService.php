<?php

namespace Botble\Courses\Services;

use Botble\Analytics\Exceptions\InvalidConfiguration;
use Botble\Analytics\Facades\Analytics;
use Botble\Analytics\Period;
use Botble\Courses\Models\Course;
use Botble\Courses\Models\CourseSession;
use Botble\Hotel\Enums\BookingStatusEnum;
use Botble\Slug\Facades\SlugHelper;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;
use Illuminate\Support\Arr;
use Throwable;

class CoursePerformanceService
{
    protected array $cache = [];

    protected array $viewCache = [];

    protected bool $canQueryAnalytics;

    protected int $maxViewsReference;

    protected int $analyticsLookbackDays;

    public function __construct()
    {
        $this->canQueryAnalytics = class_exists(Analytics::class)
            && (bool) setting('analytics_service_account_credentials')
            && (bool) setting('analytics_property_id');

        $config = config('plugins.courses.courses.performance', []);

        $this->maxViewsReference = max(1, (int) ($config['max_views_reference'] ?? 500));
        $this->analyticsLookbackDays = max(1, (int) ($config['analytics_period_days'] ?? 30));
    }

    public function forCourse(Course $course): array
    {
        $cacheKey = 'course-' . $course->getKey();

        if (! isset($this->cache[$cacheKey])) {
            $viewsData = $this->resolveViews($course);

            $booked = (int) ($course->active_bookings_count ?? $course->bookings()
                ->whereIn('status', [
                    BookingStatusEnum::PENDING,
                    BookingStatusEnum::PROCESSING,
                    BookingStatusEnum::COMPLETED,
                ])->count());

            $attended = (int) ($course->attended_bookings_count ?? $course->bookings()
                ->where('status', BookingStatusEnum::COMPLETED)
                ->count());

            $maxSeats = $course->unlimited_seats ? null : (int) ($course->number_of_seats ?: 0);

            $this->cache[$cacheKey] = $this->calculateScores(
                $viewsData['count'],
                $viewsData['source'],
                $booked,
                $maxSeats,
                $attended,
                $course
            );
        }

        return $this->cache[$cacheKey];
    }

    public function forSession(CourseSession $session): array
    {
        $cacheKey = 'session-' . $session->getKey();

        if (! isset($this->cache[$cacheKey])) {
            $course = $session->course;
            $viewsData = $course ? $this->resolveViews($course) : ['count' => 0, 'source' => 'none'];

            $booked = (int) ($session->active_bookings_count ?? $session->activeBookings()->count());
            $attended = (int) ($session->attended_bookings_count ?? $session->bookings()
                ->where('status', BookingStatusEnum::COMPLETED)
                ->count());

            $maxSeats = $session->available_seats;

            $this->cache[$cacheKey] = $this->calculateScores(
                $viewsData['count'],
                $viewsData['source'],
                $booked,
                $maxSeats,
                $attended,
                $course
            );
        }

        return $this->cache[$cacheKey];
    }

    public function analyticsAvailable(): bool
    {
        return $this->canQueryAnalytics;
    }

    public function analyticsDays(): int
    {
        return $this->analyticsLookbackDays;
    }

    protected function calculateScores(
        int $views,
        string $viewsSource,
        int $bookedSeats,
        ?int $maxSeats,
        int $engagedParticipants,
        ?Course $course = null
    ): array {
        $viewScore = min(($views / max($this->maxViewsReference, 1)) * 100, 100);

        $seatsCapacity = $maxSeats !== null ? max($maxSeats, 1) : 1;
        $occupancyScore = $maxSeats !== null
            ? min(($bookedSeats / $seatsCapacity) * 100, 100)
            : ($bookedSeats > 0 ? 100 : 0);

        $engagementScore = $bookedSeats > 0
            ? min(($engagedParticipants / max($bookedSeats, 1)) * 100, 100)
            : 0;

        $conversionScore = $views > 0
            ? min((($bookedSeats / max($views, 1)) * 100) * 10, 100)
            : 0;

        $finalScore = ($viewScore * 0.25)
            + ($occupancyScore * 0.35)
            + ($engagementScore * 0.25)
            + ($conversionScore * 0.15);

        $score = round(min($finalScore, 100), 1);

        if ($course) {
            $course->score = $score;
        }

        return [
            'views' => $views,
            'views_source' => $viewsSource,
            'booked_seats' => $bookedSeats,
            'engaged_participants' => $engagedParticipants,
            'max_seats' => $maxSeats,
            'remaining_seats' => $maxSeats !== null ? max($maxSeats - $bookedSeats, 0) : null,
            'view_score' => round($viewScore, 1),
            'occupancy_percent' => round($occupancyScore, 1),
            'engagement_percent' => round($engagementScore, 1),
            'conversion_percent' => round($conversionScore, 1),
            'score' => $score,
            'analytics_days' => $this->analyticsLookbackDays,
            'analytics_available' => $this->canQueryAnalytics,
        ];
    }

    protected function resolveViews(?Course $course): array
    {
        if (! $course) {
            return ['count' => 0, 'source' => 'none'];
        }

        $courseId = $course->getKey();

        if (isset($this->viewCache[$courseId])) {
            return $this->viewCache[$courseId];
        }

        $storedViews = $course->getMetaData('views', true);
        $hasStoredViews = $storedViews !== null && $storedViews !== '';
        $storedCount = (int) $storedViews;

        $result = ['count' => 0, 'source' => 'none'];

        $slug = SlugHelper::getSlug(null, null, $course::class, $courseId);

        if (! $slug) {
            if ($hasStoredViews) {
                $result = ['count' => $storedCount, 'source' => 'metadata'];
            }

            return $this->viewCache[$courseId] = $result;
        }

        $prefix = SlugHelper::getPrefix($course::class, 'kurse');
        $path = trim($prefix ? $prefix . '/' . $slug->key : $slug->key, '/');
        $fullUrl = url($path);

        if (! $this->canQueryAnalytics) {
            if ($hasStoredViews) {
                $result = ['count' => $storedCount, 'source' => 'metadata'];
            }

            return $this->viewCache[$courseId] = $result;
        }

        try {
            $queryResult = Analytics::dateRange(Period::days($this->analyticsLookbackDays))
                ->metrics('screenPageViews')
                ->dimensions('fullPageUrl')
                ->whereDimension('fullPageUrl', MatchType::MATCH_TYPE_EXACT, $fullUrl)
                ->limit(1)
                ->get()
                ->table;

            $views = (int) Arr::get($queryResult->first() ?? [], 'screenPageViews', 0);

            $result = ['count' => $views, 'source' => 'analytics'];
        } catch (InvalidConfiguration|Throwable) {
            if ($hasStoredViews) {
                $result = ['count' => $storedCount, 'source' => 'metadata'];
            }
        }

        if ($result['source'] === 'none' && $hasStoredViews) {
            $result = ['count' => $storedCount, 'source' => 'metadata'];
        }

        return $this->viewCache[$courseId] = $result;
    }
}
