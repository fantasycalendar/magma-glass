<x-app-layout>
    <x-slot name="mobile_header">
        <h1>Articles tagged <strong>{{ $tagSearch }}</strong></h1>
    </x-slot>

    <x-slot name="header">
        <h1>Articles tagged <strong>{{ $tagSearch }}</strong></h1>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden">
        <div class="px-0 bg-white dark:bg-gray-800">
            <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <x-tagged-list :articles="$results"/>
            </div>
        </div>
    </div>
</x-app-layout>
