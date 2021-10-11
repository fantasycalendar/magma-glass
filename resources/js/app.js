require('./bootstrap');

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import hotkeys from 'hotkeys-js';

hotkeys('ctrl+k', function(event, handler){
    event.preventDefault();
    window.dispatchEvent(new Event('focus-search'));
});

hotkeys('ctrl+shift+l', function(event, handler){
    event.preventDefault();
    console.log("Ctrl+Shift+L pressed");
    window.toggleTheme();
});
