@props([
    'seo' => [],
    'title' => 'The Redeemed Christian Church of God, GOALS Parish | Worship, Word & Empowerment',
    'description' =>
        'Welcome to The Redeemed Christian Church of God, GOALS Parish. Join us for spirit-filled worship, life-transforming sermons, youth fellowship, community outreach, and empowering Christian ministries.',
    'keywords' =>
        'RCCG GOALS Parish, The Redeemed Christian Church of God, RCCG Church, Christian Church, Bible Teaching Church, Youth Fellowship Wednesdays 7pm, Church Services, Online Sermons, Church Events, Gospel Ministry, Children Ministry, Women Fellowship, Men Fellowship, Christian Worship Center',
])

@php
    $title = $seo['title'] ?? $title;
    $description = $seo['description'] ?? $description;
    $keywords = $seo['keywords'] ?? $keywords;
    $canonical = $seo['canonical'] ?? url()->current();
    $robots = $seo['robots'] ?? 'index,follow';
    $image = $seo['image'] ?? asset('assets/images/resources/goals_logo_real.png');
    $ogType = $seo['type'] ?? 'website';
    $siteName = $seo['site_name'] ?? config('seo.site_name');
    $twitterCard = $seo['twitter_card'] ?? 'summary_large_image';
    $prevUrl = $seo['prev'] ?? null;
    $nextUrl = $seo['next'] ?? null;
    $schemaPayloads = $seo['schema'] ?? [];
