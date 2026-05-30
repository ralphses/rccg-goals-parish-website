<x-app-layout
    title="Create Event | RCCG GOALS Parish Admin"
    description="Create a new event in the RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Create Event, Church CMS, Event Management"
>
    <x-dashboard.events.create
        :departments="$departments"
        :statuses="$statuses"
    />
</x-app-layout>