<x-app-layout 
    title="View Announcement | {{ $announcement->title }}"
    description="View the announcement '{{ $announcement->title }}' in the RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Announcements, Church CMS, Announcement Management, {{ $announcement->title }}"
>
    <x-dashboard.announcement.show :announcement="$announcement" />
</x-app-layout>