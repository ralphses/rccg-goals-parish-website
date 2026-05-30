 <!--Contact Page Start-->
        <section class="contact-page">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4 col-lg-5">
                        <div class="contact-page__left">
                            <div class="section-title text-left">
                                <span class="section-title__tagline">Contact with us</span>
                                <h2 class="section-title__title">Get in Touch With us</h2>
                            </div>
                            <p class="contact-page__text">We would love to hear from you. Whether you have a question, a prayer request, or just want to say hello, please don't hesitate to reach out.</p>
                            <div class="contact-page__social">
                                <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                                <a href="#" target="_blank"><i class="fab fa-facebook"></i></a>
                                <a href="#" target="_blank"><i class="fab fa-pinterest-p"></i></a>
                                <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                                <a href="https://chat.whatsapp.com/IaH0ZO2WcurJEoQMvhnMTW?mode=gi_t" target="_blank"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-7">
                        <div class="contact-page__right">
                            <form action="{{ route('contact.store') }}" method="POST" class="comment-one__form contact-form-validated">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="comment-form__input-box">
                                            <input type="text" placeholder="Your Name" name="name">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="comment-form__input-box">
                                            <input type="email" placeholder="Email Address" name="email">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="comment-form__input-box">
                                            <input type="text" placeholder="Phone Number" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="comment-form__input-box">
                                            <select name="subject">
                                                <option value="">Subject</option>
                                                <option value="General Inquiry">General Inquiry</option>
                                                <option value="Prayer Request">Prayer Request</option>
                                                <option value="Testimony">Testimony</option>
                                                <option value="Feedback">Feedback</option>
                                            </select><!-- /# -->
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="comment-form__input-box text-message-box">
                                            <textarea name="message" placeholder="Write a Comment"></textarea>
                                        </div>
                                        <div class="comment-form__btn-box">
                                            <button type="submit" class="thm-btn comment-form__btn">Send us a
                                                message</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Contact Page End-->
        

        <!--Contact Info End-->