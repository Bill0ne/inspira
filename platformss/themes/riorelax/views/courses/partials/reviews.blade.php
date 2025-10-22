@php
    Theme::asset()->usePath()->add('jquery-bar-rating-css', 'plugins/jquery-bar-rating/css-stars.css');
    Theme::asset()->container('footer')->usePath()->add('jquery-bar-rating-js', 'plugins/jquery-bar-rating/jquery.barrating.min.js');
    Theme::asset()->container('footer')->usePath()->add('review-js', 'js/review.js');
@endphp

@php
    $canReview = false;
    $isLoggedIn = auth('customer')->check();

    if ($isLoggedIn) {
        $hasBooked = auth('customer')->user()->hasBookedCourse($model);
        $hasReviewed = auth('customer')->user()->hasReviewedCourse($model);
        $canReview = $hasBooked && ! $hasReviewed;
    }
@endphp

<div class="course-review-block mt-50">
    <h3 class="text-xl">{{ __('Write a review') }}</h3>
    <form action="{{ route('customer.ajax.course.review.store', $model->slug) }}" method="post" class="review-form space-y-3">
        @csrf
        <input type="hidden" name="course_id" value="{{ $model->id }}">

        <div class="mb-20">
            <select name="star" id="select-star">
                @foreach(range(1, 5) as $i)
                    <option value="{{ $i }}" @selected(old('star', 5) === $i)>{{ $i }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <textarea name="content" class="form-input custom-review-input mb-20"
                      placeholder="{{ __('Enter your message') }}"
                      @disabled(! $canReview)>{{ old('content') }}</textarea>
        </div>

        @if (! $isLoggedIn)
            <p class="text-danger">{{ __('Please log in to write a review!') }}</p>
        @elseif (! $hasBooked)
            <p class="text-danger">{{ __('You need to book this course to write a review!') }}</p>
        @elseif ($hasReviewed)
            <p class="text-danger">{{ __('You already wrote a review for this course!') }}</p>
        @endif

        <button type="submit" class="custom-submit-review-btn mb-20" @disabled(! $canReview)>
            {{ __('Submit review') }}
        </button>
    </form>

    <div class="pt-8 mt-8 border-top">
        @if($model->reviews_count)
            <div class="d-flex justify-content-between mt-10 mb-20 reviews-block">
                <h4>
                    <span class="reviews-count">
                        {{ __(':count Review(s)', ['count' => $model->approved_review_count]) }}
                    </span>
                </h4>
                <div class="loading-spinner d-none"></div>

                @include(Theme::getThemeNamespace('views.courses.partials.review-star'), [
                    'avgStar' => $model->reviews_avg_star,
                    'count'   => $model->reviews_count
                ])
            </div>
        @endif

        <div class="reviews-list mb-20 {{ $model->approved_review_count ? 'mt-10' : '' }}"
             data-url="{{ route('customer.ajax.course.review.index', $model->slug) }}">
        </div>
    </div>
</div>
