<x-app-layout 
    title="Edit Testimony | {{ $testimony->title }}"
    description="Edit the testimony from {{ $testimony->testifier_name }} in the RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Testimonies, Church CMS, Testimony Management, {{ $testimony->title }}"
>
    <x-dashboard.testimony.edit :testimony="$testimony" :announcementTypes="$announcementTypes" />
</x-app-layout>