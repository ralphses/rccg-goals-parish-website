<x-app-layout
    title="Create Sermon | RCCG GOALS Parish Admin"
    description="Create a new sermon in the RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Create Sermon, Church CMS, Sermon Management"
>
    <x-dashboard.sermons.create
        :speakers="$speakers"
        :statuses="$statuses"
    />
</x-app-layout>