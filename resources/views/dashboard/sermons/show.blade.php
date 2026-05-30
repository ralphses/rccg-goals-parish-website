<x-app-layout
    title="{{ $sermon->title }} | RCCG GOALS Parish Admin"
    description="View sermon details in the RCCG GOALS Parish Admin dashboard."
>
    <x-dashboard.sermons.show
        :sermon="$sermon"
    />
</x-app-layout>