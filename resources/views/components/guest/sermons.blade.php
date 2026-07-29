@props(['videoSermons', 'audioSermons'])

<!--Sermons List Start-->
<section class="donations-list sermons-list" id="video">
    <div class="container">
        <div class="row mb-4 align-items-center">
            <!-- Search Form -->
            <div class="row mb-4 align-items-center">
                <!-- Search Form -->
                <div class="col-xl-8 col-lg-8 col-md-12 mb-3 mb-lg-0">
                    <form class="sermon-search-form d-flex shadow-sm rounded-pill overflow-hidden w-100" method="GET"
                        action="#">
                        <input type="text" name="query" class="form-control border-0 px-4 py-3"
                            placeholder="Search sermons..." value="{{ request('query') }}">
                        <button type="submit" class="btn btn-primary px-4 rounded-end">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Sort Dropdown -->
                <div class="col-xl-4 col-lg-4 col-md-12 text-lg-end">
                    <select class="form-select shadow-sm rounded-pill w-100 py-3 px-3 border-0 mt-2 mt-lg-0">
                        <option selected>Sort by Latest</option>
                        <option value="popular">Sort by Popular</option>
                        <option value="preacher">Sort by Preacher</option>
                        <option value="date">Sort by Date</option>
                    </select>
                </div>
            </div>


            <div class="donations-list__inner">
                @forelse ($videoSermons as $sermon)
                    <div class="donations-list__single">

                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <div class="donations-list__img position-relative">
                                    <img src="{{ $sermon->cover_image_url ?? asset('assets/images/resources/donations-list-img-1.jpg') }}"
                                        alt="{{ $sermon->title }}" loading="lazy">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div class="donations-list__right">
                                    <div class="donations-list__content">

                                        <div class="donations-list__category">
                                            <p>{{ $sermon->type }}</p>
                                        </div>

                                        <h3 class="donations-list__title mt-4">
                                            <a href="{{ route('sermons.show', $sermon->slug) }}">{{ $sermon->title }}</a>
                                        </h3>

                                        <p class="donations-list__text">
                                            {{ $sermon->description }}
                                        </p>

                                        <div class="sermon-meta mt-3">
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
                    </div>
                @empty
                    <p>No video sermons available at the moment.</p>
                @endforelse
            </div>

        </div>
        {{ $videoSermons->links('vendor.pagination.modern') }}
    </div>
</div>

</section>
<!--Sermons List End-->

<!--Sermons List Start-->
<section class="donations-list sermons-list" id="audio">
    <div class="container">
        <div class="row mb-4 align-items-center">

            <div class="row mb-4 align-items-center">
                <!-- Search Form -->
                <div class="col-xl-8 col-lg-8 col-md-12 mb-3 mb-lg-0">
                    <form class="sermon-search-form d-flex shadow-sm rounded-pill overflow-hidden w-100" method="GET"
                        action="#">
                        <input type="text" name="query" class="form-control border-0 px-4 py-3"
                            placeholder="Search sermons..." value="{{ request('query') }}">
                        <button type="submit" class="btn btn-primary px-4 rounded-end">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Sort Dropdown -->
                <div class="col-xl-4 col-lg-4 col-md-12 text-lg-end">
                    <select class="form-select shadow-sm rounded-pill w-100 py-3 px-3 border-0 mt-2 mt-lg-0">
                        <option selected>Sort by Latest</option>
                        <option value="popular">Sort by Popular</option>
                        <option value="preacher">Sort by Preacher</option>
                        <option value="date">Sort by Date</option>
                    </select>
                </div>
            </div>
            <div class="donations-list__inner">

                @forelse ($audioSermons as $sermon)
                    <div class="donations-list__single">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <div class="donations-list__img position-relative">
                                    <img src="{{ $sermon->cover_image_url ?? asset('assets/images/resources/donations-list-img-1.jpg') }}"
                                        alt="{{ $sermon->title }}" loading="lazy">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div class="donations-list__right">
                                    <div class="donations-list__content">

                                        <div class="donations-list__category">
                                            <p>{{ $sermon->type }}</p>
                                        </div>

                                        <h3 class="donations-list__title mt-4">
                                            <a href="{{ route('sermons.show', $sermon->slug) }}">{{ $sermon->title }}</a>
                                        </h3>

                                        <p class="donations-list__text">
                                            {{ $sermon->description }}
                                        </p>

                                        <div class="sermon-meta mt-3">
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
                    </div>
                @empty
                    <p>No audio sermons available at the moment.</p>
                @endforelse

            </div>
        </div>
    </div>
    {{ $audioSermons->links('vendor.pagination.modern') }}
</section>
<!--Sermons List End-->

