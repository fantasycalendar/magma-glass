<template>
    <div id="app" class="font-sans antialiased" @article-change="updateArticle($event.detail)">
        <div class="h-screen flex overflow-hidden bg-white dark:bg-gray-800">
            <div class="fixed inset-0 flex z-40 md:hidden" role="dialog" aria-modal="true" :class="{ 'pointer-events-none': !sidebar }" v-cloak>
                <div class="fixed inset-0 bg-gray-400 dark:bg-gray-600 bg-opacity-75 transition-opacity ease-linear duration-300" aria-hidden="true" :class="{ 'opacity-100': sidebar, 'opacity-0': !sidebar }"  @click="sidebar = !sidebar" v-cloak></div>

                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-700 transition ease-in-out duration-300 transform" :class="{ 'translate-x-0': sidebar, '-translate-x-full': !sidebar }" v-cloak>
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
                            <img class="h-18 mx-auto hidden dark:inline" src="/images/logo.png" alt="Magma Glass">
                            <img class="h-18 mx-auto dark:hidden" src="/images/logo-dark.png" alt="Magma Glass">
                        </div>
<!--                        <x-article-search></x-article-search>-->
<!--                        <x-file-tree></x-file-tree>-->
                    </div>
                    <div class="flex-shrink-0 flex justify-between align-middle bg-white dark:bg-gray-700 text-gray-400 font-medium dark:font-light p-4">
                        <div class="grid place-items-center" v-html="article.title"></div>
                        <div>
<!--                            <i class="fa cursor-pointer p-2 border dark:border-gray-600 rounded dark:bg-gray-700 dark:hover:bg-gray-800 transition-all ease-linear duration-300" @click="window.toggleTheme" :class="{ 'fa-moon': theme === 'dark', 'fa-sun': theme === 'light' }"></i>-->
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
                                <img class="h-18 mx-auto hidden dark:inline" src="/images/logo.png" alt="Magma Glass">
                                <img class="h-18 mx-auto dark:hidden" src="/images/logo-dark.png" alt="Magma Glass">
                            </div>
<!--                            <x-article-search :keyboardShortcut="true"></x-article-search>-->
<!--                            <x-file-tree></x-file-tree>-->
                        </div>
                        <div class="flex-shrink-0 flex justify-between align-middle bg-gray-50 dark:bg-gray-700 text-gray-400 font-medium dark:font-light p-4">
                            <div class="grid place-items-center">
                            </div>
                            <div>
<!--                                <i class="fa cursor-pointer p-2 border dark:border-gray-600 rounded dark:bg-gray-700 dark:hover:bg-gray-600 transition-all ease-linear duration-300" @click="window.toggleTheme" :class="{ 'fa-moon': theme === 'dark', 'fa-sun': theme === 'light' }"></i>-->
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
                    <div class="grid place-items-center text-gray-700 dark:text-white flex-grow-1 overflow-x-scroll text-2xl flex-nowrap whitespace-nowrap pr-4" v-html="article.title">
                    </div>
                </div>
                <main class="bg-white dark:bg-gray-800 flex-1 relative z-0 overflow-y-auto focus:outline-none">
                    <div class="py-6 text-gray-700 dark:text-white">
                        <div class="max-w-7xl mx-auto px-4 hidden md:block sm:px-6 lg:px-8">
                            <h1 class="text-4xl font-bold" v-html="article.title">
                            </h1>
                            <hr class="max-w-7xl mx-auto px-4 hidden md:block sm:px-6 lg:px-8 border-gray-300 dark:border-gray-700 my-8">
                        </div>
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8" id="article-content" v-html="article.content">
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <image-modal></image-modal>
    </div>
</template>

<script>
import './ImageModal'

export default {
    name: 'Layout',
    data() {
        return {
            sidebar: false,
            loaded: false,
            theme: '',
            searchTerm: '',
            article: {
                title: 'Loading',
                content: 'Loading...'
            },
            articleUpdated: false,
            searchResults: [],
            showSearchResults: false,
        }
    },
    popstate() {
        updateArticle(decodeURI(location.pathname).substring(2), true);
    },
    created() {
        window.addEventListener('popstate',this.popstate);
    },
    mounted() {
        console.log('Initing');
        if(!location.pathname.startsWith('/a')) {
            console.log(location.pathname);
            this.loaded = true;
            return;
        }

        this.theme = localStorage.theme;

        let storageID = 'article_content_cache.' + decodeURI(location.pathname.substring(2));

        let storedArticle = this.getWithExpiry(storageID);
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
    methods: {
        postInit() {
            let currentItem;
            let currentKey;

            for (let i = 0; i < localStorage.length; i++){
                currentItem = localStorage.getItem(localStorage.key(i));
                currentKey = localStorage.key(i);

                if (currentItem.includes("expiry")) {
                    this.getWithExpiry(currentKey);
                }
            }

            if(!this.loaded) {
                console.log("We didn't load from cache, fetch it.");
                this.updateArticle(decodeURI(location.pathname.substr(2)))
                this.loaded = true;
            }

            hljs.highlightAll();
        },

        updateArticle(path, back = false) {
            console.log("Asked to update article to " + path);

            if(!location.pathname.startsWith('/a/')) {
                self.location = '/a/' + path;
            }

            if(path.endsWith('.md')) {
                path = path.substr(0, path.length - 3);
            }
            if(!path.startsWith('/')) {
                path = '/' + path;
            }

            let storedArticle = this.getWithExpiry('article_content_cache.' + path);

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

                    this.$nextTick(() => this.postRender());

                    this.setWithExpiry('article_content_cache.' + decodeURI(location.pathname.substr(2)), this.article, 300000);
                });
            }


            if(!back && location.origin + '/a' + path !== window.location.href) {
                history.pushState(null, document.title, location.origin + '/a' + path);
            }

            this.sidebar = false;
            this.$nextTick(() => this.postRender());
        },

        postRender() {
            console.log('postRender');
            hljs.highlightAll();
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
        },

        setWithExpiry(key, value, ttl) {
            const item = {
                value: value,
                expiry: new Date().getTime() + ttl,
            }

            console.log("Storing " + key + " as:");
            console.log(item);
            localStorage.setItem(key, JSON.stringify(item))
        },

        getWithExpiry(key) {
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
    }
}
</script>
