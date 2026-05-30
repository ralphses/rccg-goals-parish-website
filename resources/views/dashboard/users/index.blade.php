<x-app-layout
    title="Manage Users | RCCG GOALS Parish Admin"
    description="Administrative dashboard for managing RCCG GOALS Parish website content including media, events, testimonies, users, and analytics."
    keywords="Church Dashboard, RCCG Admin, Church CMS, Media Management, Events Management, Church Analytics"
>
    <x-dashboard.users.index
        :users="$users"
     >

    </x-dashboard.users.index>
</x-app-layout>