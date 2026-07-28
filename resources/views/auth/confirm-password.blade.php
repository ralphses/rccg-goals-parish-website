<x-guest-layout 
    title="Confirm Password | RCCG, GOALS Parish"
    description="This is a secure area of the application. Please confirm your password before continuing."
    keywords="Confirm Password, Security, RCCG GOALS Parish, Member Portal"
>
    <!-- Confirm Password Page Start -->
    <section class="contact-page login-page">
        <div class="container">
            <div class="row justify-content-center align-items-center">

                <!-- Left Info -->
                <div class="col-xl-4 col-lg-5 mb-4 mb-lg-0">
                    <div class="contact-page__left text-center text-lg-start">
                        <div class="section-title text-left">
                            <span class="section-title__tagline">Security Check</span>
                            <h2 class="section-title__title">Confirm Password</h2>
                        </div>

                        <p class="contact-page__text">
                            This is a secure area of the application. Please confirm your password before continuing.
                        </p>
                    </div>
                </div>

                <!-- Confirm Password Form -->
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

                        <form method="POST" action="{{ route('password.confirm') }}" class="comment-one__form">
                            @csrf

                            <div class="row">
                                <!-- Password -->
                                <div class="col-12">
                                    <div class="comment-form__input-box">
                                        <input type="password" name="password" required autofocus placeholder="Password">
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="comment-form__btn-box">
                                <button type="submit" class="thm-btn comment-form__btn w-100">
                                    Confirm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- Confirm Password Page End -->
</x-guest-layout>
