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
                                    <img src="{{ $sermon->cover_image ?? asset('assets/images/resources/donations-list-img-1.jpg') }}"
                                        alt="{{ $sermon->title }}">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div class="donations-list__right">
                                    <div class="donations-list__content">

                                        <div class="donations-list__category">
                                            <p>{{ $sermon->type }}</p>
                                        </div>

                                        <h3 class="donations-list__title mt-4">
                                            <a href="#">{{ $sermon->title }}</a>
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
                                            <a href="#" class="open-video thm-btn" data-bs-toggle="modal"
                                                data-bs-target="#videoModal" data-video="{{ $sermon->video_url }}">
                                                Watch Sermon
                                            </a>
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
                                    <img src="{{ $sermon->cover_image ?? asset('assets/images/resources/donations-list-img-1.jpg') }}"
                                        alt="{{ $sermon->title }}">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div class="donations-list__right">
                                    <div class="donations-list__content">

                                        <div class="donations-list__category">
                                            <p>{{ $sermon->type }}</p>
                                        </div>

                                        <h3 class="donations-list__title mt-4">
                                            <a href="#">{{ $sermon->title }}</a>
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
                                            <a href="#" class="open-audio thm-btn" data-bs-toggle="modal"
                                                data-bs-target="#audioModal" data-audio="{{ $sermon->audio_url }}">
                                                Listen Sermon
                                            </a>
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


<!-- Video Modal -->
<div class="modal fade video-modal" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content video-modal-content">

            <div class="modal-body p-0 position-relative">

                <!-- Close Button -->
                <button type="button" class="btn-close video-close-btn" data-bs-dismiss="modal"
                    aria-label="Close"></button>

                <!-- Header -->
                <div class="video-header text-center">
                    <p class="video-subtitle">Be blessed by this powerful sermon</p>
                </div>

                <!-- Video Iframe -->
                <div class="video-body">
                    <iframe id="videoFrame" width="100%" height="450" src=""
                        title="YouTube video player" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audio Modal -->
<div class="modal fade audio-modal" id="audioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content audio-modal-content">

            <div class="modal-body p-0 position-relative">

                <!-- Close Button -->
                <button type="button" class="btn-close audio-close-btn" data-bs-dismiss="modal"
                    aria-label="Close"></button>

                <!-- Header Section -->
                <div class="audio-header text-center">
                    <div class="audio-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <p class="audio-subtitle">Be blessed by this powerful message</p>
                </div>

                <!-- Audio Section -->
                <div class="audio-body text-center">
                    <audio id="audioPlayer" controls>
                        <source src="" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>

            </div>
        </div>
    </div>
</div>