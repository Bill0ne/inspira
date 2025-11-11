<div class="checkout-modal" id="checkoutLoginModal" aria-hidden="true">
    <div class="checkout-modal__backdrop" data-close-login></div>
    <div class="checkout-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="checkoutLoginModalTitle">
        <button type="button" class="checkout-modal__close" data-close-login aria-label="Schließen">&times;</button>
        <h3 class="checkout-modal__title" id="checkoutLoginModalTitle">{{ __('Login') }}</h3>
        <form method="POST" action="{{ route('customer.login.post') }}" class="checkout-modal__form">
            @csrf
            <input type="hidden" name="intended_url" value="{{ \Botble\Base\Facades\BaseHelper::stringify($redirectUrl ?? request()->fullUrl()) }}">
            <input type="email" name="email" class="form-control" placeholder="E-Mail" value="{{ old('email') }}" required autocomplete="email">
            <input type="password" name="password" class="form-control" placeholder="{{ __('Password') }}" required autocomplete="current-password">
            <div class="checkout-modal__actions">
                <label class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" name="remember" value="1" @checked(old('remember', true))>
                    <span class="form-check-label">{{ __('Remember me') }}</span>
                </label>
                <a href="{{ route('customer.password.request') }}">{{ __('Forgot password?') }}</a>
            </div>
            <button type="submit" class="checkout-modal__submit">{{ __('Login') }}</button>
        </form>
        <div class="checkout-modal__footer">
            {{ __('Don’t have an account?') }} <a href="{{ route('customer.register') }}">{{ __('Sign up now') }}</a>
        </div>
    </div>
</div>
