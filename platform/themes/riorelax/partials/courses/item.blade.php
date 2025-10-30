<div class="course-body">
  <h4 class="course-title">{{ strip_tags($course->name) }}</h4>

  @if ($course->subtitle)
    <p class="course-subtitle" style="font-weight:600;color:#578E88;margin-bottom:2px;">
      {{ strip_tags($course->subtitle) }}
    </p>
  @endif

  @if ($course->description)
    <p class="course-desc">
      {!! BaseHelper::clean(strip_tags($course->description, '<strong><em><br>')) !!}
    </p>
  @endif

  <div class="course-meta">
    <div class="course-meta-left">
      <span class="mtxt"><i class="fal fa-calendar-alt"></i>{{ $dateLabel }}</span>
      @if ($course->price)
        <span class="mtxt">{{ format_price($course->price) }}</span>
      @endif
    </div>
    <a href="{{ $cartUrl }}" class="btn-cart" onclick="event.stopPropagation()">
      <i class="fal fa-shopping-cart"></i>
    </a>
  </div>
</div>
