<x-app-layout
    title="Departments | RCCG GOALS Parish Admin"
    description="Manage departments in the RCCG GOALS Parish Admin dashboard."
    keywords="Church Dashboard, RCCG Admin, Departments, Church CMS, Department Management"
>
    <x-dashboard.departments.index
        :departments="$departments"
    />
</x-app-layout>