@extends(HotelHelper::viewPath('customers.master'))

@section('content')
    @php
        $user = auth('customer')->user();
    @endphp

    <div class="customer-card customer-card--profile">
        <div class="customer-card-header customer-card-header--centered">
            <div class="profile-card-summary">
                <div class="profile-card-avatar">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" />
                </div>
                <h2 class="customer-card-title">{{ __('Profil bearbeiten') }}</h2>
                <p class="customer-card-subtitle">{{ __('Passen Sie Ihre persönlichen Angaben an, damit Ihr Inspira-Konto auf dem neuesten Stand bleibt.') }}</p>
            </div>
        </div>

        <div class="customer-card-body">
            {!! Form::open(['route' => 'customer.edit-account', 'class' => 'customer-form']) !!}
                <div class="form-grid form-grid--two-columns">
                    <div class="form-field">
                        <label for="first_name">{{ __('Vorname') }}</label>
                        <input id="first_name" type="text" class="form-control @if ($errors->has('first_name')) is-invalid @endif" name="first_name" value="{{ $user->first_name }}" />
                        {!! Form::error('first_name', $errors) !!}
                    </div>

                    <div class="form-field">
                        <label for="last_name">{{ __('Nachname') }}</label>
                        <input id="last_name" type="text" class="form-control @if ($errors->has('last_name')) is-invalid @endif" name="last_name" value="{{ $user->last_name }}" />
                        {!! Form::error('last_name', $errors) !!}
                    </div>

                    <div class="form-field">
                        <label for="date_of_birth">{{ __('Geburtsdatum') }}</label>
                        <input id="date_of_birth" type="text" class="form-control date-picker @if ($errors->has('dob')) is-invalid @endif" name="dob" value="{{ $user->dob }}" autocomplete="false" />
                        {!! Form::error('dob', $errors) !!}
                    </div>

                    <div class="form-field">
                        <label for="email">{{ __('E-Mail') }}</label>
                        <input id="email" type="text" class="form-control @if ($errors->has('email')) is-invalid @endif" name="email" value="{{ $user->email }}" disabled />
                        {!! Form::error('email', $errors) !!}
                    </div>

                    <div class="form-field">
                        <label for="country">{{ __('Land') }}</label>
                        <input id="country" type="text" class="form-control @if ($errors->has('country')) is-invalid @endif" name="country" value="{{ $user->country }}" />
                        {!! Form::error('country', $errors) !!}
                    </div>

                    <div class="form-field">
                        <label for="state">{{ __('Bundesland / Provinz') }}</label>
                        <input id="state" type="text" class="form-control @if ($errors->has('state')) is-invalid @endif" name="state" value="{{ $user->state }}" />
                        {!! Form::error('state', $errors) !!}
                    </div>

                    <div class="form-field">
                        <label for="city">{{ __('Stadt') }}</label>
                        <input id="city" type="text" class="form-control @if ($errors->has('city')) is-invalid @endif" name="city" value="{{ $user->city }}" />
                        {!! Form::error('city', $errors) !!}
                    </div>

                    <div class="form-field">
                        <label for="address">{{ __('Adresse') }}</label>
                        <input id="address" type="text" class="form-control @if ($errors->has('address')) is-invalid @endif" name="address" value="{{ $user->address }}" />
                        {!! Form::error('address', $errors) !!}
                    </div>

                    <div class="form-field">
                        <label for="zip">{{ __('Postleitzahl') }}</label>
                        <input id="zip" type="text" class="form-control @if ($errors->has('zip')) is-invalid @endif" name="zip" value="{{ $user->zip }}" aria-describedby="txt-zip-error" />
                        {!! Form::error('zip', $errors) !!}
                    </div>

                    <div class="form-field">
                        <label for="phone">{{ __('Telefonnummer') }}</label>
                        <input id="phone" type="text" class="form-control @if ($errors->has('phone')) is-invalid @endif" name="phone" value="{{ $user->phone }}" />
                        {!! Form::error('phone', $errors) !!}
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary customer-btn">{{ __('Änderungen speichern') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection
