<x-app-layout
    title="Manage Events | RCCG GOALS Parish Admin"
    description="Admin panel for managing church events at RCCG GOALS Parish. Create, edit, update, and organize upcoming services, conferences, outreach programs, and special church events."
    keywords="RCCG Events Admin, Manage Church Events, RCCG GOALS Parish Events, Church Event Management, Christian Programs Admin"
>
    <x-dashboard.events.index :events="$events" />
</x-app-layout>