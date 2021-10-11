<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $page_title ?? config('app.name', 'Laravel') }}</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">

        <script src="https://d3js.org/d3.v7.min.js"></script>
        <script src="{{ asset('js/app.js') }}" defer></script>

        <script>
            // On page load or when changing themes, best to add inline in `head` to avoid FOUC
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                window.theme = 'dark'
                document.documentElement.classList.add('dark')
            } else {
                window.theme = 'light'
                document.documentElement.classList.remove('dark')
                localStorage.theme = 'light'
            }

            window.toggleTheme = function() {
                let theme = (localStorage.theme === 'dark' ? 'light' : 'dark');
                window.theme = theme;
                localStorage.theme = theme;
                document.documentElement.classList.toggle('dark', theme === 'dark');

                console.log(window.theme);
                console.log(localStorage);
                console.log(document.documentElement.classList);
            }

            function setWithExpiry(key, value, ttl) {
                const item = {
                    value: value,
                    expiry: new Date().getTime() + ttl,
                }

                console.log("Storing " + key + " as:");
                console.log(item);
                localStorage.setItem(key, JSON.stringify(item))
            }

            function getWithExpiry(key) {
                const itemStr = localStorage.getItem(key)
                console.log("Retrieved " + key + " as:");
                console.log(itemStr);

                if (!itemStr) {
                    return null
                }

                const item = JSON.parse(itemStr)

                if (new Date().getTime() > item.expiry) {
                    localStorage.removeItem(key)
                    return null
                }
                return item.value
            }

            let app = {
                'sidebar': false,
                'loaded': false,
                'theme': localStorage.theme,
                'searchTerm': '',
                'article': {
                    title: 'Loading',
                    content: 'Loading...'
                },
                'searchResults': [],
                'showSearchResults': false,
                init() {
                    console.log('Initing');
                    let storageID = 'article_content_cache.' + decodeURI(location.pathname);

                    let storedArticle = getWithExpiry(storageID);
                    if(storedArticle !== null) {
                        this.article = {
                            title: storedArticle.title,
                            content: storedArticle.content,
                        }
                        this.loaded = true;
                        console.log("loaded from cache!");
                    }

                    console.log("Stored article was ");
                    console.log(storedArticle);
                },
                postInit() {
                    let currentItem;
                    let currentKey;

                    for (let i = 0; i < localStorage.length; i++){
                        currentItem = localStorage.getItem(localStorage.key(i));
                        currentKey = localStorage.key(i);

                        if (currentItem.includes("expiry")) {
                            getWithExpiry(currentKey);
                        }
                    }

                    if(!this.loaded) {
                        console.log("We didn't load from cache, fetch it.");
                        this.updateArticle(decodeURI(location.pathname).substr(1))
                        this.loaded = true;
                    }
                },
                updateArticle(path, back = false) {
                    console.log("Asked to update article to " + path);
                    if(path.endsWith('.md')) {
                        path = path.substr(0, path.length - 3);
                    }

                    let storedArticle = getWithExpiry('article_content_cache./' + path);

                    if (storedArticle !== null) {
                        console.log("Got an article!");
                        this.article = storedArticle;
                    } else {
                        axios.get('/get-article/', {
                            params: {
                                articlePath: path
                            }
                        }).then(response => {
                            if (!response.status === 200) alert(`Something went wrong: ${response.status} - ${response.statusText}`);

                            return response.data;
                        }).then(data => {
                            this.article = {
                                title: data.title,
                                content: data.content
                            }

                            setWithExpiry('article_content_cache.' + decodeURI(location.pathname), this.article, 300000);
                        });
                    }


                    if(!back && location.origin + '/' + path !== window.location.href) {
                        history.pushState(null, document.title, location.origin + '/' + path);
                    }

                    this.sidebar = false;
                },
                fetchSearchResults($event) {
                    axios.get('/search/', {
                        params: {
                            searchTerm: this.searchTerm
                        }
                    }).then(response => {
                        if (!response.status === 200) alert(`Something went wrong: ${response.status} - ${response.statusText}`);

                        return response.data;
                    }).then(data => {
                        this.searchResults = data;
                        console.log(data);
                    });

                }
            }
        </script>
    </head>
    <body id="app" class="font-sans antialiased" x-data="app" x-init="$nextTick(() => {postInit();});" @article-change.window="updateArticle($event.detail)" @popstate.window="updateArticle(decodeURI(location.pathname).substring(1), true)">
        <div class="h-screen flex overflow-hidden bg-white dark:bg-gray-800">
            <div class="fixed inset-0 flex z-40 md:hidden" role="dialog" aria-modal="true" :class="{ 'pointer-events-none': !sidebar }">
                <div class="fixed inset-0 bg-gray-400 dark:bg-gray-600 bg-opacity-75 transition-opacity ease-linear duration-300" aria-hidden="true" :class="{ 'opacity-100': sidebar, 'opacity-0': !sidebar }"  @click="sidebar = !sidebar" x-cloak></div>

                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-700 transition ease-in-out duration-300 transform" :class="{ 'translate-x-0': sidebar, '-translate-x-full': !sidebar }" x-cloak>
                    <div class="absolute top-0 right-0 -mr-12 pt-2" :class="{ 'hidden': !sidebar }">
                        <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="sidebar = !sidebar">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                        <div class="flex-shrink-0 flex items-center px-4 text-center">
                            <img class="h-18 mx-auto hidden dark:inline" src="{{ asset('images/logo.png') }}" alt="Magma Glass">
                            <img class="h-18 mx-auto dark:hidden" src="{{ asset('images/logo-dark.png') }}" alt="Magma Glass">
                        </div>
                        <x-file-tree></x-file-tree>
                    </div>
                    <div class="flex-shrink-0 flex justify-between align-middle bg-white dark:bg-gray-700 text-gray-400 font-medium dark:font-light p-4">
                        <div class="grid place-items-center" x-html="article.title"></div>
                        <div>
                            <i class="fa cursor-pointer p-2 border dark:border-gray-600 rounded dark:bg-gray-700 dark:hover:bg-gray-800 transition-all ease-linear duration-300" @click="window.toggleTheme()" :class="{ 'fa-moon': theme === 'dark', 'fa-sun': theme === 'light' }"></i>
                        </div>
                    </div>
                </div>

                <div class="flex-shrink-0 w-14"></div> <!-- Force sidebar to shrink to fit close icon -->
            </div>

            <div class="hidden md:flex md:flex-shrink-0">
                <div class="flex flex-col w-64 md:w-80 2xl:w-96">
                    <div class="flex-1 flex flex-col min-h-0 bg-gray-50 dark:bg-gray-700 border-r border-gray-200 dark:border-gray-600">
                        <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                            <div class="flex items-center flex-shrink-0 px-4 text-center">
                                <img class="h-18 mx-auto hidden dark:inline" src="{{ asset('images/logo.png') }}" alt="Magma Glass">
                                <img class="h-18 mx-auto dark:hidden" src="{{ asset('images/logo-dark.png') }}" alt="Magma Glass">
                            </div>
                            <x-article-search :keyboardShortcut="true"></x-article-search>
                            <x-file-tree></x-file-tree>
                        </div>
                        <div class="flex-shrink-0 flex justify-between align-middle bg-gray-50 dark:bg-gray-700 text-gray-400 font-medium dark:font-light p-4">
                            <div class="grid place-items-center">
                                {{ $page_name ?? config('app.name') }}
                            </div>
                            <div>
                                <i class="fa cursor-pointer p-2 border dark:border-gray-600 rounded dark:bg-gray-700 dark:hover:bg-gray-600 transition-all ease-linear duration-300" @click="window.toggleTheme()" :class="{ 'fa-moon': theme === 'dark', 'fa-sun': theme === 'light' }"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex flex-col w-0 flex-1 overflow-hidden">
                <div class="md:hidden flex justify-start align-items-middle pl-1 pt-1 sm:pl-3 sm:pt-3 bg-gray-100 shadow dark:shadow-none dark:bg-gray-700">
                    <button type="button" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" @click="sidebar = !sidebar">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="grid place-items-center text-gray-700 dark:text-white flex-grow-1 overflow-x-scroll text-2xl flex-nowrap whitespace-nowrap pr-4">
                        {{ $mobile_header }}
                    </div>
                </div>
                <main class="bg-white dark:bg-gray-800 flex-1 relative z-0 overflow-y-auto focus:outline-none">
                    <div class="py-6 text-gray-700 dark:text-white">
                        <div class="max-w-7xl mx-auto px-4 hidden md:block sm:px-6 lg:px-8">
                            <h1 class="text-4xl" x-html="article.title"><strong>{{ config('app.name') }}</strong></h1>
                        </div>
                        <hr class="max-w-7xl mx-auto px-4 hidden md:block sm:px-6 lg:px-8 border-gray-300 dark:border-gray-700 my-8">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8" id="article-content" x-html="article.content"></div>
                    </div>
                </main>
            </div>
        </div>

        <div class="image-modal-wrapper absolute top-0 left-0 right-0 bottom-0 bg-gray-500 dark:bg-gray-900 dark:bg-opacity-80 bg-opacity-70 max-h-screen max-w-screen" x-show="showingImage" x-transition x-data="{ showingImage: false, shownImage: '', showImage: function($event) { console.log($event); this.shownImage = $event.detail; this.showingImage = true; console.log(this); }, hideImage: function($event) { console.log($event); this.showingImage = false; this.image = ''; } }" @open-image.window="showImage" @close-image.window="hideImage" x-cloak>
            <div id="image-modal" class="min-h-full min-w-full max-w-screen max-h-screen overflow-y-auto grid place-items-center cursor-pointer">
                <i class="fa fa-times p-6 text-gray-900 dark:text-gray-200 cursor-pointer absolute top-0 right-0" @click="hideImage"></i>
                <img :src="shownImage" alt="" class="m-6 cursor-default shadow-lg" @click.away="hideImage">
            </div>
        </div>
    </body>
</html>
