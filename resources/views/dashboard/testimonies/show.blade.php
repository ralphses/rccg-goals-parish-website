<x-app-layout 
    title="View Testimony | {{ $testimony->title }}"
    description="View the testimony from {{ $testimony->testifier_name }} in the RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Testimonies, Church CMS, Testimony Management, {{ $testimony->title }}"
>
    <x-dashboard.testimony.show :testimony="$testimony" />
</x-app-layout>