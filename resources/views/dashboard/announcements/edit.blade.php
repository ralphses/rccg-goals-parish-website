<x-app-layout 
    title="Edit Announcement | {{ $announcement->title }}"
    description="Edit the announcement '{{ $announcement->title }}' in the RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Announcements, Church CMS, Announcement Management, {{ $announcement->title }}"
>
    <x-dashboard.announcement.edit :announcement="$announcement" :frequencies="$frequencies" />
</x-app-layout>