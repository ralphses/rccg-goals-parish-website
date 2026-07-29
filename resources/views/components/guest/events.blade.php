@props(['events', 'sort'])

<!--Events Page Start-->
<section class="events-page py-5">
    <div class="container">

        <!-- Section Title -->
        <div class="section-title text-center mb-0">
            <span class="section-sub-title">Upcoming Gatherings</span>
            <h2 class="section-title__title">Church Events</h2>
        </div>

        <!-- Search and Sort -->
        <div class="row mb-4 align-items-center">
            <form id="events-filter-form" class="d-flex flex-wrap w-100" method="GET" action="{{ route('events') }}">
                <!-- Search Form -->
                <div class="col-xl-8 col-lg-8 col-md-12 mb-3 mb-lg-0">
                    <div class="events-search-form d-flex shadow-sm rounded-pill overflow-hidden w-100">
                        <input type="text" name="query" class="form-control border-0 px-4 py-3"
                            placeholder="Search events..." value="{{ request('query') }}">
                        <button type="submit" class="btn btn-primary px-4 rounded-end">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Sort Dropdown -->
                <div class="col-xl-4 col-lg-4 col-md-12 text-lg-end">
                    <select name="sort" class="form-select shadow-sm rounded-pill w-100 py-3 px-3 border-0 mt-2 mt-lg-0" onchange="document.getElementById('events-filter-form').submit()">
                        <option value="latest" @if($sort == 'latest') selected @endif>Sort by Latest</option>
                        <option value="date" @if($sort == 'date') selected @endif>Sort by Date</option>
                        <option value="location" @if($sort == 'location') selected @endif>Sort by Location</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="row">
            @forelse ($events as $event)
                <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                    <!--Events Single-->
                    <div class="events__single shadow-sm rounded overflow-hidden">
                        <div class="events__img">
                            <img src="{{ $event->image ? asset($event->image) : 'assets/images/resources/events-img-1.jpg' }}" alt="{{ $event->title }}" class="img-fluid">
                        </div>
                        <div class="events__content p-3">
                            <h3 class="events__title mb-2"><a href="{{ route('event', $event) }}">{{ $event->title }}</a></h3>
                            <ul class="list-unstyled events__meta mb-0">
                                <li class="mb-1"><i class="far fa-calendar-alt me-2"></i>{{ $event->event_date->format('d M, Y') }}</li>
                                <li class="mb-1"><i class="far fa-clock me-2"></i>{{ $event->event_date->format('g:ia') }}</li>
                                <li><i class="fas fa-map-marker-alt me-2"></i>{{ $event->location }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p>No events found at the moment.</p>
                </div>
            @endforelse
        </div>
        {{ $events->links('vendor.pagination.modern') }}
    </div>
</section>
<!--Events Page End-->
