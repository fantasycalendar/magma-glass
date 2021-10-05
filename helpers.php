<?php

use Illuminate\Support\Str;

if(!function_exists('wikilink')) {
    function wikilink($linkname) {
        $path = \App\Services\ArticleCache::populate()[$linkname.'.md'];

//        if(\Illuminate\Support\Str::endsWith($path, ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.tif', '.tiff'])) {
//            return asset()
//        }

//        dd($path);

        if(Str::endsWith($path, '.md')) {
            return route('index', ['articlePath' => Str::substr($path, 0, Str::length($path) - 3)]);
        }

        return route('no_such_article', ['articlePath' => $path]);
    }
}
