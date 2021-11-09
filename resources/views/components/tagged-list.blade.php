<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden sm:rounded-md">
    <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach($articles as $article)
            <li>
                <a href="{{ route('article', Str::substr($article['path'], 0, Str::length($article['path']) - 3)) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-blue-400 truncate">
                                {{ $article['title'] }}
                            </p>
                            <div class="ml-2 flex-shrink-0 flex">
                                @isset($article['tags'])
                                    @foreach($article['tags'] as $tag)
                                        <p class="px-2 mr-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">
                                            {{ $tag }}
                                        </p>
                                    @endforeach
                                @endisset
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <!-- Heroicon name: solid/users -->
                                    <i class="fab fa-markdown mr-2"></i>
                                    {{ $article['path'] }}
                                </p>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500 dark:text-gray-400 sm:mt-0">
                                <!-- Heroicon name: solid/calendar -->
                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                </svg>
                                <p>
                                    Created On
                                    <time datetime="{{ $article['last_modified']->format('Y-m-d') }}">{{ $article['last_modified']->format('F m, Y') }}</time>
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
</div>
