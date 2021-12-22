<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

if(!function_exists('wikilink')) {
    function wikilink($linkname) {
        $cache = app()->make('articles')->getLinkData();

        if(\Illuminate\Support\Str::endsWith($linkname, ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.tif', '.tiff'])) {
            return route('image', $linkname);
        }

        $linkname = strtolower($linkname);

        if(Arr::has($cache['articles'], $linkname.".md")) {
            $path = Arr::get($cache['articles'], $linkname.".md")['path'];
            return Str::substr($path, 0, Str::length($path) - 3);
        }

        return route('no_such_article', ['articlePath' => $linkname]);
    }
}

if(!function_exists('app_logo')) {
    function app_logo() {
        return config('app.logo') ?? asset('images/logo.png');
    }
}

if(!function_exists('app_logo_dark')) {
    function app_logo_dark() {
        return config('app.logo')
            ? config('app.logo-dark') ?? app_logo()
            : asset('images/logo-dark.png');
    }
}
