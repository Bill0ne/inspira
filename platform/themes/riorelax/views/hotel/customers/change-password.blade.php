@extends(HotelHelper::viewPath('customers.master'))

@section('content')
    <div class="customer-card">
        <div class="customer-card-header">
            <h2 class="customer-card-title">{{ __('Change password') }}</h2>
            <p class="customer-card-subtitle">{{ __('Choose a strong password to secure your Inspira account.') }}</p>
        </div>

        <div class="customer-card-body">
            {!! Form::open(['route' => 'customer.post.change-password', 'method' => 'post', 'class' => 'customer-form']) !!}
                <div class="form-grid">
                    <div class="form-field">
                        <label for="old_password">{{ __('Old Password') }}</label>
                        <input id="old_password" type="password" class="form-control @if ($errors->has('old_password')) is-invalid @endif" name="old_password" placeholder="{{ __('Current Password') }}" />
                        {!! Form::error('old_password', $errors) !!}
                    </div>

                    <div class="form-field">
                        <label for="password">{{ __('New Password') }}</label>
                        <input id="password" type="password" class="form-control @if ($errors->has('password')) is-invalid @endif" name="password" placeholder="{{ __('New Password') }}" />
                        {!! Form::error('password', $errors) !!}
                    </div>

                    <div class="form-field">
                        <label for="password_confirmation">{{ __('Password Confirmation') }}</label>
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Password Confirmation') }}" />
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">{{ __('Change password') }}</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop
