<x-app-layout
    title="View Event | RCCG GOALS Parish Admin"
    description="View event details in RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Event Details, Church CMS, Event Management"
>
    <x-dashboard.events.show
        :event="$event"
    />
</x-app-layout>