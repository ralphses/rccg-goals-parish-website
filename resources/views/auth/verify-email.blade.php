<x-guest-layout 
    title="Verify Email | RCCG, GOALS Parish"
    description="Verify your email address to access the dashboard at The Redeemed Christian Church of God, GOALS Parish."
    keywords="Verify Email, RCCG GOALS Parish, Member Portal"
>
    <!-- Verify Email Page Start -->
    <section class="contact-page login-page">
        <div class="container">
            <div class="row justify-content-center align-items-center">

                <!-- Left Info -->
                <div class="col-xl-4 col-lg-5 mb-4 mb-lg-0">
                    <div class="contact-page__left text-center text-lg-start">
                        <div class="section-title text-left">
                            <span class="section-title__tagline">Account Activation</span>
                            <h2 class="section-title__title">Verify Email</h2>
                        </div>

                        <p class="contact-page__text">
                            Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you. If you didn't receive the email, we will gladly send you another.
                        </p>
                    </div>
                </div>

                <!-- Verify Email Actions -->
                <div class="col-xl-6 col-lg-7">
                    <div class="contact-page__right login-card">
                        @if (session('status') == 'verification-link-sent')
                            <div class="alert alert-success mb-4">
                                A new verification link has been sent to the email address you provided during registration.
                            </div>
                        @endif

                        <div class="comment-one__form">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <div class="comment-form__btn-box mb-0">
                                        <button type="submit" class="thm-btn comment-form__btn">
                                            Resend Verification Email
                                        </button>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link text-muted" style="text-decoration: underline;">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- Verify Email Page End -->
</x-guest-layout>
