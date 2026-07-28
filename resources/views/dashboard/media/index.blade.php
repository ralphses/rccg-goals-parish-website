<x-app-layout
    title="Manage Media | RCCG GOALS Parish Admin"
    description="Admin panel for managing church media at RCCG GOALS Parish. Upload, view, and organize images and other media files."
    keywords="RCCG Media Admin, Manage Church Media, RCCG GOALS Parish Media, Church Media Management"
>
    <x-dashboard.media.index :media="$media" :categories="$categories" :youtube-connected="$youtubeConnected" :queued-video-count="$queuedVideoCount" :background-upload-count="$backgroundUploadCount" />
</x-app-layout>
