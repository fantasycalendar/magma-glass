<?php

namespace App\Services;

use App\Exceptions\ArticleNotFoundException;
use App\Models\Article;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleCache
{
    public static function populate()
    {
        return cache()->remember('articles_cache', config('magmaglass.cache_ttl'), function() {
            return static::loadDirectory('/');
        });
    }

    public static function loadDirectory(string $directory): array
    {
        return collect(Storage::disk('articles')->allFiles())
            ->reject(fn($path) => Str::startsWith($path, '.obsidian/'))
            ->mapWithKeys(fn($path) => [basename($path) => $path])
            ->toArray();
    }

    public static function hasArticle($articleName): bool
    {
        return Arr::exists(static::populate(), $articleName . '.md');
    }

    /**
     * Retreives a given Article by name
     *
     * @param $articleName
     * @return Article
     * @throws ArticleNotFoundException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getByArticleName($articleName): Article
    {
        $articlePath = collect(static::populate())->get($articleName . '.md');
        if(!Storage::disk('articles')->exists($articlePath)) {
            throw new ArticleNotFoundException("No article '$articleName' was found.");
        }

        return new Article($articleName, $articlePath, Storage::disk('articles')->get($articlePath));
    }

    /**
     * Retreives a given Article by path
     *
     * @param $articlePath
     * @return Article
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function getByArticlePath($articlePath): Article
    {
        $localPath = $articlePath . '.md';

        $fullArticlePath = Storage::disk('articles')->path($localPath);
        if(!Storage::disk('articles')->exists($localPath)) {
            throw new ArticleNotFoundException("No article '$localPath' was found. (tried $fullArticlePath)");
        }

        $details = pathinfo($fullArticlePath);

        return new Article($details['filename'], $fullArticlePath, Storage::disk('articles')->get($localPath));
    }
}
