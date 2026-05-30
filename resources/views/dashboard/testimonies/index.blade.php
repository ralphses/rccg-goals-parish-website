<x-app-layout
    title="Testimonies | RCCG GOALS Parish Admin"
    description="Manage testimonies in the RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Testimonies, Church CMS, Testimony Management"
>
    <x-dashboard.testimony.index
        :testimonies="$testimonies"
    />
</x-app-layout>