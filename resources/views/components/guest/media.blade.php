@props(['galleryMedia', 'testimonyMedia'])

<!-- Media Page Start -->
<section class="gallery-page">
    <div class="container">

        <div class="media-section mb-5" id="gallery">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <div class="section-title mb-0">
                    <span class="section-sub-title">Our Moments</span>
                    <h2 class="section-title__title">Church Gallery</h2>
                </div>
            </div>

            <div class="row">
                @forelse ($galleryMedia as $item)
                    @php
                        $galleryLinkClass = match ($item->media_type) {
                            \App\enums\MediaType::IMAGE => 'img-popup',
                            \App\enums\MediaType::VIDEO => 'video-popup',
                            default => '',
                        };
                        $galleryHref = $item->media_type === \App\enums\MediaType::VIDEO ? $item->youtube_embed_url : $item->file_url;
                    @endphp
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="two-section__gallery-single">
                            <div class="two-section__gallery-img-inner">
                                <a class="{{ $galleryLinkClass }} guest-media-link" href="{{ $galleryHref }}" @if ($item->media_type === \App\enums\MediaType::IMAGE) data-group="1" @endif>
                                    <img src="{{ $item->visual_url }}" alt="{{ $item->title }}" class="guest-media-thumb" loading="lazy">
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

        <div class="media-section mb-5" id="testimonies">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <div class="section-title mb-0">
                    <span class="section-sub-title">Faith Stories</span>
                    <h2 class="section-title__title">Testimonies</h2>
                </div>
            </div>

            <div class="row">
                @forelse ($testimonyMedia as $item)
                    @php
                        $testimonyLinkClass = match ($item->media_type) {
                            \App\enums\MediaType::IMAGE => 'img-popup',
                            \App\enums\MediaType::VIDEO => 'video-popup',
                            default => '',
                        };
                        $testimonyHref = $item->media_type === \App\enums\MediaType::VIDEO ? $item->youtube_embed_url : $item->file_url;
                    @endphp
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="two-section__gallery-single">
                            <div class="two-section__gallery-img-inner">
                                <a class="{{ $testimonyLinkClass }} guest-media-link" href="{{ $testimonyHref }}" @if ($item->media_type === \App\enums\MediaType::IMAGE) data-group="2" @endif>
                                    <img src="{{ $item->visual_url }}" alt="{{ $item->title }}" class="guest-media-thumb" loading="lazy">
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

@push('scripts')
    <style>
        .guest-media-thumb {
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
        }

        .guest-media-link {
            display: block;
        }
    </style>
@endpush
