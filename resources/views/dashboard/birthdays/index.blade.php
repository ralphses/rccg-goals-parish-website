<x-app-layout
    title="Birthday Greetings | RCCG GOALS Parish"
    description="Create and publish birthday greetings for church members."
    keywords="birthday greetings, church members, RCCG GOALS"
>
    <div class="container">
        <div class="page-inner">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <span class="text-uppercase fw-semibold text-primary small">Birthday Greetings Studio</span>
                        <h2 class="mb-1">Celebrants for {{ $today->format('l, d F Y') }}</h2>
                        <p class="text-muted mb-0">Create a personalized card, review it, then publish it to the home page.</p>
                    </div>
                    <span class="badge bg-primary fs-6 px-3 py-2">{{ $celebrants->count() }} celebrant{{ $celebrants->count() === 1 ? '' : 's' }}</span>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <div class="row g-4">
                @forelse ($celebrants as $celebrant)
                    @php($greeting = $greetings->get($celebrant->id))
                    <div class="col-xl-6">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <img src="{{ $celebrant->avatar_url }}" alt="{{ $celebrant->name }}" width="72" height="72" class="rounded-circle object-fit-cover border">
                                    <div>
                                        <h4 class="mb-1">{{ $celebrant->name }}</h4>
                                        <span class="text-muted"><i class="far fa-calendar-alt me-1"></i>{{ $celebrant->birthday->format('d F') }}</span>
                                        @if (!$celebrant->avatar)
                                            <span class="badge bg-light text-dark ms-2">Default photo</span>
                                        @endif
                                    </div>
                                </div>

                                @if ($greeting?->media)
                                    <img src="{{ $greeting->media->file_url }}" alt="Birthday card for {{ $celebrant->name }}" class="img-fluid rounded border mb-4 birthday-preview">
                                @endif

                                <form method="POST" action="{{ route('dashboard.birthdays.generate', $celebrant) }}" data-birthday-generation-form>
                                    @csrf
                                    <label for="message-{{ $celebrant->id }}" class="form-label fw-semibold">Birthday message</label>
                                    <textarea id="message-{{ $celebrant->id }}" name="message" rows="4" maxlength="280" class="form-control mb-3">{{ old('message', $greeting?->message ?? \App\Services\BirthdayCardService::DEFAULT_MESSAGE) }}</textarea>
                                    <button type="submit" class="btn btn-primary" data-generate-button><i class="fas fa-wand-magic-sparkles me-1"></i>{{ $greeting ? 'Regenerate card' : 'Generate card' }}</button>
                                    <div class="birthday-generation-progress d-none mt-3" role="status" aria-live="polite" data-generation-progress>
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span data-generation-status>Preparing card…</span>
                                            <span data-generation-percent>10%</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" data-generation-bar style="width: 10%"></div>
                                        </div>
                                    </div>
                                </form>

                                @if ($greeting?->media)
                                    <hr>
                                    <div class="d-flex gap-2 flex-wrap align-items-center">
                                        <a href="{{ route('dashboard.birthdays.download', $greeting) }}" class="btn btn-outline-secondary"><i class="fas fa-download me-1"></i>Download</a>
                                        @if ($greeting->isPublished())
                                            <form method="POST" action="{{ route('dashboard.birthdays.unpublish', $greeting) }}">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-outline-danger">Unpublish</button>
                                            </form>
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <form method="POST" action="{{ route('dashboard.birthdays.publish', $greeting) }}">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-success">Publish to home page</button>
                                            </form>
                                            <span class="badge bg-secondary">Draft</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12"><div class="card border-0 shadow-sm"><div class="card-body text-center py-5"><i class="fas fa-birthday-cake fa-3x text-muted mb-3"></i><h4>No birthdays today</h4><p class="text-muted mb-0">There are no active members celebrating today.</p></div></div></div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        .birthday-preview { max-height: 520px; width: 100%; object-fit: contain; background: #f8fafc; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-birthday-generation-form]').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    if (form.dataset.submitting === 'true') return;
                    form.dataset.submitting = 'true';
                    const button = form.querySelector('[data-generate-button]');
                    const progress = form.querySelector('[data-generation-progress]');
                    const bar = form.querySelector('[data-generation-bar]');
                    const status = form.querySelector('[data-generation-status]');
                    const percent = form.querySelector('[data-generation-percent]');
                    const message = form.querySelector('textarea[name="message"]');
                    let value = 10;

                    button.disabled = true;
                    message.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Generating card…';
                    progress.classList.remove('d-none');

                    const steps = [
                        [35, 'Preparing profile image…'],
                        [65, 'Rendering birthday card…'],
                        [88, 'Saving card…'],
                        [95, 'Finalizing preview…'],
                    ];

                    steps.forEach(([nextValue, label], index) => {
                        window.setTimeout(() => {
                            value = nextValue;
                            bar.style.width = `${value}%`;
                            percent.textContent = `${value}%`;
                            status.textContent = label;
                        }, (index + 1) * 550);
                    });

                    // Yield once so the initial progress state is painted before navigation begins.
                    window.requestAnimationFrame(() => window.setTimeout(() => form.submit(), 50));
                });
            });
        });
    </script>
</x-app-layout>
