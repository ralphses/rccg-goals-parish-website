<x-app-layout
    title="Edit Event | RCCG GOALS Parish Admin"
    description="Edit event details in RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Edit Event, Church CMS, Event Management"
>
    <x-dashboard.events.edit
        :event="$event"
        :departments="$departments"
        :statuses="$statuses"
    />
</x-app-layout>