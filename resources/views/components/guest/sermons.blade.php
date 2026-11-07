@props(['videoSermons', 'audioSermons'])

<!--Sermons List Start-->
<section class="donations-list sermons-list sermons-page--pack" id="video">
    <div class="container">
        <div class="sermons-page__heading">
            <span class="sermons-page__eyebrow"><span aria-hidden="true"></span>Watch and Grow</span>
            <h1>Recent Sermons</h1>
            <span class="sermons-page__accent" aria-hidden="true"></span>
            <p>Stay inspired and spiritually nourished through messages from our church family.</p>
        </div>
        <div class="row mb-4 align-items-center">
            <!-- Search Form -->
            <div class="row mb-4 align-items-center sermons-page__toolbar">
                <!-- Search Form -->
                <div class="col-xl-8 col-lg-8 col-md-12 mb-3 mb-lg-0">
                    <form class="sermon-search-form sermons-page__search d-flex overflow-hidden w-100" method="GET" action="{{ route('sermons') }}">
                        <input type="text" name="query" class="form-control border-0 px-4 py-3"
                            placeholder="Search sermons..." value="{{ request('query') }}">
                        <button type="submit" class="sermons-page__search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Sort Dropdown -->
                <div class="col-xl-4 col-lg-4 col-md-12 text-lg-end">
                    <select class="form-select sermons-page__sort w-100" aria-label="Sort video sermons">
                        <option selected>Sort by Latest</option>
                        <option value="popular">Sort by Popular</option>
                        <option value="preacher">Sort by Preacher</option>
                        <option value="date">Sort by Date</option>
                    </select>
                </div>
            </div>


            <div class="donations-list__inner">
                @forelse ($videoSermons as $sermon)
                    <article class="donations-list__single sermons-page__card">

                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <div class="donations-list__img sermons-page__image position-relative">
                                    <img src="{{ $sermon->cover_image_url ?? asset('assets/images/resources/donations-list-img-1.jpg') }}"
                                        alt="{{ $sermon->title }}" loading="lazy">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div class="donations-list__right">
                                    <div class="donations-list__content sermons-page__content">

                                        <div class="donations-list__category">
                                            <p><i class="fas fa-video" aria-hidden="true"></i>{{ $sermon->type }}</p>
                                        </div>

                                        <h3 class="donations-list__title mt-4">
                                            <a href="{{ route('sermons.show', $sermon->slug) }}">{{ $sermon->title }}</a>
                                        </h3>

                                        <p class="donations-list__text">
                                            {{ $sermon->description }}
                                        </p>

                                        <div class="sermon-meta sermons-page__meta mt-3">
                                            <p>By <strong> {{ $sermon->speaker->name ?? 'Unknown' }}
                                                    ({{ \Carbon\Carbon::parse($sermon->sermon_date)->format('F d, Y') }})</strong>
                                            </p>
                                        </div>

                                        <div class="mt-4">
                                            <a href="{{ route('sermons.show', $sermon->slug) }}" class="thm-btn">Watch Sermon</a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="sermons-page__empty"><i class="fas fa-video" aria-hidden="true"></i><p>No video sermons available at the moment.</p></div>
                @endforelse
            </div>

        </div>
        {{ $videoSermons->links('vendor.pagination.modern') }}
    </div>
</div>

</section>
<!--Sermons List End-->

<!--Sermons List Start-->
<section class="donations-list sermons-list sermons-page--pack sermons-page--audio" id="audio">
    <div class="container">
        <div class="sermons-page__heading">
            <span class="sermons-page__eyebrow"><span aria-hidden="true"></span>Listen Anywhere</span>
            <h2>Audio Sermons</h2>
            <span class="sermons-page__accent" aria-hidden="true"></span>
            <p>Carry the Word with you and make space for faith throughout your day.</p>
        </div>
        <div class="row mb-4 align-items-center">

            <div class="row mb-4 align-items-center sermons-page__toolbar">
                <!-- Search Form -->
                <div class="col-xl-8 col-lg-8 col-md-12 mb-3 mb-lg-0">
                    <form class="sermon-search-form sermons-page__search d-flex overflow-hidden w-100" method="GET" action="{{ route('sermons') }}">
                        <input type="text" name="query" class="form-control border-0 px-4 py-3"
                            placeholder="Search sermons..." value="{{ request('query') }}">
                        <button type="submit" class="sermons-page__search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Sort Dropdown -->
                <div class="col-xl-4 col-lg-4 col-md-12 text-lg-end">
                    <select class="form-select sermons-page__sort w-100" aria-label="Sort audio sermons">
                        <option selected>Sort by Latest</option>
                        <option value="popular">Sort by Popular</option>
                        <option value="preacher">Sort by Preacher</option>
                        <option value="date">Sort by Date</option>
                    </select>
                </div>
            </div>
            <div class="donations-list__inner">

                @forelse ($audioSermons as $sermon)
                    <article class="donations-list__single sermons-page__card">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <div class="donations-list__img sermons-page__image position-relative">
                                    <img src="{{ $sermon->cover_image_url ?? asset('assets/images/resources/donations-list-img-1.jpg') }}"
                                        alt="{{ $sermon->title }}" loading="lazy">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div class="donations-list__right">
                                    <div class="donations-list__content sermons-page__content">

                                        <div class="donations-list__category">
                                            <p><i class="fas fa-headphones" aria-hidden="true"></i>{{ $sermon->type }}</p>
                                        </div>

                                        <h3 class="donations-list__title mt-4">
                                            <a href="{{ route('sermons.show', $sermon->slug) }}">{{ $sermon->title }}</a>
                                        </h3>

                                        <p class="donations-list__text">
                                            {{ $sermon->description }}
                                        </p>

                                        <div class="sermon-meta sermons-page__meta mt-3">
                                            <p>By <strong> {{ $sermon->speaker->name ?? 'Unknown' }}
                                                    ({{ \Carbon\Carbon::parse($sermon->sermon_date)->format('F d, Y') }})</strong>
                                            </p>
                                        </div>

                                        <div class="mt-4">
                                            <a href="{{ route('sermons.show', $sermon->slug) }}" class="thm-btn">Listen Sermon</a>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="sermons-page__empty"><i class="fas fa-headphones" aria-hidden="true"></i><p>No audio sermons available at the moment.</p></div>
                @endforelse

            </div>
        </div>
    </div>
    {{ $audioSermons->links('vendor.pagination.modern') }}
</section>
<!--Sermons List End-->

