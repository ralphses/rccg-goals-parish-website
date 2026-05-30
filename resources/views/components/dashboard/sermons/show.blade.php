<div class="container">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">

                <div class="card">

                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Sermon Details</div>
                        <div>
                              @if (auth()->user()->isAdmin() || auth()->user()->isPastor())
                            <a href="{{ route('dashboard.sermons.edit', $sermon) }}" class="btn btn-primary">Edit</a>
                            @endif
                            <a href="{{ route('dashboard.sermons.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </div>

                    <div class="card-body">

                        <!-- Sermon Details -->
                        <div class="row">

                            <!-- Cover Image -->
                            <div class="col-md-4">
                                <img src="{{ $sermon->cover_image }}" alt="{{ $sermon->title }}" class="img-fluid rounded">
                            </div>

                            <div class="col-md-8">
                                <h3>{{ $sermon->title }}</h3>
                                <p class="text-muted">
                                    Preached by {{ $sermon->speaker->name }} on {{ $sermon->sermon_date->format('d M, Y') }}
                                </p>
                                <p><strong>Duration:</strong> {{ $sermon->duration }}</p>
                                <p><strong>Status:</strong> <span class="badge bg-info">{{ $sermon->status }}</span></p>

                                <hr>

                                <h5>Description</h5>
                                <p>{{ $sermon->description }}</p>

                                <h5>Message</h5>
                                <div>{!! $sermon->message !!}</div>

                                <hr>

                                <!-- Audio/Video -->
                                @if($sermon->audio_url)
                                    <h5>Audio</h5>
                                    <audio controls class="w-100">
                                        <source src="{{ $sermon->audio_url }}" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                @endif

                                @if($sermon->video_url)
                                    <h5 class="mt-4">Video</h5>
                                    <div class="ratio ratio-16x9">
                                        <iframe src="{{ $sermon->video_url }}" title="Sermon Video" allowfullscreen></iframe>
                                    </div>
                                @endif

                                <hr>

                                <!-- Attachments -->
                                <h5>Attachments</h5>
                                @if($sermon->attachments->count() > 0)
                                    <ul class="list-group">
                                        @foreach($sermon->attachments as $attachment)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a href="{{ route('dashboard.sermon-attachments.download', $attachment) }}">{{ $attachment->file_name }}</a>
                                                <span class="badge bg-primary rounded-pill">{{ $attachment->file_type }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>No attachments for this sermon.</p>
                                @endif

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>