<x-app-layout
    title="Edit User | RCCG GOALS Parish Admin"
    description="Edit user details including role, status, and department assignments in RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Edit User, Church CMS, User Management"
>
    <x-dashboard.users.edit
        :user="$user"
        :roles="$roles"
        :departments="$departments"
        :states="$states"
    />
</x-app-layout>