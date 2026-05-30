<x-app-layout
    title="View User | RCCG GOALS Parish Admin"
    description="View user details in RCCG GOALS Parish Admin dashboard including departments, role, and status."
    keywords="Church Dashboard, RCCG Admin, User Details, Church CMS, User Management"
>
    <x-dashboard.users.show
        :user="$user"
    />
</x-app-layout>