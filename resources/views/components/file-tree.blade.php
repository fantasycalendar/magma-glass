@once
    <script>
        let fileTree = function() {
            return {
                showLevel: function(el) {
                    if (el.style.length === 1 && el.style.display === 'none') {
                        el.removeAttribute('style')
                    } else {
                        el.style.removeProperty('display')
                    }
                    setTimeout(()=>{
                        el.previousElementSibling.querySelector('i.fa').classList.add("fa-folder-open");
                        el.previousElementSibling.querySelector('i.fa').classList.remove("fa-folder");
                        el.classList.add("opacity-100");
                    },10)
                },
                hideLevel: function(el) {
                    el.style.display = 'none';
                    el.classList.remove("opacity-100");
                    el.previousElementSibling.querySelector('i.fa').classList.remove("fa-folder-open");
                    el.previousElementSibling.querySelector('i.fa').classList.add("fa-folder");

                    let refs = el.querySelectorAll('ul[x-ref]');
                    for (var i = 0; i < refs.length; i++) {
                        this.hideLevel(refs[i]);
                    }
                },
                toggleLevel: function(el) {
                    console.log(el);
                    if( el.style.length && el.style.display === 'none' ) {
                        this.showLevel(el);
                    } else {
                        this.hideLevel(el);
                    }
                }
            }
        }
    </script>
@endonce

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
