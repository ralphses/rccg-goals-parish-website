<x-guest-layout 
    :title="$event->title . ' | The Redeemed Christian Church of God, GOALS Parish'"
    :description="$event->meta_description ?? Str::limit(strip_tags($event->description), 160)"
    :keywords="$event->meta_keywords ?? 'RCCG GOALS Parish Event, Church Program, Christian Worship Event'"
>
 <x-guest.event :event="$event" />
</x-guest-layout>