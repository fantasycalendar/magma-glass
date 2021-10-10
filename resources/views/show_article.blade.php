<x-app-layout>
    <x-slot name="mobile_header">
        @if($isIndex)
            <strong>{{ config('app.name') }}</strong>
        @else
            {{ $article->name ?? 'Loading' }}
        @endif
    </x-slot>

    <x-slot name="header">
        @if($isIndex)
            <h1 class="text-4xl"><strong>{{ config('app.name') }}</strong></h1>
        @else
            <h1 class="text-4xl">{{ $article->name ?? 'Loading...' }}</h1>
        @endif
        <hr class="border-gray-300 dark:border-gray-700 my-8">
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden">
        <div class="px-0 bg-white dark:bg-gray-800" id="article-content">
{{--            {!! $article->getParsed() !!}--}}
        </div>
    </div>
</x-app-layout>
