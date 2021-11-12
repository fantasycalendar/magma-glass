require('./bootstrap');

// Syntax highlighting in code blocks
import hljs from 'highlight.js';
hljs.configure({languages:[]});
window.hljs = hljs;

// Hotkeys
import hotkeys from 'hotkeys-js';


hotkeys('ctrl+k', function(event, handler){
    event.preventDefault();
    window.dispatchEvent(new Event('focus-search'));
});

hotkeys('ctrl+shift+l', function(event, handler){
    event.preventDefault();
    window.toggleTheme();
});

hotkeys('ctrl+f5,ctrl+shift+r', function(event, handler){
    event.preventDefault();
    let currentItem;
    let currentKey;
    let toRemove = [];

    for (let i = 0; i < localStorage.length; i++){
        currentItem = localStorage.getItem(localStorage.key(i));
        currentKey = localStorage.key(i);

        if (currentItem.includes("expiry")) {
            toRemove.push(currentKey);
        }
    }

    for (let i = 0; i < toRemove.length; i++) {
        localStorage.removeItem(toRemove[i]);
    }

    let url = new URL(location.href);
    url.searchParams.append('cold_boot', 'true')
    self.location = url;
});

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'

createInertiaApp({
    resolve: name => require(`./Pages/${name}`),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})
