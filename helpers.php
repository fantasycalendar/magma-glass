<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

if (! function_exists('wikilink')) {
    /**
     * Resolve a wikilink name to a URL path.
     * For images, returns the image route URL.
     * For articles, returns the relative path (without .md extension).
     */
    function wikilink(string $linkname): string
    {
        $imageExtensions = ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.tif', '.tiff'];

        if (Str::endsWith($linkname, $imageExtensions)) {
            return route('image', $linkname);
        }

        $cache = app()->make('articles')->getLinkData();
        $articles = $cache['articles'] ?? collect();

        // Try to find by lowercase basename match via the basename index
        $basenameIndex = $cache['basename_index'] ?? [];
        $basename = strtolower($linkname) . '.md';

        if (isset($basenameIndex[$basename]) && ! empty($basenameIndex[$basename])) {
            $cacheKey = $basenameIndex[$basename][0];
            $article = $articles[$cacheKey] ?? null;

            if ($article) {
                $path = $article['path'];

                return Str::endsWith($path, '.md')
                    ? Str::substr($path, 0, Str::length($path) - 3)
                    : $path;
            }
        }

        return route('article', ['articlePath' => $linkname]);
    }
}

if (! function_exists('app_logo')) {
    function app_logo(): string
    {
        return config('app.logo') ?? asset('images/logo.png');
    }
}

if (! function_exists('app_logo_dark')) {
    function app_logo_dark(): string
    {
        return config('app.logo')
            ? (config('app.logo-dark') ?? app_logo())
            : asset('images/logo-dark.png');
    }
}
