<!-- Change Password Page Start -->
<section class="contact-page login-page">
    <div class="container">
        <div class="row justify-content-center align-items-center">

            <!-- Left Info -->
            <div class="col-xl-4 col-lg-5 mb-4 mb-lg-0">
                <div class="contact-page__left text-center text-lg-start">
                    <div class="section-title text-left">
                        <span class="section-title__tagline">Security Check</span>
                        <h2 class="section-title__title">Change Your Password</h2>
                    </div>

                    <p class="contact-page__text">
                        For security reasons, you are required to change your default password before accessing your dashboard.
                    </p>

                    <div class="contact-page__social">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            <!-- Change Password Form -->
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

                    <form method="POST" action="{{ route('password.change.update') }}" class="comment-one__form">
                        @csrf

                        <div class="row">
                            <!-- New Password -->
                            <div class="col-12">
                                <div class="comment-form__input-box">
                                    <input type="password" name="password" required autofocus placeholder="New Password">
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
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>
<!-- Change Password Page End -->