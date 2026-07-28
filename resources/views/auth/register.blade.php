<x-guest-layout 
    title="Register | RCCG, GOALS Parish"
    description="Create a new account at The Redeemed Christian Church of God, GOALS Parish to access sermons, events, and department updates."
    keywords="Register, Create Account, RCCG GOALS Parish, Church Member Registration"
>
    <!-- Register Page Start -->
    <section class="contact-page login-page">
        <div class="container">
            <div class="row justify-content-center align-items-center">

                <!-- Left Info -->
                <div class="col-xl-4 col-lg-5 mb-4 mb-lg-0">
                    <div class="contact-page__left text-center text-lg-start">
                        <div class="section-title text-left">
                            <span class="section-title__tagline">Member Access</span>
                            <h2 class="section-title__title">Join Us</h2>
                        </div>

                        <p class="contact-page__text">
                            Register today to connect with departments, stream sermons, and get involved in church events.
                        </p>
                    </div>
                </div>

                <!-- Register Form -->
                <div class="col-xl-6 col-lg-7">
                    <div class="contact-page__right login-card">
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

                        <form method="POST" action="{{ route('register') }}" class="comment-one__form">
                            @csrf

                            <div class="row">
                                <!-- Name -->
                                <div class="col-12">
                                    <div class="comment-form__input-box">
                                        <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Full Name">
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-12">
                                    <div class="comment-form__input-box">
                                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="Email Address">
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="col-12">
                                    <div class="comment-form__input-box">
                                        <input type="password" name="password" required placeholder="Password">
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-12">
                                    <div class="comment-form__input-box">
                                        <input type="password" name="password_confirmation" required placeholder="Confirm Password">
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="comment-form__btn-box">
                                <button type="submit" class="thm-btn comment-form__btn w-100">
                                    Register
                                </button>
                            </div>

                            <p class="text-center mt-3">
                                Already have an account? <a href="{{ route('login') }}">Login here</a>
                            </p>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- Register Page End -->
</x-guest-layout>
