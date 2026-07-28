<x-guest-layout 
    title="Reset Password | RCCG, GOALS Parish"
    description="Reset your password to regain access to your account at The Redeemed Christian Church of God, GOALS Parish."
    keywords="Reset Password, Change Password, RCCG GOALS Parish, Church Member Portal"
>
    <!-- Reset Password Page Start -->
    <section class="contact-page login-page">
        <div class="container">
            <div class="row justify-content-center align-items-center">

                <!-- Left Info -->
                <div class="col-xl-4 col-lg-5 mb-4 mb-lg-0">
                    <div class="contact-page__left text-center text-lg-start">
                        <div class="section-title text-left">
                            <span class="section-title__tagline">Account Security</span>
                            <h2 class="section-title__title">Reset Password</h2>
                        </div>

                        <p class="contact-page__text">
                            Please choose a strong password to secure your account.
                        </p>
                    </div>
                </div>

                <!-- Reset Password Form -->
                <div class="col-xl-6 col-lg-7">
                    <div class="contact-page__right login-card">
                        <!-- Display Status Messages -->
                        @if(session('status'))
                            <div class="alert alert-success mb-3">
                                {{ session('status') }}
                            </div>
                        @endif

                        <!-- Display Validation Errors -->
                        @if($errors->any())
                            <div class="alert alert-danger mb-3">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.store') }}" class="comment-one__form">
                            @csrf

                            <!-- Password Reset Token -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div class="row">
                                <!-- Email -->
                                <div class="col-12">
                                    <div class="comment-form__input-box">
                                        <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus placeholder="Email Address">
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="col-12">
                                    <div class="comment-form__input-box">
                                        <input type="password" name="password" required placeholder="New Password">
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-12">
                                    <div class="comment-form__input-box">
                                        <input type="password" name="password_confirmation" required placeholder="Confirm New Password">
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="comment-form__btn-box">
                                <button type="submit" class="thm-btn comment-form__btn w-100">
                                    Reset Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- Reset Password Page End -->
</x-guest-layout>
