@props(['sermon'])

<section class="event-details">
    <div class="container">
        <div class="row g-5">
            <div class="col-xl-7 col-lg-7">
                <div class="event-details__img mb-4">
                    <img src="{{ $sermon->cover_image_url ?? asset('assets/images/resources/donations-list-img-1.jpg') }}"
                        alt="{{ $sermon->title }}" loading="eager">
                </div>

                <div class="event-details__bottom-content">
                    <h1 class="event-details__title">{{ $sermon->title }}</h1>
                    @if ($sermon->description)
                        <p class="event-details__text-1 mb-4">{{ $sermon->description }}</p>
                    @endif

                    @if ($sermon->youtube_embed_url)
                        <div class="ratio ratio-16x9 mb-4">
                            <iframe src="{{ $sermon->youtube_embed_url }}"
                                title="{{ $sermon->title }}"
                                loading="lazy"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen></iframe>
                        </div>
                    @elseif ($sermon->video_url)
                        <div class="mb-4">
                            <a href="{{ $sermon->video_url }}" class="thm-btn" target="_blank" rel="noopener">Watch Sermon Video</a>
                        </div>
                    @endif

                    @if ($sermon->audio_url)
                        <div class="mb-4">
                            <audio controls preload="none" style="width: 100%;">
                                <source src="{{ $sermon->audio_url }}">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    @endif

                    @if ($sermon->message)
                        <div class="event-details__text-1">
                            {!! nl2br(e($sermon->message)) !!}
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-xl-5 col-lg-5">
                <div class="event-details__sidebar">
                    <ul class="event-details__sidebar-details list-unstyled">
                        <li>
                            <p>Speaker:</p>
                            <span>{{ $sermon->speaker->name ?? 'RCCG GOALS Parish' }}</span>
                        </li>
                        <li>
                            <p>Date:</p>
                            <span>{{ optional($sermon->sermon_date)->format('d M, Y') }}</span>
                        </li>
                        <li>
                            <p>Duration:</p>
                            <span>{{ $sermon->duration ?: 'N/A' }}</span>
                        </li>
                        <li>
                            <p>Status:</p>
                            <span>{{ ucfirst($sermon->status?->value ?? $sermon->status) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
