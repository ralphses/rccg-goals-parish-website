<x-app-layout
    title="View Department | RCCG GOALS Parish Admin"
    description="View department details in the RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, View Department, Church CMS, Department Management"
>
    <x-dashboard.departments.show
        :department="$department"
    />
</x-app-layout>