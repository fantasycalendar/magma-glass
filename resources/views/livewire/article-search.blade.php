<div class="mx-5 pt-4 relative" x-data="{
                showSearchResults: @entangle('showSearchResults')
           }" @click.away="showSearchResults = false">
    <label for="search" class="sr-only">Search</label>
    <input type="search" name="search" id="search"
           class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600"
           placeholder="Search (Ctrl+K)"
           wire:model="searchTerm"
           @focusin="showSearchResults = true"
    >

    @if($showSearchResults)
        <div class="absolute bg-white border dark:bg-gray-800 dark:border-gray-600 rounded shadow-lg w-full">
            <ul>
                @if(strlen($searchTerm))
                    @foreach($searchResults as $result)
                        <li class="px-4 py-2 cursor-pointer text-gray-500 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600" wire:click="$emit('changeArticle', $result['title'])">{{ $result['title'] }}</li>
                    @endforeach

                    @if(!count($searchResults))
                        <li class="px-4 py-2 text-gray-500 dark:text-gray-400">No results for '{{ $searchTerm }}'</li>
                    @endif
                @else
                    <li class="px-4 py-2 text-gray-500 dark:text-gray-400">Search results will appear here</li>
                @endif
            </ul>
        </div>
    @endif
</div>
