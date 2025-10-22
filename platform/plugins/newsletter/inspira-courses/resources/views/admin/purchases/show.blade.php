@extends('core/base::layouts.master')

@section('content')
<h3>Teilnehmer / Kauf #{{ $item->id }}</h3>
<div class="card">
  <div class="card-body">
    <p><strong>Transaktion:</strong> {{ $item->transaction_id }}</p>
    <p><strong>Kurs:</strong> {{ optional($item->session->course)->name }}</p>
    <p><strong>Termin:</strong> {{ optional($item->session->starts_at)->format('d.m.Y H:i') }} @ {{ $item->session->location }}</p>
    <p><strong>Menge:</strong> {{ $item->qty }}</p>
    <p><strong>Betrag:</strong> {{ format_price($item->amount) }} {{ $item->currency }}</p>
    <p><strong>Status:</strong> {{ $item->status }}</p>
    <a href="{{ route('inspira-courses.purchases.index') }}" class="btn btn-secondary">Zurück</a>
  </div>
</div>
@endsection
