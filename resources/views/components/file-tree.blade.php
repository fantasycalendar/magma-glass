<nav class="mt-5 flex-1 px-2 bg-gray-50 dark:bg-gray-700 space-y-1" x-data="fileTree()">
    @if($contents->count())
        <ul>
            @foreach($contents as $item)
                <x-file-tree-item :contents="$item"></x-file-tree-item>
            @endforeach
        </ul>
    @else
        <ul>
            <li>
                No files in directory.
            </li>
        </ul>
    @endif
</nav>
