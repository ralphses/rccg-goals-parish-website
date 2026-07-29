@props([
    'title' => 'The Redeemed Christian Church of God, GOALS Parish | Worship, Word & Empowerment',
    'description' =>
        'Welcome to The Redeemed Christian Church of God, GOALS Parish. Join us for spirit-filled worship, life-transforming sermons, youth fellowship, community outreach, and empowering Christian ministries.',
    'keywords' =>
        'RCCG GOALS Parish, The Redeemed Christian Church of God, RCCG Church, Christian Church, Bible Teaching Church, Youth Fellowship Wednesdays 7pm, Church Services, Online Sermons, Church Events, Gospel Ministry, Children Ministry, Women Fellowship, Men Fellowship, Christian Worship Center',
])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}" />
    <meta name="keywords" content="{{ $keywords }}" />
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicons/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicons/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicons/favicon-16x16.png') }}" />
    <link rel="manifest" href="{{ asset('assets/images/favicons/site.webmanifest') }}" />

    <!-- Fonts and icons -->
    <script src="{{ asset('assets/dashboard/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/kaiadmin.min.css') }}" />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/demo.css') }}" />
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <!-- Logo Header -->
                <div class="logo-header" data-background-color="dark">
                    <a href="{{ route('home') }}" class="logo">
                        <img src="{{ asset('assets/images/resources/goals_logo_real.png') }}" alt="navbar brand"
                            class="navbar-brand" height="30" />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                            <i class="gg-menu-left"></i>
                        </button>
                    </div>
                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                </div>
                <!-- End Logo Header -->
            </div>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-secondary">

                        <li class="nav-item active">
                            <a href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('dashboard.events.index') }}">
                                <i class="fas fa-calendar"></i>
                                <p>Events</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('dashboard.departments.index') }}">
                                <i class="fas fa-sitemap"></i>
                                <p>Departments</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('dashboard.sermons.index') }}">
                                <i class="fas fa-book"></i>
                                <p>Sermons</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('dashboard.testimonies.index') }}">
                                <i class="fas fa-heart"></i>
                                <p>Testimonies</p>
                            </a>
                        </li>

                        @if (auth()->user()->isAdmin() || auth()->user()->isPastor() || auth()->user()->isMedia())
                        <li class="nav-item">
                            <a href="{{ route('dashboard.announcements.index') }}">
                                <i class="fas fa-bell"></i>
                                <p>Announcements</p>
                            </a>
                        </li>
                        @endif

                        @if (auth()->user()->isAdmin() || auth()->user()->isPastor() || auth()->user()->isMedia() || auth()->user()->isEditor())
                        <li class="nav-item">
                            <a href="{{ route('dashboard.media.index') }}">
                                <i class="fas fa-photo-video"></i>
                                <p>Media</p>
                            </a>
                        </li>
                        @endif

                        @if (auth()->user()->isAdmin() || auth()->user()->isPastor() || auth()->user()->isMedia())
                        <li class="nav-item">
                            <a href="{{ route('dashboard.stream.index') }}">
                                <i class="fas fa-video"></i>
                                <p>Stream</p>
                            </a>
                        </li>
                        @endif

                        @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                        <li class="nav-item">
                            <a href="{{ route('dashboard.users.index') }}">
                                <i class="fas fa-users"></i>
                                <p>Users</p>
                            </a>
                        </li>
                        @endif

                        <li class="nav-item">
                            <a href="{{ route('settings.index') }}">
                                <i class="fas fa-cog"></i>
                                <p>Settings</p>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
        <!-- End Sidebar -->
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="{{ route('dashboard') }}" class="logo">
                            <img src="{{ asset('assets/images/resources/goals_logo_real.png') }}" alt="navbar brand"
                                class="navbar-brand" height="30" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="gg-menu-right"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="gg-menu-left"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                    <!-- End Logo Header -->
                </div>
                <!-- Navbar Header -->
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <nav
                            class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="submit" class="btn btn-search pe-1">
                                        <i class="fa fa-search search-icon"></i>
                                    </button>
                                </div>
                                <input type="text" placeholder="Search ..." class="form-control" />
                            </div>
                        </nav>

                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <x-dashboard.notification-bell />
                            <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"
                                    role="button" aria-expanded="false" aria-haspopup="true">
                                    <i class="fa fa-search"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-search animated fadeIn">
                                    <form class="navbar-left navbar-form nav-search">
                                        <div class="input-group">
                                            <input type="text" placeholder="Search ..." class="form-control" />
                                        </div>
                                    </form>
                                </ul>
                            </li>

                            <li class="nav-item topbar-icon dropdown hidden-caret">
                                <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-bell"></i>
                                    <span class="notification">4</span>
                                </a>
                                <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                                    <li>
                                        <div class="dropdown-title">
                                            You have 4 new notification
                                        </div>
                                    </li>
                                    <li>
                                        <div class="notif-scroll scrollbar-outer">
                                            <div class="notif-center">
                                                <a href="#">
                                                    <div class="notif-icon notif-primary">
                                                        <i class="fa fa-user-plus"></i>
                                                    </div>
                                                    <div class="notif-content">
                                                        <span class="block"> New user registered </span>
                                                        <span class="time">5 minutes ago</span>
                                                    </div>
                                                </a>
                                                <a href="#">
                                                    <div class="notif-icon notif-success">
                                                        <i class="fa fa-comment"></i>
                                                    </div>
                                                    <div class="notif-content">
                                                        <span class="block">
                                                            Rahmad commented on Admin
                                                        </span>
                                                        <span class="time">12 minutes ago</span>
                                                    </div>
                                                </a>
                                                <a href="#">
                                                    <div class="notif-img">
                                                        <img src="{{ asset('assets/dashboard/img/profile2.jpg') }}"
                                                            alt="Img Profile" />
                                                    </div>
                                                    <div class="notif-content">
                                                        <span class="block">
                                                            Reza send messages to you
                                                        </span>
                                                        <span class="time">12 minutes ago</span>
                                                    </div>
                                                </a>
                                                <a href="#">
                                                    <div class="notif-icon notif-danger">
                                                        <i class="fa fa-heart"></i>
                                                    </div>
                                                    <div class="notif-content">
                                                        <span class="block"> Farrah liked Admin </span>
                                                        <span class="time">17 minutes ago</span>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="see-all" href="javascript:void(0);">See all notifications<i
                                                class="fa fa-angle-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item topbar-icon dropdown hidden-caret">
                                <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                                    <i class="fas fa-layer-group"></i>
                                </a>
                                <div class="dropdown-menu quick-actions animated fadeIn">
                                    <div class="quick-actions-header">
                                        <span class="title mb-1">Quick Actions</span>
                                        <span class="subtitle op-7">Shortcuts</span>
                                    </div>
                                    <div class="quick-actions-scroll scrollbar-outer">
                                        <div class="quick-actions-items">
                                            <div class="row m-0">
                                                <a class="col-6 col-md-4 p-0" href="#">
                                                    <div class="quick-actions-item">
                                                        <div class="avatar-item bg-danger rounded-circle">
                                                            <i class="far fa-calendar-alt"></i>
                                                        </div>
                                                        <span class="text">Calendar</span>
                                                    </div>
                                                </a>
                                                <a class="col-6 col-md-4 p-0" href="#">
                                                    <div class="quick-actions-item">
                                                        <div class="avatar-item bg-warning rounded-circle">
                                                            <i class="fas fa-map"></i>
                                                        </div>
                                                        <span class="text">Maps</span>
                                                    </div>
                                                </a>
                                                <a class="col-6 col-md-4 p-0" href="#">
                                                    <div class="quick-actions-item">
                                                        <div class="avatar-item bg-info rounded-circle">
                                                            <i class="fas fa-file-excel"></i>
                                                        </div>
                                                        <span class="text">Reports</span>
                                                    </div>
                                                </a>
                                                <a class="col-6 col-md-4 p-0" href="#">
                                                    <div class="quick-actions-item">
                                                        <div class="avatar-item bg-success rounded-circle">
                                                            <i class="fas fa-envelope"></i>
                                                        </div>
                                                        <span class="text">Emails</span>
                                                    </div>
                                                </a>
                                                <a class="col-6 col-md-4 p-0" href="#">
                                                    <div class="quick-actions-item">
                                                        <div class="avatar-item bg-primary rounded-circle">
                                                            <i class="fas fa-file-invoice-dollar"></i>
                                                        </div>
                                                        <span class="text">Invoice</span>
                                                    </div>
                                                </a>
                                                <a class="col-6 col-md-4 p-0" href="#">
                                                    <div class="quick-actions-item">
                                                        <div class="avatar-item bg-secondary rounded-circle">
                                                            <i class="fas fa-credit-card"></i>
                                                        </div>
                                                        <span class="text">Payments</span>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#"
                                    aria-expanded="false">
                                    <div class="avatar-sm">
                                        <img src="{{ auth()->user()->avatar_url ?? asset('assets/img/default-avatar.png') }}" alt="{{ auth()->user()->name }}"
                                            class="avatar-img rounded-circle" />
                                    </div>
                                    <span class="profile-username">
                                        <span class="op-7">Hi,</span>
                                        <span class="fw-bold">{{ auth()->user()->name }}</span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <div class="dropdown-user-scroll scrollbar-outer">
                                        <li>
                                            <div class="user-box">
                                                <div class="avatar-lg">
                                                    <img src="{{ auth()->user()->avatar_url ?? asset('assets/img/default-avatar.png') }}"
                                                        alt="image profile" class="avatar-img rounded" />
                                                </div>
                                                <div class="u-text">
                                                    <h4>{{ auth()->user()->name }}</h4>
                                                    <p class="text-muted mb-1">{{ auth()->user()->email }}</p>
                                                    <p class="text-muted mb-2">{{ ucwords(str_replace('_', ' ', auth()->user()->role->value)) }}</p>
                                                    <a href="{{ route('settings.index') }}"
                                                        class="btn btn-xs btn-secondary btn-sm">View
                                                        Profile</a>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="{{ route('settings.index') }}">My Profile</a>
                                            <a class="dropdown-item" href="{{ route('settings.index') }}">Account Settings</a>
                                            @if (auth()->user()->phone)
                                                <div class="dropdown-item-text text-muted small">Phone: {{ auth()->user()->phone }}</div>
                                            @endif
                                            @if (auth()->user()->occupation)
                                                <div class="dropdown-item-text text-muted small">Occupation: {{ auth()->user()->occupation }}</div>
                                            @endif
                                            @if (auth()->user()->state_of_origin)
                                                <div class="dropdown-item-text text-muted small">State: {{ auth()->user()->state_of_origin }}</div>
                                            @endif
                                            <div class="dropdown-divider"></div>

                                            <!-- Logout using Laravel POST -->
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item">Logout</button>
                                            </form>
                                        </li>
                                    </div>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>

            {{ $slot }}

        </div>
    </div>

    <!--   Core JS Files   -->
    <script src="{{ asset('assets/dashboard/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('assets/dashboard/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('assets/dashboard/js/plugin/chart.js/chart.min.js') }}"></script>

    <!-- jQuery Sparkline -->
    <script src="{{ asset('assets/dashboard/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

    <!-- Chart Circle -->
    <script src="{{ asset('assets/dashboard/js/plugin/chart-circle/circles.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('assets/dashboard/js/plugin/datatables/datatables.min.js') }}"></script>

    <!-- Bootstrap Notify -->
    <script src="{{ asset('assets/dashboard/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

    <!-- jQuery Vector Maps -->
    <script src="{{ asset('assets/dashboard/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/plugin/jsvectormap/world.js') }}"></script>
    <!-- Sweet Alert -->
    <script src="{{ asset('assets/dashboard/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('assets/dashboard/js/kaiadmin.min.js') }}"></script>

    <style>
        .dashboard-submit-progress {
            margin-top: 16px;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid #dbeafe;
            background: linear-gradient(180deg, #eff6ff 0%, #f8fbff 100%);
        }

        .dashboard-submit-progress.d-none {
            display: none !important;
        }

        .dashboard-submit-progress__head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 10px;
        }

        .dashboard-submit-progress__label {
            font-weight: 700;
            color: #0f172a;
        }

        .dashboard-submit-progress__hint {
            display: block;
            color: #64748b;
            font-size: 0.82rem;
            margin-top: 2px;
        }

        .dashboard-submit-progress__percent {
            font-weight: 800;
            color: #1d4ed8;
            min-width: 48px;
            text-align: right;
        }

        .dashboard-submit-progress__bar {
            height: 12px;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.12);
            overflow: hidden;
        }

        .dashboard-submit-progress__bar-fill {
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #2563eb 0%, #0ea5e9 100%);
            transition: width 0.18s ease;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dashboardForms = Array.from(document.querySelectorAll('.main-panel form'))
                .filter((form) => {
                    const method = (form.getAttribute('method') || 'GET').toUpperCase();
                    if (method === 'GET') return false;
                    if (form.matches('[data-media-form], [data-progress-ignore], [data-bulk-form]')) return false;
                    if ((form.getAttribute('action') || '').includes('/logout')) return false;
                    if (!form.querySelector('button[type="submit"], input[type="submit"]')) return false;
                    return true;
                });

            function createProgressNode() {
                const wrapper = document.createElement('div');
                wrapper.className = 'dashboard-submit-progress d-none';
                wrapper.innerHTML = `
                    <div class="dashboard-submit-progress__head">
                        <div>
                            <div class="dashboard-submit-progress__label" data-progress-label>Preparing request...</div>
                            <span class="dashboard-submit-progress__hint">Please keep this page open while the request completes.</span>
                        </div>
                        <div class="dashboard-submit-progress__percent" data-progress-percent>0%</div>
                    </div>
                    <div class="dashboard-submit-progress__bar">
                        <div class="dashboard-submit-progress__bar-fill" data-progress-fill></div>
                    </div>
                `;

                return wrapper;
            }

            function mountProgress(form, progressNode) {
                const actionRow = form.querySelector('.dashboard-form-actions, .card-action, .show-action-row, .comment-form__btn-box');
                if (actionRow?.parentNode) {
                    actionRow.parentNode.insertBefore(progressNode, actionRow);
                    return;
                }

                const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitButton?.parentNode) {
                    submitButton.parentNode.insertBefore(progressNode, submitButton);
                    return;
                }

                form.appendChild(progressNode);
            }

            function updateProgress(progressNode, percent, label) {
                const bounded = Math.max(0, Math.min(100, Math.round(percent)));
                progressNode.classList.remove('d-none');
                progressNode.querySelector('[data-progress-fill]').style.width = `${bounded}%`;
                progressNode.querySelector('[data-progress-percent]').textContent = `${bounded}%`;
                progressNode.querySelector('[data-progress-label]').textContent = label;
            }

            dashboardForms.forEach((form) => {
                const submitButtons = Array.from(form.querySelectorAll('button[type="submit"], input[type="submit"]'));
                if (!submitButtons.length) return;

                const progressNode = createProgressNode();
                mountProgress(form, progressNode);

                let isSubmitting = false;

                form.addEventListener('submit', function (event) {
                    if (event.defaultPrevented || isSubmitting) {
                        return;
                    }

                    isSubmitting = true;

                    submitButtons.forEach((button) => {
                        if (button.dataset.originalHtml === undefined) {
                            button.dataset.originalHtml = button.tagName === 'INPUT' ? button.value : button.innerHTML;
                        }

                        button.disabled = true;

                        if (button.tagName === 'INPUT') {
                            button.value = 'Submitting...';
                        } else {
                            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting...';
                        }
                    });

                    updateProgress(progressNode, 0, 'Preparing request...');
                    event.preventDefault();

                    const xhr = new XMLHttpRequest();
                    xhr.open((form.getAttribute('method') || 'POST').toUpperCase(), form.action, true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Accept', 'text/html,application/xhtml+xml');

                    xhr.upload.addEventListener('progress', function (uploadEvent) {
                        if (!uploadEvent.lengthComputable) {
                            updateProgress(progressNode, 15, 'Submitting request...');
                            return;
                        }

                        const percent = (uploadEvent.loaded / uploadEvent.total) * 100;
                        updateProgress(progressNode, percent, percent >= 100 ? 'Finalizing response...' : 'Submitting request...');
                    });

                    xhr.addEventListener('load', function () {
                        if (xhr.status >= 200 && xhr.status < 400) {
                            updateProgress(progressNode, 100, 'Request complete. Updating page...');
                            if (xhr.responseURL) {
                                window.history.replaceState({}, '', xhr.responseURL);
                            }
                            document.open();
                            document.write(xhr.responseText);
                            document.close();
                            return;
                        }

                        isSubmitting = false;
                        updateProgress(progressNode, 0, 'Request failed. Please try again.');

                        submitButtons.forEach((button) => {
                            button.disabled = false;
                            if (button.tagName === 'INPUT') {
                                button.value = button.dataset.originalHtml || 'Submit';
                            } else {
                                button.innerHTML = button.dataset.originalHtml || 'Submit';
                            }
                        });

                        alert('The request could not be completed. Please try again.');
                    });

                    xhr.addEventListener('error', function () {
                        isSubmitting = false;
                        updateProgress(progressNode, 0, 'Network error while submitting.');

                        submitButtons.forEach((button) => {
                            button.disabled = false;
                            if (button.tagName === 'INPUT') {
                                button.value = button.dataset.originalHtml || 'Submit';
                            } else {
                                button.innerHTML = button.dataset.originalHtml || 'Submit';
                            }
                        });

                        alert('A network error interrupted the request. Please try again.');
                    });

                    xhr.send(new FormData(form));
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
