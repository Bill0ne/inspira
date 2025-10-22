@extends(Theme::getThemeNamespace('layouts.master'))

@section('content')
<section class="container py-5">
  <div class="text-center">
    <h1 class="mb-3">{{ __('Buchung erfolgreich!') }}</h1>
    <p class="mb-4">{{ __('Transaktion:') }} {{ $purchase->transaction_id }}</p>
    <p class="mb-1">{{ __('Kurs:') }} {{ $purchase->session->course->name }}</p>
    <p class="mb-1">{{ __('Termin:') }} {{ optional($purchase->session->starts_at)->translatedFormat('d.m.Y H:i') }} @ {{ $purchase->session->location }}</p>
    <p class="mb-1">{{ __('Menge:') }} {{ $purchase->qty }}</p>
    <p class="mb-1">{{ __('Status:') }} {{ ucfirst($purchase->status) }}</p>
    <a class="btn btn-primary mt-3" href="{{ route('public.courses.index') }}">{{ __('Zurück zu den Kursen') }}</a>
  </div>
</section>
@endsection