@endphp


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- SEO Title & Meta Description -->
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}" />
    <meta name="keywords" content="{{ $keywords }}" />
    <meta name="author" content="The Redeemed Christian Church of God, GOALS Parish" />
    <meta name="robots" content="{{ $robots }}" />
    <link rel="canonical" href="{{ $canonical }}" />
    @if ($prevUrl)
        <link rel="prev" href="{{ $prevUrl }}" />
    @endif
    @if ($nextUrl)
        <link rel="next" href="{{ $nextUrl }}" />
    @endif
    <meta property="og:type" content="{{ $ogType }}" />
    <meta property="og:title" content="{{ $title }}" />
    <meta property="og:description" content="{{ $description }}" />
    <meta property="og:url" content="{{ $canonical }}" />
    <meta property="og:image" content="{{ $image }}" />
    <meta property="og:site_name" content="{{ $siteName }}" />
    <meta name="twitter:card" content="{{ $twitterCard }}" />
    <meta name="twitter:title" content="{{ $title }}" />
    <meta name="twitter:description" content="{{ $description }}" />
    <meta name="twitter:image" content="{{ $image }}" />

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicons/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ asset('assets/images/favicons/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ asset('assets/images/favicons/favicon-16x16.png') }}" />
    <link rel="manifest" href="{{ asset('assets/images/favicons/site.webmanifest') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fredoka+One&family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&display=swap"
        rel="stylesheet" />

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/animate/animate.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/animate/custom-animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/jarallax/jarallax.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/jquery-magnific-popup/jquery.magnific-popup.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/nouislider/nouislider.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/nouislider/nouislider.pips.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/odometer/odometer.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/swiper/swiper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/pifoxen-icons/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/tiny-slider/tiny-slider.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/reey-font/stylesheet.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/owl-carousel/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/owl-carousel/owl.theme.default.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/bxslider/jquery.bxslider.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-select/css/bootstrap-select.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/vegas/vegas.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/jquery-ui/jquery-ui.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendors/timepicker/timePicker.css') }}" />

    <!-- Template Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/pifoxen.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/pifoxen-responsive.css') }}" />
    @foreach ($schemaPayloads as $schemaPayload)
        <script type="application/ld+json">{!! json_encode($schemaPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endforeach
</head>

<body>
    <div class="preloader">
        <img class="preloader__image" width="60" src="{{ asset('assets/images/resources/goals_logo_real.png') }}"
            alt="" />
    </div>
    <!-- /.preloader -->
    <div class="page-wrapper">
        <header class="main-header clearfix">
            <div class="main-header__top">
                <div class="main-header__top-left">
                    <p class="main-header__top-text">Welcome to The Redeemed Christian Church, Glory of All Lands Parish
                    </p>
                    <div class="main-header__top-social">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-pinterest-p"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="main-header__top-right">
                    <ul class="list-unstyled main-header__top-address">
                        <li>
                            <div class="icon">
                                <span class="icon-pin"></span>
                            </div>
                            <div class="text">
                                <p>Plot 27 Mobil Road, Off Ilaje Bustop, Ajah, Lagos</p>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <span class="icon-email"></span>
                            </div>
                            <div class="text">
                                <p><a href="mailto:info@rccggoalsparish.com">info@rccggoalsparish.com</a></p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <nav class="main-menu clearfix">
                <div class="main-menu-wrapper clearfix">

                    <!-- Logo (Extreme Left) -->
                    <div class="main-menu-wrapper__logo">
                        <a href="{{ route('home') }}" class="logo-link">
                            <img src="{{ asset('assets/images/resources/goals_logo_real.png') }}"
                                alt="RCCG Glory of All Lands Parish Logo">
                        </a>
                    </div>

                    <!-- Right Side: Menu + CTA -->
                    <div class="main-menu-wrapper__right-content">

                        <!-- Main Menu -->
                        <div class="main-menu-wrapper__main-menu">
                            <a href="#" class="mobile-nav__toggler">
                                <i class="fa fa-bars"></i>
                            </a>

                            <ul class="main-menu__list">
                                <li><a href="{{ route('home') }}">Home</a></li>

                                <li class="dropdown">
                                    <a href="{{ route('about') }}">About Us</a>
                                    <ul>
                                        <li><a href="{{ route('about') }}#history">Church History</a></li>
                                        <li><a href="{{ route('about') }}#mission">Mission & Vision</a></li>
                                        <li><a href="{{ route('about') }}#pastorate">Pastorate</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown">
                                    <a href="{{ route('sermons') }}">Sermons</a>
                                    <ul>
                                        <li><a href="{{ route('sermons') }}#video">Video Sermons</a></li>
                                        <li><a href="{{ route('sermons') }}#audio">Audio Sermons</a></li>
                                    </ul>
                                </li>

                                <li class="dropdown">
                                    <a href="{{ route('media') }}">Media</a>
                                    <ul>
                                        <li><a href="{{ route('media') }}#gallery">Gallery</a></li>
                                        <li><a href="{{ route('media') }}#testimonies">Testimonies</a></li>
                                    </ul>
                                </li>

                                <li><a href="{{ route('events') }}">Events</a></li>
                                <li><a href="{{ route('departments') }}">Departments</a></li>
                                <li><a href="{{ route('contact') }}">Contact</a></li>

                                <!-- Auth Button -->
                                @guest
                                    <li class="main-menu__login">
                                        <a href="{{ route('login') }}" class="btn btn-outline-primary"
                                            style="padding: 8px 18px; border-radius: 25px;">
                                            Login
                                        </a>
                                    </li>
                                @endguest

                                @auth
                                    <li class="main-menu__login">
                                        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary"
                                            style="padding: 8px 18px; border-radius: 25px; margin-right: 10px;">
                                            Dashboard
                                        </a>
                                    </li>
                                @endauth
                            </ul>
                        </div>

                    </div>
                </div>
            </nav>
        </header>


        {{ $slot }}

        <!--Site Footer Start-->
        <footer class="site-footer">
            <div class="site-footer-bg" style="background-image: url(assets/images/backgrounds/site-footer-bg.jpg);">
            </div>
            <div class="site-footer__top">
                <div class="container">
                    <div class="row">
                        <!-- About / Donate -->
                        <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                            <div class="footer-widget__column footer-widget__about">
                                <div class="footer-widget__about-text-box">
                                    <p class="footer-widget__about-text">Your Support Can Help Transform Lives and
                                        Empower Our Church Community</p>
                                </div>
                                <a href="donate-now.html" class="donate-btn footer-donate__btn">
                                    <i class="fa fa-heart"></i> Donate Now
                                </a>
                            </div>
                        </div>

                        <!-- Quick Links -->
                        <div class="col-xl-2 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                            <div class="footer-widget__column footer-widget__links clearfix">
                                <h3 class="footer-widget__title">Quick Links</h3>
                                <ul class="footer-widget__links-list list-unstyled clearfix">
                                    <li><a href="{{ route('about') }}">About Us</a></li>
                                    <li><a href="{{ route('sermons') }}">Sermons</a></li>
                                    <li><a href="{{ route('events') }}">Events</a></li>
                                    <li><a href="{{ route('media') }}">Media</a></li>
                                    <li><a href="{{ route('contact') }}">Contact</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Ministries / Programs -->
                        <div class="col-xl-2 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="300ms">
                            <div class="footer-widget__column footer-widget__non-profit clearfix">
                                <h3 class="footer-widget__title">Our Ministries</h3>
                                <ul class="footer-widget__non-profit-list list-unstyled clearfix">
                                    <li><a href="#">Children Ministry</a></li>
                                    <li><a href="#">Youth Ministry</a></li>
                                    <li><a href="#">Women Ministry</a></li>
                                    <li><a href="#">Men Ministry</a></li>
                                    <li><a href="#">Community Service</a></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Contact -->
                        <div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="400ms">
                            <div class="footer-widget__column footer-widget__contact clearfix">
                                <h3 class="footer-widget__title">Contact Us</h3>
                                <ul class="list-unstyled footer-widget__contact-list">
                                    <li>
                                        <div class="icon"><span class="icon-email"></span></div>
                                        <div class="text">
                                            <a href="mailto:info@rccggoalsparish.com">info@rccggoalsparish.com</a>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="icon"><span class="icon-telephone"></span></div>
                                        <div class="text">
                                            <a href="tel:+2348065799999">+234 806 579 9999</a>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="icon"><span class="icon-pin"></span></div>
                                        <div class="text">
                                            <p>Plot 27 Mobil Road, Off Ilaje Bustop, Ajah, Lagos</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="site-footer__bottom">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12">
                            <div
                                class="site-footer__bottom-inner d-flex justify-content-between align-items-center flex-wrap">
                                <p class="site-footer__bottom-text">© Copyright 2026 by
                                    <a href="#">The Redeemed Christian Church of God, GOALS Parish</a>
                                </p>
                                <div class="site-footer__social">
                                    <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                                    <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                    <a href="#" target="_blank"><i class="fab fa-pinterest-p"></i></a>
                                    <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!--Site Footer End-->


    </div><!-- /.page-wrapper -->


    <div class="mobile-nav__wrapper">
        <div class="mobile-nav__overlay mobile-nav__toggler"></div>

        <div class="mobile-nav__content">
            <span class="mobile-nav__close mobile-nav__toggler">
                <i class="fa fa-times"></i>
            </span>

            <!-- Mobile Logo -->
            <div class="mobile-nav__logo">
                <a href="index.html" aria-label="RCCG GOALS logo">
                    <img src="assets/images/resources/goals_logo_real.png" alt="RCCG Glory of All Lands Parish Logo">
                </a>
            </div>

            <div class="mobile-nav__container"></div>

            <ul class="mobile-nav__contact list-unstyled">
                <li>
                    <i class="fa fa-envelope"></i>
                    <a href="mailto:info@rccggoalsparish.com">info@rccggoalsparish.com</a>
                </li>
                <li>
                    <i class="fa fa-phone-alt"></i>
                    <a href="tel:+13073330079">+1 (307) 333-0079</a>
                </li>
            </ul>

            <div class="mobile-nav__top">
                <div class="mobile-nav__social">
                    <a href="#" class="fab fa-facebook-square"></a>
                    <a href="#" class="fab fa-instagram"></a>
                    <a href="#" class="fab fa-youtube"></a>
                </div>
            </div>
        </div>
    </div>

    <!-- /.mobile-nav__wrapper -->

    <div class="search-popup">
        <div class="search-popup__overlay search-toggler"></div>
        <!-- /.search-popup__overlay -->
        <div class="search-popup__content">
            <form action="#">
                <label for="search" class="sr-only">search here</label><!-- /.sr-only -->
                <input type="text" id="search" placeholder="Search Here..." />
                <button type="submit" aria-label="search submit" class="thm-btn">
                    <i class="icon-magnifying-glass"></i>
                </button>
            </form>
        </div>
        <!-- /.search-popup__content -->
    </div>
    <!-- /.search-popup -->

    <a href="#" data-target="html" class="scroll-to-target scroll-to-top"><i class="fa fa-angle-up"></i></a>


    <script src="{{ asset('assets/vendors/jquery/jquery-3.6.1.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jarallax/jarallax.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-ajaxchimp/jquery.ajaxchimp.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-appear/jquery.appear.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-circle-progress/jquery.circle-progress.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-magnific-popup/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-validate/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/nouislider/nouislider.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/odometer/odometer.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/swiper/swiper.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/tiny-slider/tiny-slider.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/wnumb/wNumb.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/wow/wow.js') }}"></script>
    <script src="{{ asset('assets/vendors/isotope/isotope.js') }}"></script>
    <script src="{{ asset('assets/vendors/countdown/countdown.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/owl-carousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/bxslider/jquery.bxslider.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/vegas/vegas.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/jquery-ui/jquery-ui.js') }}"></script>
    <script src="{{ asset('assets/vendors/timepicker/timePicker.js') }}"></script>

    <script>
        const videoModal = document.getElementById('videoModal');
        const videoFrame = document.getElementById('videoFrame');

        videoModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const videoSrc = button.getAttribute('data-video');
            videoFrame.src = videoSrc + "?autoplay=1&rel=0";
        });

        videoModal.addEventListener('hidden.bs.modal', function() {
            videoFrame.src = ""; // stop video when modal closed
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const audioModal = document.getElementById('audioModal');
            const audioPlayer = document.getElementById('audioPlayer');

            document.querySelectorAll('.open-audio').forEach(button => {
                button.addEventListener('click', function() {
                    let audioURL = this.getAttribute('data-audio');
                    audioPlayer.src = audioURL;
                    audioPlayer.load();
                    audioPlayer.play();
                });
            });

            audioModal.addEventListener('hidden.bs.modal', function() {
                audioPlayer.pause();
                audioPlayer.currentTime = 0;
                audioPlayer.src = "";
            });

        });
    </script>



    <!-- template js -->
    <script src="{{ asset('assets/js/pifoxen.js') }}"></script>

</body>

</html>
