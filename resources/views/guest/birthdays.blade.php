<x-guest-layout :seo="$seo">
    @php
        $currentUser = auth()->user();
        $canPost = $currentUser?->isActive();
        $canManageBoard = $currentUser && ($currentUser->isAdmin() || $currentUser->isMedia() || $currentUser->leadsActiveDepartment());
    @endphp

    <section class="birthday-board birthday-board--public py-5">
        <div class="container">
            <div class="section-title text-center">
                <span class="section-title__tagline">Celebrating with joy</span>
                <h1 class="section-title__title">Today's Birthday Celebrants</h1>
                <p class="birthday-board__date">{{ $today->format('l, j F') }}</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success birthday-board__alert">{{ session('success') }}</div>
            @endif

            @if ($birthdayGreetings->isEmpty())
                <div class="birthday-board__empty text-center">
                    <i class="fa fa-gift"></i>
                    <h3>No published birthday cards today</h3>
                    <p>Please join us again to celebrate the next members of our church family.</p>
                </div>
            @else
                <div class="birthday-board__cards {{ $birthdayGreetings->count() === 1 ? 'birthday-board__cards--single' : '' }}">
                    @foreach ($birthdayGreetings as $greeting)
                        <article class="birthday-board__card">
                            <img src="{{ $greeting->media->file_url }}" alt="Happy birthday {{ $greeting->user->name }}" loading="lazy">
                        </article>
                    @endforeach
                </div>

                <section class="birthday-board__messages" aria-labelledby="birthday-wishes-heading">
                    <div class="birthday-board__messages-heading">
                        <span class="section-title__tagline">Church family message board</span>
                        <h2 id="birthday-wishes-heading">Share a birthday wish</h2>
                    </div>

                    @if ($canPost)
                        <form method="POST" action="{{ route('birthdays.comments.store') }}" class="birthday-board__form">
                            @csrf
                            <label for="birthday-message" class="visually-hidden">Your birthday wish</label>
                            <textarea id="birthday-message" name="message" rows="4" maxlength="500" required placeholder="Write a joyful birthday message for today's celebrants...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="birthday-board__error">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="thm-btn">Post your wish <i class="fa fa-paper-plane"></i></button>
                        </form>
                    @elseif (auth()->guest())
                        <div class="birthday-board__login-note">
                            <p>Sign in with your church account to add a birthday wish.</p>
                            <a href="{{ route('login') }}" class="thm-btn">Sign in to comment</a>
                        </div>
                    @else
                        <div class="birthday-board__login-note">
                            <p>Only active church accounts can post birthday wishes.</p>
                        </div>
                    @endif

                    <div class="birthday-board__comment-list">
                        @forelse ($comments as $comment)
                            <article class="birthday-board__comment">
                                <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" class="birthday-board__comment-avatar">
                                <div class="birthday-board__comment-content">
                                    <div class="birthday-board__comment-meta">
                                        <strong>{{ $comment->user->name }}</strong>
                                        <time datetime="{{ $comment->created_at->toAtomString() }}">{{ $comment->created_at->timezone('Africa/Lagos')->format('j M, g:i a') }}</time>
                                    </div>
                                    <p>{{ $comment->message }}</p>
                                </div>
                                @if ($currentUser && ($comment->user_id === $currentUser->id || $canManageBoard))
                                    <form method="POST" action="{{ route('birthdays.comments.destroy', $comment) }}" class="birthday-board__delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="birthday-board__delete" aria-label="Remove birthday wish">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </article>
                        @empty
                            <p class="birthday-board__no-comments">Be the first to send a birthday wish.</p>
                        @endforelse
                    </div>
                </section>
            @endif
        </div>
    </section>
</x-guest-layout>
