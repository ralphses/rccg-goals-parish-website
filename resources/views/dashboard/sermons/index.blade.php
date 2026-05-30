<x-app-layout
    title="Sermons | RCCG GOALS Parish Admin"
    description="Manage sermons in the RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Sermons, Church CMS, Sermon Management"
>
    <x-dashboard.sermons.index
        :sermons="$sermons"
    />
</x-app-layout>