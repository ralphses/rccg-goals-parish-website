@php
    $notifications = auth()->user()->appNotifications()->latest()->take(5)->get();
    $unreadCount = auth()->user()->appNotifications()->where('is_read', false)->count();
@endphp

<li class="nav-item topbar-icon dropdown hidden-caret">
    <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-bell"></i>
        @if($unreadCount > 0)
            <span class="notification">{{ $unreadCount }}</span>
        @endif
    </a>
    <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
        <li>
            <div class="dropdown-title">You have {{ $unreadCount }} new notification(s)</div>
        </li>
        <li>
            <div class="notif-scroll scrollbar-outer">
                <div class="notif-center">
                    @foreach($notifications as $notification)
                        @if($notification->link)
                            <a href="{{ $notification->link }}" data-notification-id="{{ $notification->id }}" class="notification-item">
                                <div class="notif-icon">
                                    <i class="fa fa-plus-circle"></i>
                                </div>
                                <div class="notif-content">
                                    <span class="block">
                                        {{ $notification->title }}
                                    </span>
                                    <span class="time">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </a>
                        @else
                            <div class="notification-item-static">
                                <div class="notif-icon">
                                    <i class="fa fa-plus-circle"></i>
                                </div>
                                <div class="notif-content">
                                    <span class="block">
                                        {{ $notification->title }}
                                    </span>
                                    <span class="time">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </li>
        <li>
            <a class="see-all" href="javascript:void(0);">See all notifications<i class="fa fa-angle-right"></i>
            </a>
        </li>
    </ul>
</li>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            item.addEventListener('click', function(e) {
                const notificationId = this.dataset.notificationId;
                const link = this.href;

                // If there's no notification ID or link, let the browser handle it.
                if (!notificationId || !link) {
                    return;
                }

                // Prevent the default link navigation so we can mark as read first.
                e.preventDefault();

                fetch(`/dashboard/notifications/${notificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).finally(() => {
                    // Always navigate to the link, regardless of whether
                    // the mark-as-read call succeeded. The user's primary
                    // intent is to navigate.
                    window.location.href = link;
                });
            });
        });
    });
</script>
@endpush