<a href="javascript:"
   class="text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white group flex items items-center px-3 py-2 text-sm font-medium rounded-sm {{ $contents->has('children') ? 'has-children' : '' }}"
   @if($contents->has('children'))
       @click.prevent="toggleLevel($refs.{{ $contents->get('ref') }})"
   @elseif(Str::endsWith($contents->get('path'), ['png', 'jpg', 'jpeg', 'gif', 'tif', 'tiff']))
        @click="$dispatch('open-image', '{{ wikilink($contents->get('path')) }}')"
   @else
       @click="$dispatch('article-change', '{{ $contents->get('path') }}')"
   @endif
>
    <i class='fa {{ $contents->get('icon') ?? 'fa-folder' }} mr-3 text-center align-middle flex-shrink-0 h-100 w-100 inline-block w-4 h-4'></i>
    {{ $contents->get('title') }}
</a>

@if($contents->has('children'))
    <ul style="display:none;" x-ref="{{ $contents->get('ref') }}" class="pl-2 pb-1 ml-0 transition-all duration-150 opacity-0 list-none">
        @foreach($contents->get('children') as $item)
            <x-file-tree-item :contents="$item"></x-file-tree-item>
        @endforeach
    </ul>
@endif
