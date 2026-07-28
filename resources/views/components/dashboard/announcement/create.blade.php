@props(['frequencies'])
@include('components.dashboard.partials.form-shell')

<div class="container"><div class="page-inner"><div class="dashboard-form-shell">
    <div class="dashboard-form-hero card mb-4"><div class="card-body p-4 p-lg-5"><div class="row align-items-center g-4"><div class="col-lg-8"><span class="dashboard-form-eyebrow">Announcements Form</span><h2 class="dashboard-form-title">Create an announcement with cleaner content, media, and scheduling controls.</h2><p class="dashboard-form-subtitle">Compose the notice, attach supporting media, and set the service date and recurrence from one refined workspace.</p></div><div class="col-lg-4"><div class="dashboard-form-hero-actions"><a href="{{ route('dashboard.announcements.index') }}" class="btn btn-outline-secondary btn-lg dashboard-form-secondary-btn">Back to Announcements</a><div class="dashboard-form-note"><span class="dot"></span>Media previews appear immediately after selection</div></div></div></div></div></div>
    <div class="card dashboard-form-card"><div class="card-header"><div class="card-title">New Announcement</div></div><div class="card-body">
        <form action="{{ route('dashboard.announcements.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group"><label for="title">Title</label><input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>@error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label for="content">Content</label><textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5" required>{{ old('content') }}</textarea>@error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="form-group"><label>Media Files (Images or Videos)</label><div class="input-group"><div class="custom-file w-100"><input type="file" class="custom-file-input form-control @error('media.*') is-invalid @enderror" id="media" name="media[]" multiple accept="image/*,video/*"><label class="custom-file-label form-control" for="media">Choose files</label></div></div>@error('media.*')<div class="text-danger mt-1">{{ $message }}</div>@enderror<div id="media-preview" class="mt-3 d-flex flex-wrap gap-2"></div></div>
            <div class="row">
                <div class="col-md-6"><div class="form-group"><label for="service_date">Service Date</label><input type="date" class="form-control @error('service_date') is-invalid @enderror" id="service_date" name="service_date" value="{{ old('service_date') }}" required>@error('service_date')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
                <div class="col-md-6"><div class="form-group"><label for="frequency">Frequency</label><select class="form-control @error('frequency') is-invalid @enderror" id="frequency" name="frequency">@foreach ($frequencies as $frequency)<option value="{{ $frequency->value }}" {{ old('frequency') == $frequency->value ? 'selected' : '' }}>{{ $frequency->name }}</option>@endforeach</select>@error('frequency')<div class="invalid-feedback">{{ $message }}</div>@enderror</div></div>
            </div>
            <div class="form-group"><div class="form-check"><input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}><label class="form-check-label" for="is_active">Active</label></div></div>
            <div class="dashboard-form-actions"><button type="submit" class="btn btn-primary dashboard-form-primary-btn">Create Announcement</button><a href="{{ route('dashboard.announcements.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
        </form>
    </div></div>
</div></div></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const mediaInput = document.getElementById('media');
    const mediaPreview = document.getElementById('media-preview');
    const fileLabel = document.querySelector('.custom-file-label');
    mediaInput.addEventListener('change', function () {
        mediaPreview.innerHTML = '';
        const files = this.files;
        fileLabel.textContent = files.length > 0 ? `${files.length} file(s) selected` : 'Choose files';
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
