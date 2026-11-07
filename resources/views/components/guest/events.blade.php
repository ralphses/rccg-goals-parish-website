@props(['events', 'sort'])

<!--Events Page Start-->
<section class="events-page events-page--pack">
    <div class="container">

        <!-- Section Title -->
        <div class="section-title text-center events-page__heading">
            <span class="section-sub-title events-page__eyebrow"><span aria-hidden="true"></span>Upcoming Gatherings</span>
            <h2 class="section-title__title">Church Events</h2>
            <span class="events-page__accent" aria-hidden="true"></span>
            <p class="events-page__intro">Stay connected with what is happening in our church family and make room for the moments that matter.</p>
        </div>

        <!-- Search and Sort -->
        <div class="events-page__toolbar">
            <form id="events-filter-form" class="events-page__filter" method="GET" action="{{ route('events') }}">
                <!-- Search Form -->
                <div class="events-page__search">
                    <div class="events-search-form d-flex overflow-hidden w-100">
                        <input type="text" name="query" class="form-control border-0 px-4 py-3"
                            placeholder="Search events..." value="{{ request('query') }}">
                        <button type="submit" class="events-page__search-button" aria-label="Search events">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Sort Dropdown -->
                <div class="events-page__sort">
                    <select name="sort" class="form-select" onchange="document.getElementById('events-filter-form').submit()">
                        <option value="latest" @if($sort == 'latest') selected @endif>Sort by Latest</option>
                        <option value="date" @if($sort == 'date') selected @endif>Sort by Date</option>
                        <option value="location" @if($sort == 'location') selected @endif>Sort by Location</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="row events-page__grid">
            @forelse ($events as $event)
                <div class="col-xl-4 col-lg-6 col-md-6 events-page__item">
                    <!--Events Single-->
                    <article class="events__single events-page__card">
                        <div class="events__img events-page__image">
                            <img src="{{ $event->image_url ?? asset('assets/images/resources/events-img-1.jpg') }}" alt="{{ $event->title }}" class="img-fluid" loading="lazy">
                            <span class="events-page__image-badge"><i class="far fa-calendar-alt" aria-hidden="true"></i> Upcoming</span>
                        </div>
                        <div class="events__content events-page__content">
                            <h3 class="events__title"><a href="{{ route('event', $event) }}">{{ $event->title }}</a></h3>
                            <ul class="list-unstyled events__meta events-page__meta">
                                <li><i class="far fa-calendar-alt" aria-hidden="true"></i>{{ $event->event_date->format('d M, Y') }}</li>
                                <li><i class="far fa-clock" aria-hidden="true"></i>{{ $event->event_date->format('g:ia') }}</li>
                                <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i>{{ $event->location }}</li>
                            </ul>
                            <a class="events-page__link" href="{{ route('event', $event) }}">View event <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="events-page__empty">
                        <span class="events-page__empty-icon" aria-hidden="true"><i class="far fa-calendar-alt"></i></span>
                        <h3>No events found at the moment</h3>
                        <p>Check back soon for the next opportunity to gather with us.</p>
                    </div>
                </div>
            @endforelse
        </div>
        {{ $events->links('vendor.pagination.modern') }}
    </div>
</section>
<!--Events Page End-->
