<?php

namespace App\Services;

use App\enums\EventStatus;
use App\enums\SermonStatus;
use App\enums\UserStatus;
use App\Models\Department;
use App\Models\Event;
use App\Models\Media;
use App\Models\Sermon;
use App\Support\MediaUrl;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class SeoService
{
    public function page(array $overrides = []): array
    {
        $defaultImage = asset(config('seo.default_share_image'));
        $canonical = $overrides['canonical'] ?? url()->full();

        return [
            'title' => $overrides['title'] ?? config('seo.site_name'),
            'description' => $overrides['description'] ?? config('seo.default_description'),
            'keywords' => is_array($overrides['keywords'] ?? null)
                ? implode(', ', array_filter($overrides['keywords']))
                : ($overrides['keywords'] ?? config('seo.default_keywords')),
            'canonical' => $canonical,
            'robots' => $overrides['robots'] ?? 'index,follow',
            'image' => $this->absoluteUrl($overrides['image'] ?? $defaultImage) ?? $defaultImage,
            'type' => $overrides['type'] ?? 'website',
            'site_name' => config('seo.site_name'),
            'site_short_name' => config('seo.site_short_name'),
            'twitter_card' => 'summary_large_image',
            'prev' => $overrides['prev'] ?? null,
            'next' => $overrides['next'] ?? null,
            'schema' => array_merge($this->siteSchemas(), $overrides['schema'] ?? []),
        ];
    }

    public function home(): array
    {
        return $this->page([
            'title' => 'Church in Ajah, Lagos | RCCG GOALS Parish',
            'description' => 'Join RCCG GOALS Parish in Ajah, Lagos for worship, Bible teaching, prayer, youth fellowship, and uplifting church events with a strong local community focus.',
            'keywords' => 'RCCG GOALS Parish, church in Ajah Lagos, worship centre Ajah, Bible teaching church Lagos, Christian fellowship Ajah, live stream church Lagos',
            'canonical' => route('home'),
            'schema' => [$this->churchSchema()],
        ]);
    }

    public function about(): array
    {
        return $this->page([
            'title' => 'About RCCG GOALS Parish | Ajah, Lagos',
            'description' => 'Learn about RCCG GOALS Parish in Ajah, Lagos, our mission, worship culture, leadership, and commitment to raising lives through the Gospel of Jesus Christ.',
            'keywords' => 'about RCCG GOALS Parish, Ajah church, Lagos Christian church, church vision, church mission',
            'canonical' => route('about'),
        ]);
    }

    public function contact(): array
    {
        return $this->page([
            'title' => 'Contact RCCG GOALS Parish | Ajah, Lagos',
            'description' => 'Contact RCCG GOALS Parish in Ajah, Lagos for worship enquiries, prayer requests, service information, ministry questions, and location details.',
            'keywords' => 'contact RCCG GOALS Parish, church contact Ajah, Lagos church address, prayer request church',
            'canonical' => route('contact'),
            'schema' => [[
                '@context' => 'https://schema.org',
                '@type' => 'ContactPage',
                'name' => 'Contact RCCG GOALS Parish',
                'url' => route('contact'),
                'about' => ['@id' => route('home') . '#church'],
            ]],
        ]);
    }

    public function sermonsIndex(LengthAwarePaginator $videoSermons, LengthAwarePaginator $audioSermons): array
    {
        return $this->page(array_filter([
            'title' => 'Sermons | RCCG GOALS Parish',
            'description' => 'Watch and listen to Bible-based sermons from RCCG GOALS Parish in Ajah, Lagos, including YouTube video messages and audio teachings.',
            'keywords' => 'RCCG GOALS Parish sermons, online sermons Lagos, Bible teaching videos, church audio messages',
            'canonical' => route('sermons'),
            'prev' => $videoSermons->previousPageUrl() ?: $audioSermons->previousPageUrl(),
            'next' => $videoSermons->nextPageUrl() ?: $audioSermons->nextPageUrl(),
        ], fn ($value) => filled($value)));
    }

    public function sermon(Sermon $sermon): array
    {
        $description = $this->fallbackDescription($sermon->meta_description, $sermon->description, $sermon->message);
        $image = $sermon->cover_image_url ?: asset(config('seo.default_share_image'));

        return $this->page([
            'title' => $sermon->meta_title ?: ($sermon->title . ' | RCCG GOALS Parish'),
            'description' => $description,
            'keywords' => $sermon->meta_keywords ?: 'RCCG GOALS Parish sermon, Christian sermon, Bible teaching, Ajah Lagos church sermon',
            'canonical' => route('sermons.show', $sermon->slug),
            'image' => $image,
            'type' => 'article',
            'schema' => [[
                '@context' => 'https://schema.org',
                '@type' => $sermon->youtube_embed_url || $sermon->video_url ? 'VideoObject' : 'Article',
                'name' => $sermon->title,
                'description' => $description,
                'url' => route('sermons.show', $sermon->slug),
                'thumbnailUrl' => [$image],
                'datePublished' => optional($sermon->published_at ?? $sermon->sermon_date)->toAtomString(),
                'image' => $image,
                'embedUrl' => $sermon->youtube_embed_url,
                'contentUrl' => $sermon->public_video_url,
                'author' => [
                    '@type' => 'Person',
                    'name' => $sermon->speaker->name ?? 'RCCG GOALS Parish',
                ],
                'publisher' => [
                    '@id' => route('home') . '#organization',
                ],
            ]],
        ]);
    }

    public function eventsIndex(LengthAwarePaginator $events): array
    {
        return $this->page(array_filter([
            'title' => 'Events | RCCG GOALS Parish',
            'description' => 'Stay updated with worship services, conferences, outreach programs, and church events from RCCG GOALS Parish in Ajah, Lagos.',
            'keywords' => 'RCCG GOALS Parish events, church events Ajah, Lagos worship events, Christian programs Lagos',
            'canonical' => route('events'),
            'prev' => $events->previousPageUrl(),
            'next' => $events->nextPageUrl(),
        ], fn ($value) => filled($value)));
    }

    public function event(Event $event): array
    {
        $description = $this->fallbackDescription($event->meta_description, $event->description);
        $image = $event->image_url ?: asset(config('seo.default_share_image'));

        return $this->page([
            'title' => $event->meta_title ?: ($event->title . ' | RCCG GOALS Parish Event'),
            'description' => $description,
            'keywords' => $event->meta_keywords ?: 'RCCG GOALS Parish event, church program, worship event, Ajah Lagos',
            'canonical' => route('event', $event),
            'image' => $image,
            'type' => 'article',
            'schema' => [[
                '@context' => 'https://schema.org',
                '@type' => 'Event',
                'name' => $event->title,
                'description' => $description,
                'startDate' => optional($event->event_date)->toAtomString(),
                'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
                'eventStatus' => $this->eventStatusSchema($event->status?->value ?? $event->status),
                'image' => [$image],
                'location' => [
                    '@type' => 'Place',
                    'name' => $event->location ?: config('seo.site_short_name'),
                    'address' => $this->postalAddress(),
                ],
                'organizer' => ['@id' => route('home') . '#organization'],
                'url' => route('event', $event),
            ]],
        ]);
    }

    public function departmentsIndex(LengthAwarePaginator $departments): array
    {
        return $this->page(array_filter([
            'title' => 'Departments | RCCG GOALS Parish',
            'description' => 'Explore active ministries and departments at RCCG GOALS Parish in Ajah, Lagos, and learn how each team serves the church community.',
            'keywords' => 'RCCG GOALS Parish departments, church ministries Ajah, church teams Lagos',
            'canonical' => route('departments'),
            'prev' => $departments->previousPageUrl(),
            'next' => $departments->nextPageUrl(),
        ], fn ($value) => filled($value)));
    }

    public function department(Department $department): array
    {
        $description = $this->fallbackDescription($department->meta_description, $department->description);
        $image = $department->image_url ?: asset(config('seo.default_share_image'));

        return $this->page([
            'title' => $department->meta_title ?: ($department->name . ' | RCCG GOALS Parish Department'),
            'description' => $description,
            'keywords' => $department->meta_keywords ?: 'RCCG GOALS Parish department, church ministry, Ajah Lagos church team',
            'canonical' => route('department', $department),
            'image' => $image,
            'type' => 'article',
            'schema' => [[
                '@context' => 'https://schema.org',
                '@type' => 'AboutPage',
                'name' => $department->name,
                'description' => $description,
                'url' => route('department', $department),
                'primaryImageOfPage' => $image,
                'about' => [
                    '@type' => 'Organization',
                    'name' => $department->name,
                ],
            ]],
        ]);
    }

    public function media(LengthAwarePaginator $galleryMedia, LengthAwarePaginator $testimonyMedia): array
    {
        return $this->page(array_filter([
            'title' => 'Media | RCCG GOALS Parish',
            'description' => 'Browse church gallery moments, testimonies, and ministry visuals from RCCG GOALS Parish in Ajah, Lagos.',
            'keywords' => 'RCCG GOALS Parish media, church gallery, testimonies, worship photos Lagos',
            'canonical' => route('media'),
            'prev' => $galleryMedia->previousPageUrl() ?: $testimonyMedia->previousPageUrl(),
            'next' => $galleryMedia->nextPageUrl() ?: $testimonyMedia->nextPageUrl(),
        ], fn ($value) => filled($value)));
    }

    public function fallbackDescription(?string ...$values): string
    {
        foreach ($values as $value) {
            $clean = trim(strip_tags((string) $value));

            if ($clean !== '') {
                return Str::limit($clean, 160);
            }
        }

        return config('seo.default_description');
    }

    public function publicSitemapUrls(): array
    {
        $urls = [
            ['loc' => route('home'), 'lastmod' => now()],
            ['loc' => route('about'), 'lastmod' => null],
            ['loc' => route('contact'), 'lastmod' => null],
            ['loc' => route('sermons'), 'lastmod' => Sermon::query()->where('status', SermonStatus::PUBLISHED->value)->max('updated_at')],
            ['loc' => route('events'), 'lastmod' => Event::query()->where('status', '!=', EventStatus::CANCELLED->value)->max('updated_at')],
            ['loc' => route('departments'), 'lastmod' => Department::query()->where('status', UserStatus::ACTIVE->value)->max('updated_at')],
            ['loc' => route('media'), 'lastmod' => Media::query()->where('is_public', true)->max('updated_at')],
        ];

        foreach (Sermon::query()->where('status', SermonStatus::PUBLISHED->value)->where(function ($query) {
            $query->whereNotNull('video_url')->orWhereNotNull('audio_url');
        })->get(['slug', 'updated_at']) as $sermon) {
            $urls[] = ['loc' => route('sermons.show', $sermon->slug), 'lastmod' => $sermon->updated_at];
        }

        foreach (Event::query()->where('status', '!=', EventStatus::CANCELLED->value)->get(['id', 'updated_at']) as $event) {
            $urls[] = ['loc' => route('event', $event), 'lastmod' => $event->updated_at];
        }

        foreach (Department::query()->where('status', UserStatus::ACTIVE->value)->get(['id', 'updated_at']) as $department) {
            $urls[] = ['loc' => route('department', $department), 'lastmod' => $department->updated_at];
        }

        return $urls;
    }

    public function robotsTxt(): string
    {
        return implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /dashboard',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            'Disallow: /profile',
            'Disallow: /verify-email',
            'Sitemap: ' . route('sitemap'),
            '',
        ]);
    }

    private function siteSchemas(): array
    {
        $home = route('home');
        $logo = asset('assets/images/resources/goals_logo_real.png');
        $sameAs = config('seo.organization.same_as', []);

        return [
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                '@id' => $home . '#website',
                'url' => $home,
                'name' => config('seo.site_name'),
                'inLanguage' => 'en',
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                '@id' => $home . '#organization',
                'name' => config('seo.site_name'),
                'url' => $home,
                'logo' => $logo,
                'email' => config('seo.organization.email'),
                'telephone' => config('seo.organization.phone'),
                'sameAs' => empty($sameAs) ? null : $sameAs,
                'address' => $this->postalAddress(),
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'Church',
                '@id' => $home . '#church',
                'name' => config('seo.site_short_name'),
                'url' => $home,
                'image' => $logo,
                'logo' => $logo,
                'email' => config('seo.organization.email'),
                'telephone' => config('seo.organization.phone'),
                'address' => $this->postalAddress(),
            ],
        ];
    }

    private function churchSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Church',
            'name' => config('seo.site_name'),
            'url' => route('home'),
            'email' => config('seo.organization.email'),
            'telephone' => config('seo.organization.phone'),
            'address' => $this->postalAddress(),
        ];
    }

    private function postalAddress(): array
    {
        return [
            '@type' => 'PostalAddress',
            'streetAddress' => config('seo.organization.street_address'),
            'addressLocality' => config('seo.organization.locality'),
            'addressRegion' => config('seo.organization.region'),
            'addressCountry' => config('seo.organization.country'),
        ];
    }

    private function eventStatusSchema(?string $status): string
    {
        return match ($status) {
            EventStatus::CANCELLED->value => 'https://schema.org/EventCancelled',
            EventStatus::COMPLETED->value => 'https://schema.org/EventCompleted',
            default => 'https://schema.org/EventScheduled',
        };
    }

    private function absoluteUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        return MediaUrl::toPublicUrl($url);
    }
}
