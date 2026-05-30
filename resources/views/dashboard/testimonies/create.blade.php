<x-app-layout
    title="Add Testimony | RCCG GOALS Parish Admin"
    description="Add a new testimony in the RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Add Testimony, Church CMS, Testimony Management"
>
    <x-dashboard.testimony.create
        :categories="$categories"
        :announcementTypes="$announcementTypes"
    />
</x-app-layout>