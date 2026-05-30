<x-guest-layout 
    :title="$department->name . ' | The Redeemed Christian Church of God, GOALS Parish'"
    :description="$department->meta_description ?? Str::limit(strip_tags($department->description), 160)"
    :keywords="$department->meta_keywords ?? 'RCCG GOALS Parish Department, Ministry Team, Church Department'"
>
    <x-guest.department :department="$department" />
</x-guest-layout>