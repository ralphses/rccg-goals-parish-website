@props(['galleryMedia', 'testimonyMedia'])

<!-- Media Page Start -->
<section class="gallery-page">
    <div class="container">

        <!-- ==================== GALLERY SECTION ==================== -->
        <div class="media-section mb-5" id="gallery">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <div class="section-title mb-0">
                    <span class="section-sub-title">Our Moments</span>
                    <h2 class="section-title__title">Church Gallery</h2>
                </div>
                {{-- <a href="#" class="view-all-link">View All</a> --}}
            </div>

            <div class="row">
                @forelse ($galleryMedia as $item)
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="two-section__gallery-single">
                            <div class="two-section__gallery-img-inner">
                                <img src="{{ asset($item->file_path) }}" alt="{{ $item->title }}">
                            </div>
                            <div class="two-section__gallery-img-overly">
                                <div class="two-section__gallery-icon-bg"></div>
                                <a class="img-popup" href="{{ asset($item->file_path) }}">
                                    <span class="icon-right-arrow"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p>No images found in the gallery at the moment.</p>
                    </div>
                @endforelse
            </div>
            {{ $galleryMedia->links('vendor.pagination.modern') }}
        </div>

        <!-- ==================== TESTIMONIES SECTION ==================== -->
        <div class="media-section mb-5" id="testimonies">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <div class="section-title mb-0">
                    <span class="section-sub-title">Faith Stories</span>
                    <h2 class="section-title__title">Testimonies</h2>
                </div>
                {{-- <a href="#" class="view-all-link">View All</a> --}}
            </div>

            <div class="row">
                @forelse ($testimonyMedia as $item)
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="two-section__gallery-single">
                            <div class="two-section__gallery-img-inner">
                                <img src="{{ asset($item->file_path) }}" alt="{{ $item->title }}">
                            </div>
                            <div class="two-section__gallery-img-overly">
                                <div class="two-section__gallery-icon-bg"></div>
                                <a class="img-popup" href="{{ asset($item->file_path) }}">
                                    <span class="icon-right-arrow"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p>No testimonies available at the moment.</p>
                    </div>
                @endforelse
            </div>
            {{ $testimonyMedia->links('vendor.pagination.modern') }}
        </div>
    </div>
</section>
<!-- Media Page End -->