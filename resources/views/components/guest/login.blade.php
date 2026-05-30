<!-- Login Page Start -->
<section class="contact-page login-page">
    <div class="container">
        <div class="row justify-content-center align-items-center">

            <!-- Left Info -->
            <div class="col-xl-4 col-lg-5 mb-4 mb-lg-0">
                <div class="contact-page__left text-center text-lg-start">
                    <div class="section-title text-left">
                        <span class="section-title__tagline">Member Access</span>
                        <h2 class="section-title__title">Welcome Back</h2>
                    </div>

                    <p class="contact-page__text">
                        Login to access sermons, departments, events, media, and your church dashboard.
                    </p>

                    <div class="contact-page__social">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            <!-- Login Form -->
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

                    <form method="POST" action="{{ route('login') }}" class="comment-one__form">
                        @csrf

                        <div class="row">
                            <!-- Email -->
                            <div class="col-12">
                                <div class="comment-form__input-box">
                                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Email Address">
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="col-12">
                                <div class="comment-form__input-box">
                                    <input type="password" name="password" required placeholder="Password">
                                </div>
                            </div>

                            <!-- Remember & Forgot -->
                            <div class="col-12 d-flex justify-content-between align-items-center mb-3">
                                <label class="remember-me">
                                    <input type="checkbox" name="remember"> Remember me
                                </label>

                                @if(Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                                @endif
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="comment-form__btn-box">
                            <button type="submit" class="thm-btn comment-form__btn w-100">
                                Login
                            </button>
                        </div>

                        <p class="text-center mt-2">
                            Don't have an account? <a href="{{ route('register') }}">Register here</a>
                        </p>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- Login Page End -->