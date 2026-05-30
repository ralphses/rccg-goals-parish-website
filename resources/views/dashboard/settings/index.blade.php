<x-app-layout
   title="Settings | {{ auth()->user()->name }}"
    description="Manage your account settings, including profile information, password, and notification preferences."
    keywords="account settings, profile management, password change, notification settings"
>
    <x-dashboard.settings.index
        :settings="$settings"
     >

    </x-dashboard.settings.index>
</x-app-layout>

