<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

if(!function_exists('wikilink')) {
    function wikilink($linkname) {
        $cache = \App\Services\ArticleCache::populate();

        if(\Illuminate\Support\Str::endsWith($linkname, ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.tif', '.tiff'])) {
            return route('image', $linkname);
        }

        if(Arr::has($cache, $linkname.".md")) {
            $path = Arr::get($cache, $linkname.".md");
            return route('index', ['articlePath' => Str::substr($path, 0, Str::length($path) - 3)]);
        }

        return route('no_such_article', ['articlePath' => $linkname]);
    }
}
