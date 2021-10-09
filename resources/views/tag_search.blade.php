<x-app-layout>
    <x-slot name="mobile_header">
        <h1>Articles tagged <strong>{{ $tagSearch }}</strong></h1>
    </x-slot>

    <x-slot name="header">
        <h1>Articles tagged <strong>{{ $tagSearch }}</strong></h1>
        <hr class="border-gray-300 dark:border-gray-700 my-8">
    </x-slot>

    <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <x-tagged-list :articles="$results"/>
{{--            @foreach($results as $result)--}}
{{--                <div class="transform transform-gpu relative bg-gray-100 dark:bg-gray-800 p-4 my-8 rounded-lg shadow hover:-translate-y-1 hover:shadow-lg transition duration-150 hover:bg-gray-50 dark:hover:bg-gray-600">--}}
{{--                    <h2>{{ $result['title'] }}</h2>--}}
{{--                    <div class="flex">--}}
{{--                        @foreach($result['tags'] as $tag)--}}
{{--                            <span class="border border-white mr-1.5 dark:border-gray-800 hover:bg-white dark:hover:bg-gray-600 rounded bg-gray-100 dark:bg-gray-700 px-1">--}}
{{--                                <a class="dark:text-blue-400" href="{{ route('tag', ['tag' => $tag]) }}">{{ $tag }}</a>--}}
{{--                            </span>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            @endforeach--}}
    </div>
</x-app-layout>
