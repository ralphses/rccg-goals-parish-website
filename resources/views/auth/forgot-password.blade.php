<x-guest-layout 
    title="Forgot Password | RCCG, GOALS Parish"
    description="Forgot your password? No worries. Enter your email address to receive a password reset link."
    keywords="Forgot Password, Reset Password, RCCG GOALS Parish, Church Member Portal"
>
    <!-- Forgot Password Page Start -->
    <section class="contact-page login-page">
        <div class="container">
            <div class="row justify-content-center align-items-center">

                <!-- Left Info -->
                <div class="col-xl-4 col-lg-5 mb-4 mb-lg-0">
                    <div class="contact-page__left text-center text-lg-start">
                        <div class="section-title text-left">
                            <span class="section-title__tagline">Password Recovery</span>
                            <h2 class="section-title__title">Forgot Password?</h2>
                        </div>

                        <p class="contact-page__text">
                            Enter your email address and we will mail you a link to reset your password.
                        </p>
                    </div>
                </div>

                <!-- Forgot Password Form -->
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

                        <form method="POST" action="{{ route('password.email') }}" class="comment-one__form">
                            @csrf

                            <div class="row">
                                <!-- Email -->
                                <div class="col-12">
                                    <div class="comment-form__input-box">
                                        <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Email Address">
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="comment-form__btn-box">
                                <button type="submit" class="thm-btn comment-form__btn w-100">
                                    Email Password Reset Link
                                </button>
                            </div>

                            <p class="text-center mt-3">
                                Remembered your password? <a href="{{ route('login') }}">Back to Login</a>
                            </p>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- Forgot Password Page End -->
</x-guest-layout>
