<x-app-layout 
    title="Upload Media | RCCG GOALS Parish Admin" 
    description="Upload church media including images, video, and audio to the RCCG GOALS Parish portal." 
    keywords="Church Dashboard, RCCG Admin, Upload Media, Media Manager" 
> 
    <x-dashboard.media.create 
        :categories="$categories" 
        :youtube-connected="$youtubeConnected"
    /> 
</x-app-layout>
