@extends(HotelHelper::viewPath('customers.master'))

@section('content')
    <div class="customer-card">
        <div class="customer-card-header">
            <h2 class="customer-card-title">{{ __('Passwort ändern') }}</h2>
            <p class="customer-card-subtitle">{{ __('Wählen Sie ein sicheres Passwort, um Ihr Inspira-Konto zu schützen.') }}</p>
        </div>

        <div class="customer-card-body">
            {!! Form::open(['route' => 'customer.post.change-password', 'method' => 'post', 'class' => 'customer-form']) !!}
                <div class="form-grid">
                    <div class="form-field">
                        <label for="old_password">{{ __('Aktuelles Passwort') }}</label>
                        <input id="old_password" type="password" class="form-control @if ($errors->has('old_password')) is-invalid @endif" name="old_password" placeholder="{{ __('Aktuelles Passwort') }}" />
                        {!! Form::error('old_password', $errors) !!}
                    </div>

                    <div class="form-field">
                        <label for="password">{{ __('Neues Passwort') }}</label>
                        <input id="password" type="password" class="form-control @if ($errors->has('password')) is-invalid @endif" name="password" placeholder="{{ __('Neues Passwort') }}" />
                        {!! Form::error('password', $errors) !!}
                    </div>

                    <div class="form-field">
                        <label for="password_confirmation">{{ __('Passwortbestätigung') }}</label>
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Passwortbestätigung') }}" />
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">{{ __('Passwort ändern') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop
