<x-app-layout
    title="Edit Department | RCCG GOALS Parish Admin"
    description="Edit department details in the RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Edit Department, Church CMS, Department Management"
>
    <x-dashboard.departments.edit
        :department="$department"
        :users="$users"
    />
</x-app-layout>
