@extends(Theme::getThemeNamespace('layouts.master'))

@section('content')
<section class="container py-5">
  <h1 class="mb-4">{{ __('Kurse') }}</h1>
  <div class="row">
    @forelse($courses as $course)
      @php $s = $course->sessions->sortBy('starts_at')->first(); @endphp
      <div class="col-12 col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <h5 class="card-title">{{ $course->name }}</h5>
            @if($course->instructor)
              <div class="small text-muted mb-2">{{ __('Kurshalter') }}: {{ $course->instructor->name }}</div>
            @endif
            <p class="card-text">{{ Str::limit($course->description, 120) }}</p>
            @if($s)
              <p class="mb-1">{{ $s->starts_at->translatedFormat('d.m.Y H:i') }} @ {{ $s->location }}</p>
              <p class="mb-3">
                {{ $isLoggedIn ? format_price($s->price) : __('Preis nach Login') }}
              </p>
            @endif
            <a href="{{ route('public.courses.show', $course->slug) }}" class="btn btn-primary w-100">
              {{ __('Details') }}
            </a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><div class="alert alert-info">{{ __('Keine Kurse gefunden.') }}</div></div>
    @endforelse
  </div>
</section>
@endsection
