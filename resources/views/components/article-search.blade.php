<div class="mx-5 pt-4 relative">
    <label for="email" class="sr-only">Email</label>
    <input type="text" name="email" id="email"
           class="shadow-sm focus:ring-teal-500 focus:border-teal-500 block w-full sm:text-sm border-gray-300 rounded dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600"
           x-model="searchTerm"
           @input.debounce="fetchSearchResults"
           @focusin="showSearchResults = true"
           @keyup.enter="if(searchResults.length) { updateArticle(searchResults[0].path); $event.srcElement.blur(); }"
           @if(isset($keyboardShortcut) && $keyboardShortcut)
               @focus-search.window="$el.focus(); $el.select();"
               placeholder="Search (Ctrl+K)"
           @else
                placeholder="Search"
           @endif
    >
    <div class="absolute bg-white border dark:bg-gray-800 dark:border-gray-600 rounded shadow-lg w-full" x-show.transition="showSearchResults">
        <ul>
            <template x-for="result in searchResults">
                <li class="px-4 py-2 cursor-pointer text-gray-500 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600" @click="updateArticle(result.path); $nextTick(() => showSearchResults = false)" x-text="result.title"></li>
            </template>
            <li x-show="!searchTerm.length" class="px-4 py-2 text-gray-500 dark:text-gray-400">Search results will appear here</li>
            <li x-show="(searchTerm.length && !searchResults.length)" class="px-4 py-2 text-gray-500 dark:text-gray-400">No results for '<span x-text="searchTerm"></span>'</li>
        </ul>
    </div>
</div>
