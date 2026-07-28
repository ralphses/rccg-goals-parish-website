<x-app-layout 
    title="Edit Media | RCCG GOALS Parish Admin" 
    description="Edit media details including title, category, and visibility in RCCG GOALS Parish Admin dashboard." 
    keywords="Church Dashboard, RCCG Admin, Edit Media, Church CMS, Media Management" 
> 
    <x-dashboard.media.edit 
        :media="$media" 
        :categories="$categories" 
        :youtube-connected="$youtubeConnected"
    /> 
</x-app-layout>
