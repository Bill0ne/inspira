@extends('core/base::layouts.master')

@section('content')
    <div class="max-w-full">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h5>Kundenkategorie setzen (Quick Test)</h5></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('pc.admin.setCustomerCategory') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <label>E-Mail des Kunden</label>
                                <input type="email" name="email" class="form-control" required placeholder="kunde@example.com">
                            </div>
                            <div class="form-group mb-3">
                                <label>Kundenkategorie</label>
                                <input type="text" name="customer_category" class="form-control" value="VIP" required>
                                <small class="text-muted">z. B. STANDARD, VIP, PARTNER …</small>
                            </div>
                            <button class="btn btn-primary">Speichern</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h5>Übersicht</h5></div>
                    <div class="card-body">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('pc.tiers.index') }}">Zu den Stufen</a>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('pc.rules.index') }}">Zu den Regeln</a>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('pc.categories.index') }}">Zu den Kategorien</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
