<x-app-layout
    title="Create User | RCCG GOALS Parish Admin"
    description="Administrative dashboard for managing RCCG GOALS Parish website content including media, events, testimonies, users, and analytics."
    keywords="Church Dashboard, RCCG Admin, Church CMS, Media Management, Events Management, Church Analytics"
>
    <x-dashboard.users.create
        :roles="$roles"
        :departments="$departments"
        :states="$states"
     >

    </x-dashboard.users.create>
</x-app-layout>