@extends(Theme::getThemeNamespace('layouts.master'))

@section('content')
<section class="container py-5">
  <h1 class="mb-1">{{ $course->name }}</h1>
  @if($course->instructor)
    <div class="mb-3 text-muted">{{ __('Kurshalter') }}: {{ $course->instructor->name }} @if($course->instructor->email) — {{ $course->instructor->email }} @endif</div>
  @endif
  <p class="mb-4">{{ $course->description }}</p>

  <div class="row">
    @forelse($course->sessions as $session)
      <div class="col-12 col-md-6 mb-4">
        <div class="p-3 border rounded h-100">
          <p class="mb-1"><strong>{{ __('Termin:') }}</strong> {{ $session->starts_at->translatedFormat('d.m.Y H:i') }} @ {{ $session->location }}</p>
          <p class="mb-1"><strong>{{ __('Plätze:') }}</strong> {{ $session->remainingSeats() }}</p>
          <p class="mb-3"><strong>{{ __('Preis:') }}</strong> {{ $isLoggedIn ? format_price($session->price) : __('Preis nach Login') }}</p>

          @if($session->remainingSeats() > 0)
            <form action="{{ route('public.courses.checkout') }}" method="POST" class="d-flex gap-2 align-items-center">
              @csrf
              <input type="hidden" name="session_id" value="{{ $session->id }}">
              <input type="number" name="qty" value="1" min="1" max="{{ $session->remainingSeats() }}" class="form-control" style="max-width:120px">
              @if($isLoggedIn)
                <input type="hidden" name="payment_method" value="{{ function_exists('get_payment_setting') ? \Botble\Payment\Supports\PaymentMethods::getDefaultMethod() : '' }}">
                <button type="submit" class="btn btn-primary">{{ __('Ticket kaufen') }}</button>
              @else
                <a href="{{ route('customer.login') }}" class="btn btn-secondary">{{ __('Zum Login, Preis anzeigen') }}</a>
              @endif
            </form>
          @else
            <div class="text-danger">{{ __('Ausgebucht') }}</div>
          @endif
        </div>
      </div>
    @empty
      <div class="col-12"><div class="alert alert-info">{{ __('Für diesen Kurs sind noch keine Termine verfügbar.') }}</div></div>
    @endforelse
  </div>
</section>
@endsection
