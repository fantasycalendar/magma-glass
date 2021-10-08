<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $page_title ?? config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">

        <!-- Scripts -->
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

            let fileTree = function() {
                return {
                    files: [
                        { 'title': 'A test file', 'filename': 'A test file.md' },
                        { 'title': 'A test folder', 'children': [
                                { 'title': 'A test file', 'filename': 'A test folder/A test file.md' },
                                { 'title': 'A second test folder', 'children': [
                                        { 'title': 'A test file', 'filename': 'A test folder/A second test folder/A test file.md' }
                                    ] },
                                { 'title': 'A third test folder', 'children': [
                                        { 'title': 'A test file', 'filename': 'A test folder/A third test folder/A test file.md' }
                                    ] }
                            ] }
                    ],
                    renderLevel: function(obj,i){
                        let ref = 'l'+Math.random().toString(36).substring(7);
                        let folderIcon = "<i class=\\'fa text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300 mr-3 text-center align-middle flex-shrink-0 h-100 w-100 inline-block w-4 h-4 fa-folder\\'></i>";
                        let fileIcon = "<i class=\\'fa text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-300 mr-3 text-center align-middle flex-shrink-0 h-100 w-100 inline-block w-4 h-4 fa-file\\'></i>";


                        let html = `<a :href="(file.children ? '#' : file.filename)"
                                       class="text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white group flex items items-center px-3 py-2 text-sm font-medium rounded-sm"
                                       :class="{'has-children':file.children}"
                                       x-html="(file.children ? '${folderIcon}' : '${fileIcon}') + ' ' + file.title" ${ obj.children ? `@click.prevent="toggleLevel($refs.${ref})"` : '' }></a>`;

                        if(obj.children) {
                            html += `<ul style="display:none;" x-ref="${ref}" class="pl-2 pb-1 ml-0 transition-all duration-150 opacity-0 list-none">
                            <template x-for='(file,i) in file.children'>
                                <li x-html="renderLevel(file,i)"></li>
                            </template>
                        </ul>`;
                        }

                        return html;
                    },
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
                        if( el.style.length && el.style.display === 'none' ) {
                            this.showLevel(el);
                        } else {
                            this.hideLevel(el);
                        }
                    }
                }
            }
        </script>
    </head>
    <body id="app" class="font-sans antialiased" x-data="{ 'sidebar': false, 'loaded': false, 'theme': localStorage.theme }">
    <!-- This example requires Tailwind CSS v2.0+ -->
    <div class="h-screen flex overflow-hidden bg-white dark:bg-gray-800">
        <!-- Off-canvas menu for mobile, show/hide based on off-canvas menu state. -->
        <div class="fixed inset-0 flex z-40 md:hidden" role="dialog" aria-modal="true" :class="{ 'pointer-events-none': !sidebar }">
            <!--
              Off-canvas menu overlay, show/hide based on off-canvas menu state.

              Entering: "transition-opacity ease-linear duration-300"
                From: "opacity-0"
                To: "opacity-100"
              Leaving: "transition-opacity ease-linear duration-300"
                From: "opacity-100"
                To: "opacity-0"
            -->
            <div class="fixed inset-0 bg-gray-400 dark:bg-gray-600 bg-opacity-75 transition-opacity ease-linear duration-300" aria-hidden="true" :class="{ 'opacity-100': sidebar, 'opacity-0': !sidebar }"  @click="sidebar = !sidebar" x-cloak></div>

            <!--
              Off-canvas menu, show/hide based on off-canvas menu state.

              Entering: "transition ease-in-out duration-300 transform"
                From: "-translate-x-full"
                To: "translate-x-0"
              Leaving: "transition ease-in-out duration-300 transform"
                From: "translate-x-0"
                To: "-translate-x-full"
            -->
            <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white dark:bg-gray-700 transition ease-in-out duration-300 transform" :class="{ 'translate-x-0': sidebar, '-translate-x-full': !sidebar }" x-cloak>
                <!--
                  Close button, show/hide based on off-canvas menu state.

                  Entering: "ease-in-out duration-300"
                    From: "opacity-0"
                    To: "opacity-100"
                  Leaving: "ease-in-out duration-300"
                    From: "opacity-100"
                    To: "opacity-0"
                -->
                <div class="absolute top-0 right-0 -mr-12 pt-2" :class="{ 'hidden': !sidebar }">
                    <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="sidebar = !sidebar">
                        <span class="sr-only">Close sidebar</span>
                        <!-- Heroicon name: outline/x -->
                        <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                    <div class="flex-shrink-0 flex items-center px-4 text-center">
                        <img class="h-18 mx-auto" src="{{ asset('images/logo.png') }}" alt="Workflow">
                    </div>
                    <nav class="mt-5 px-2 space-y-1" x-data="fileTree()">
                        <ul class="ml-0 list-none">
                            <template x-for="(file, i) in files">
                                <li x-html="renderLevel(file, i)"></li>
                            </template>
                        </ul>
                    </nav>
                </div>
                <div class="flex-shrink-0 flex justify-between align-middle bg-white dark:bg-gray-700 text-gray-400 font-medium dark:font-light p-4">
                    <div class="grid place-items-center">
                        {{ $page_name ?? config('app.name') }}
                    </div>
                    <div>
                        <i class="fa cursor-pointer p-2 border dark:border-gray-600 rounded dark:bg-gray-700 dark:hover:bg-gray-800 transition-all ease-linear duration-300" @click="window.toggleTheme()" :class="{ 'fa-moon': theme === 'dark', 'fa-sun': theme === 'light' }"></i>
                    </div>
                </div>
            </div>

            <div class="flex-shrink-0 w-14">
                <!-- Force sidebar to shrink to fit close icon -->
            </div>
        </div>

        <!-- Static sidebar for desktop -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 md:w-80 2xl:w-96">
                <!-- Sidebar component, swap this element with another sidebar if you like -->
                <div class="flex-1 flex flex-col min-h-0 bg-white dark:bg-gray-700 border-r border-gray-200 dark:border-gray-600">
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <div class="flex items-center flex-shrink-0 px-4 text-center">
                            <img class="h-18 mx-auto" src="{{ asset('images/logo.png') }}" alt="Workflow">
                        </div>
                        <nav class="mt-5 flex-1 px-2 bg-white dark:bg-gray-700 space-y-1" x-data="fileTree()">
                            <ul class="ml-0 list-none">
                                <template x-for="(file, i) in files">
                                    <li x-html="renderLevel(file, i)"></li>
                                </template>
                            </ul>
                        </nav>
                    </div>
                    <div class="flex-shrink-0 flex justify-between align-middle bg-white dark:bg-gray-700 text-gray-400 font-medium dark:font-light p-4">
                        <div class="grid place-items-center">
                            {{ $page_name ?? config('app.name') }}
                        </div>
                        <div>
                            <i class="fa cursor-pointer p-2 border dark:border-gray-600 rounded dark:bg-gray-700 dark:hover:bg-gray-800 transition-all ease-linear duration-300" @click="window.toggleTheme()" :class="{ 'fa-moon': theme === 'dark', 'fa-sun': theme === 'light' }"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <div class="md:hidden flex justify-start align-items-middle pl-1 pt-1 sm:pl-3 sm:pt-3 bg-gray-100 shadow dark:shadow-none dark: bg-gray-700">
                <button type="button" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" @click="sidebar = !sidebar">
                    <span class="sr-only">Open sidebar</span>
                    <!-- Heroicon name: outline/menu -->
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="grid place-items-center text-gray-700 dark:text-white flex-grow-1 overflow-x-scroll text-2xl flex-nowrap whitespace-nowrap pr-4">
                    {{ $mobile_header }}
                </div>
            </div>
            <main class="bg-gray-100 dark:bg-gray-800 flex-1 relative z-0 overflow-y-auto focus:outline-none">
                <div class="py-6 text-gray-700 dark:text-white">
                    <div class="max-w-7xl mx-auto px-4 hidden md:block sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>
    </body>
</html>
