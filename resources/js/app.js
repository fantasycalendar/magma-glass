import './bootstrap';

// Syntax highlighting in code blocks
import hljs from 'highlight.js';
hljs.configure({languages:[]});

window.hljs = hljs;


// Alpine JS components/modules
import app from './magma-glass/app';
import fileTree from './magma-glass/file-tree';

import Alpine from 'alpinejs';
window.Alpine = Alpine;

Alpine.data('app', app);
Alpine.data('fileTree', fileTree);

Alpine.start();

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
    let toRemove = [];

    for (let i = 0; i < localStorage.length; i++){
        let currentItem = localStorage.getItem(localStorage.key(i));
        let currentKey = localStorage.key(i);

        if (currentItem && currentItem.includes("expiry")) {
            toRemove.push(currentKey);
        }
    }

    for (let i = 0; i < toRemove.length; i++) {
        localStorage.removeItem(toRemove[i]);
    }

    // Hard refresh the page (clears client-side article cache only)
    self.location = location.href;
});
