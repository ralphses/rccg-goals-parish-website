@props(['event'])

<!--Event Details Start-->
<section class="event-details">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="event-details__img">
                    <img src="{{ $event->image ? asset($event->image) : asset('assets/images/resources/event-details-img-1.jpg') }}" alt="{{ $event->title }}">
                    <div class="events__date">
                        <p>{{ $event->event_date->format('d') }} <br> {{ $event->event_date->format('M') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="event-details__bottom">
            <div class="row">
                <div class="col-xl-8 col-lg-7">
                    <div class="event-details__bottom-left">
                        <div class="event-details__bottom-content">
                            <h3 class="event-details__title">{{ $event->title }}</h3>
                            <p class="event-details__text-1">{{ $event->description }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-5">
                    <div class="event-details__sidebar">
                        <ul class="event-details__sidebar-details list-unstyled">
                            <li>
                                <p>Starting Time:</p>
                                <span>{{ $event->event_date->format('h:i A') }}</span>
                            </li>
                            <li>
                                <p>Date:</p>
                                <span>{{ $event->event_date->format('d M, Y') }}</span>
                            </li>
                            <li>
                                <p>Location:</p>
                                <span>{{ $event->location }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Event Details End-->