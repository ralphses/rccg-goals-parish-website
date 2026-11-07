<x-guest-layout :seo="$seo">
    @php
        $currentUser = auth()->user();
        $canPost = $currentUser?->isActive();
        $canManageBoard = $currentUser && ($currentUser->isAdmin() || $currentUser->isMedia() || $currentUser->leadsActiveDepartment());
    @endphp

    <section class="birthday-page birthday-page--pack">
        <div class="birthday-page__glow birthday-page__glow--one" aria-hidden="true"></div>
        <div class="birthday-page__glow birthday-page__glow--two" aria-hidden="true"></div>

        <div class="container birthday-page__container">
            <header class="birthday-page__heading">
                <span class="birthday-page__eyebrow"><span aria-hidden="true"></span>Celebrate our church family</span>
                <h1>Today's Birthday Celebrants</h1>
                <span class="birthday-page__accent" aria-hidden="true"></span>
                <div class="birthday-page__date"><i class="far fa-calendar-alt" aria-hidden="true"></i>{{ $today->format('l, j F Y') }}</div>
                <p>Join us in celebrating the people who make our church family brighter, warmer, and stronger.</p>
            </header>

            @if (session('success'))
                <div class="alert alert-success birthday-page__alert">{{ session('success') }}</div>
            @endif

            @if ($birthdayGreetings->isEmpty())
                <div class="birthday-page__empty">
                    <div class="birthday-page__empty-icon"><i class="fas fa-gift" aria-hidden="true"></i></div>
                    <div>
                        <span class="birthday-page__card-kicker">A little celebration is coming</span>
                        <h2>No published birthday cards today</h2>
                        <p>Please join us again to celebrate the next members of our church family.</p>
                    </div>
                </div>
            @else
                <section class="birthday-page__celebrants" aria-labelledby="birthday-cards-heading">
                    <div class="birthday-page__section-heading">
                        <div>
                            <span class="birthday-page__card-kicker">Today's celebration board</span>
                            <h2 id="birthday-cards-heading">Send them some love</h2>
                        </div>
                        <span class="birthday-page__count">{{ $birthdayGreetings->count() }} {{ Str::plural('celebrant', $birthdayGreetings->count()) }}</span>
                    </div>

                    <div class="birthday-page__cards">
                        @foreach ($birthdayGreetings as $greeting)
                            <article class="birthday-page__card">
                                <div class="birthday-page__card-image">
                                    <img src="{{ $greeting->media->file_url }}" alt="Happy birthday {{ $greeting->user->name }}" loading="lazy">
                                </div>
                                <div class="birthday-page__card-footer">
                                    <span><i class="fas fa-sparkles" aria-hidden="true"></i>Celebrating today</span>
                                    <strong>{{ $greeting->user->name }}</strong>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section class="birthday-page__messages" aria-labelledby="birthday-wishes-heading">
                    <div class="birthday-page__messages-intro">
                        <span class="birthday-page__eyebrow"><span aria-hidden="true"></span>Church family message board</span>
                        <h2 id="birthday-wishes-heading">Share a birthday wish</h2>
                        <p>A kind word, a prayer, or a joyful note can make someone's celebration even more special.</p>

                        @if ($canPost)
                            <form method="POST" action="{{ route('birthdays.comments.store') }}" class="birthday-page__form">
                                @csrf
                                <label for="birthday-message" class="visually-hidden">Your birthday wish</label>
                                <textarea id="birthday-message" name="message" rows="4" maxlength="500" required placeholder="Write a joyful birthday message...">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="birthday-page__error">{{ $message }}</p>
                                @enderror
                                <button type="submit" class="birthday-page__button">Post your wish <i class="fas fa-paper-plane" aria-hidden="true"></i></button>
                            </form>
                        @elseif (auth()->guest())
                            <div class="birthday-page__login-note">
                                <p>Sign in with your church account to add a birthday wish.</p>
                                <a href="{{ route('login') }}" class="birthday-page__button">Sign in to comment <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                            </div>
                        @else
                            <div class="birthday-page__login-note">
                                <p>Only active church accounts can post birthday wishes.</p>
                            </div>
                        @endif
                    </div>

                    <div class="birthday-page__wishes">
                        <div class="birthday-page__wishes-header">
                            <span class="birthday-page__card-kicker">Words of joy</span>
                            <span class="birthday-page__wishes-icon"><i class="fas fa-heart" aria-hidden="true"></i></span>
                        </div>
                        <div class="birthday-page__comment-list">
                            @forelse ($comments as $comment)
                                <article class="birthday-page__comment">
                                    <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" class="birthday-page__comment-avatar">
                                    <div class="birthday-page__comment-content">
                                        <div class="birthday-page__comment-meta">
                                            <strong>{{ $comment->user->name }}</strong>
                                            <time datetime="{{ $comment->created_at->toAtomString() }}">{{ $comment->created_at->timezone('Africa/Lagos')->format('j M, g:i a') }}</time>
                                        </div>
                                        <p>{{ $comment->message }}</p>
                                    </div>
                                    @if ($currentUser && ($comment->user_id === $currentUser->id || $canManageBoard))
                                        <form method="POST" action="{{ route('birthdays.comments.destroy', $comment) }}" class="birthday-page__delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="birthday-page__delete" aria-label="Remove birthday wish"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                        </form>
                                    @endif
                                </article>
                            @empty
                                <div class="birthday-page__no-comments">
                                    <i class="far fa-comment-dots" aria-hidden="true"></i>
                                    <p>Be the first to send a birthday wish.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            @endif
        </div>
    </section>
</x-guest-layout>
