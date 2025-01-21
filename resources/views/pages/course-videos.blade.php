
<x-app-layout>
@livewireStyles
    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-500 leading-tight">
            {{__("Videos")}}
        </h2>
    </x-slot>
<livewire:video-player :video="$video"/>
@livewireScripts
</x-app-layout>
