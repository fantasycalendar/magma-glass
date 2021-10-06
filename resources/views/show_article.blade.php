<x-app-layout>
    <x-slot name="header">
        @if($isIndex)
            <h1 class="text-4xl"><strong>{{ config('app.name') }}</strong></h1>
        @else
            <h1 class="text-4xl">{{ $article->name }}</h1>
        @endif
        <hr class="border-gray-700 my-8">
    </x-slot>

    <div class="bg-gray-700 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-gray-700">
            {!! $article->getParsed() !!}
        </div>
    </div>
</x-app-layout>
