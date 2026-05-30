@props(['announcement', 'frequencies'])

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Edit Announcement</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('dashboard.announcements.index') }}">Announcements</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Edit: {{ $announcement->title }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Edit Announcement</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $announcement->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5" required>{{ old('content', $announcement->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Media Files (Images or Videos)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('media.*') is-invalid @enderror" id="media" name="media[]" multiple accept="image/*,video/*">
                                    <label class="custom-file-label" for="media">Choose files</label>
                                </div>
                            </div>
                            @error('media.*')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            <div id="media-preview" class="mt-3 d-flex flex-wrap gap-2"></div>
                            @if ($announcement->media->isNotEmpty())
                                <div class="mt-3">
                                    <p>Current Media:</p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($announcement->media as $media)
                                            <div class="position-relative">
                                                @if ($media->media_type === \App\Enums\MediaType::IMAGE)
                                                    <img src="{{ Storage::url($media->file_path) }}" alt="{{ $media->title }}" class="img-thumbnail" style="height: 100px;">
                                                @elseif ($media->media_type === \App\Enums\MediaType::VIDEO)
                                                    <video controls src="{{ Storage::url($media->file_path) }}" class="img-thumbnail" style="height: 100px;"></video>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="service_date">Service Date</label>
                                    <input type="date" class="form-control @error('service_date') is-invalid @enderror" id="service_date" name="service_date" value="{{ old('service_date', $announcement->service_date->format('Y-m-d')) }}" required>
                                    @error('service_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="frequency">Frequency</label>
                                    <select class="form-control @error('frequency') is-invalid @enderror" id="frequency" name="frequency">
                                        @foreach ($frequencies as $frequency)
                                            <option value="{{ $frequency->value }}" {{ old('frequency', $announcement->frequency->value) == $frequency->value ? 'selected' : '' }}>{{ $frequency->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('frequency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active', $announcement->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="is_approved" name="is_approved" {{ old('is_approved', $announcement->is_approved) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_approved">
                                            Approved
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-action">
                            <button type="submit" class="btn btn-success">Update Announcement</button>
                            <a href="{{ route('dashboard.announcements.index') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mediaInput = document.getElementById('media');
        const mediaPreview = document.getElementById('media-preview');
        const fileLabel = document.querySelector('.custom-file-label');

        mediaInput.addEventListener('change', function () {
            mediaPreview.innerHTML = ''; // Clear previous previews
            const files = this.files;

            if (files.length > 0) {
                fileLabel.textContent = `${files.length} file(s) selected`;
            } else {
                fileLabel.textContent = 'Choose files';
            }

            for (const file of files) {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.classList.add('img-thumbnail');
                        img.style.height = '100px';
                        mediaPreview.appendChild(img);
                    }
                    reader.readAsDataURL(file);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = URL.createObjectURL(file);
                    video.controls = true;
                    video.classList.add('img-thumbnail');
                    video.style.height = '100px';
                    mediaPreview.appendChild(video);
                }
            }
        });
    });
</script>
@props(['announcement', 'frequencies'])

<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Edit Announcement</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('dashboard.announcements.index') }}">Announcements</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="#">Edit: {{ $announcement->title }}</a>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Edit Announcement</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.announcements.update', $announcement->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $announcement->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5" required>{{ old('content', $announcement->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="service_date">Service Date</label>
                                    <input type="date" class="form-control @error('service_date') is-invalid @enderror" id="service_date" name="service_date" value="{{ old('service_date', $announcement->service_date->format('Y-m-d')) }}" required>
                                    @error('service_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="frequency">Frequency</label>
                                    <select class="form-control @error('frequency') is-invalid @enderror" id="frequency" name="frequency">
                                        @foreach ($frequencies as $frequency)
                                            <option value="{{ $frequency->value }}" {{ old('frequency', $announcement->frequency->value) == $frequency->value ? 'selected' : '' }}>{{ $frequency->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('frequency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active', $announcement->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="is_approved" name="is_approved" {{ old('is_approved', $announcement->is_approved) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_approved">
                                            Approved
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-action">
                            <button type="submit" class="btn btn-success">Update Announcement</button>
                            <a href="{{ route('dashboard.announcements.index') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>