<x-app-layout
    title="Edit {{ $sermon->title }} | RCCG GOALS Parish Admin"
    description="Edit sermon details in the RCCG GOALS Parish Admin dashboard."
>
    <x-dashboard.sermons.edit
        :sermon="$sermon"
        :speakers="$speakers"
        :statuses="$statuses"
        :media-library="$mediaLibrary"
    />
</x-app-layout>
