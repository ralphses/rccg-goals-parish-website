@props(['galleryMedia', 'testimonyMedia'])

<!-- Media Page Start -->
<section class="gallery-page media-page--pack">
    <div class="container">

        <div class="media-section media-section--pack" id="gallery">
            <div class="section-title media-page__heading">
                    <span class="section-sub-title media-page__eyebrow"><span aria-hidden="true"></span>Our Moments</span>
                    <h2 class="section-title__title">Church Gallery</h2>
                    <span class="media-page__accent" aria-hidden="true"></span>
                    <p class="media-page__intro">A collection of moments, memories, and celebrations from our church family.</p>
            </div>

            <div class="row media-page__grid">
                @forelse ($galleryMedia as $item)
                    @php
                        $galleryLinkClass = match ($item->media_type) {
                            \App\enums\MediaType::IMAGE => 'img-popup',
                            \App\enums\MediaType::VIDEO => 'video-popup',
                            default => '',
                        };
                        $galleryHref = $item->media_type === \App\enums\MediaType::VIDEO ? $item->youtube_embed_url : $item->file_url;
                    @endphp
                    <div class="col-xl-3 col-lg-4 col-md-6 media-page__item">
                        <article class="two-section__gallery-single media-page__card">
                            <div class="two-section__gallery-img-inner media-page__image">
                                <a class="{{ $galleryLinkClass }} guest-media-link" href="{{ $galleryHref }}" @if ($item->media_type === \App\enums\MediaType::IMAGE) data-group="1" @endif>
                                    <img src="{{ $item->visual_url }}" alt="{{ $item->title }}" class="guest-media-thumb" loading="lazy">
                                    <span class="media-page__type"><i class="{{ $item->media_type === \App\enums\MediaType::VIDEO ? 'fas fa-play' : 'fas fa-expand' }}" aria-hidden="true"></i>{{ $item->media_type === \App\enums\MediaType::VIDEO ? 'Watch' : 'View' }}</span>
                                </a>
                            </div>
                            <div class="media-page__card-body"><h3>{{ $item->title }}</h3><span>{{ $item->media_type === \App\enums\MediaType::VIDEO ? 'Video' : 'Photo' }}</span></div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="media-page__empty"><i class="far fa-images" aria-hidden="true"></i><p>No images found in the gallery at the moment.</p></div>
                    </div>
                @endforelse
            </div>
            {{ $galleryMedia->links('vendor.pagination.modern') }}
        </div>

        <div class="media-section media-section--pack" id="testimonies">
            <div class="section-title media-page__heading">
                    <span class="section-sub-title media-page__eyebrow"><span aria-hidden="true"></span>Faith Stories</span>
                    <h2 class="section-title__title">Testimonies</h2>
                    <span class="media-page__accent" aria-hidden="true"></span>
                    <p class="media-page__intro">See how God is moving through the lives and stories of our church community.</p>
            </div>

            <div class="row media-page__grid">
                @forelse ($testimonyMedia as $item)
                    @php
                        $testimonyLinkClass = match ($item->media_type) {
                            \App\enums\MediaType::IMAGE => 'img-popup',
                            \App\enums\MediaType::VIDEO => 'video-popup',
                            default => '',
                        };
                        $testimonyHref = $item->media_type === \App\enums\MediaType::VIDEO ? $item->youtube_embed_url : $item->file_url;
                    @endphp
                    <div class="col-xl-3 col-lg-4 col-md-6 media-page__item">
                        <article class="two-section__gallery-single media-page__card">
                            <div class="two-section__gallery-img-inner media-page__image">
                                <a class="{{ $testimonyLinkClass }} guest-media-link" href="{{ $testimonyHref }}" @if ($item->media_type === \App\enums\MediaType::IMAGE) data-group="2" @endif>
                                    <img src="{{ $item->visual_url }}" alt="{{ $item->title }}" class="guest-media-thumb" loading="lazy">
                                    <span class="media-page__type"><i class="{{ $item->media_type === \App\enums\MediaType::VIDEO ? 'fas fa-play' : 'fas fa-expand' }}" aria-hidden="true"></i>{{ $item->media_type === \App\enums\MediaType::VIDEO ? 'Watch' : 'View' }}</span>
                                </a>
                            </div>
                            <div class="media-page__card-body"><h3>{{ $item->title }}</h3><span>{{ $item->media_type === \App\enums\MediaType::VIDEO ? 'Video' : 'Photo' }}</span></div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="media-page__empty"><i class="far fa-images" aria-hidden="true"></i><p>No testimonies available at the moment.</p></div>
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
