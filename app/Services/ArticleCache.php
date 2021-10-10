<?php

namespace App\Services;

use App\Exceptions\ArticleNotFoundException;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleCache
{
    public static function populate()
    {
        logger()->debug('Entered ' . static::class . '::' . __FUNCTION__);
        return cache()->remember('articles_cache', config('magmaglass.cache_ttl'), function() {
            logger()->debug('we what?');
            $articles = collect(static::loadDirectory('/'));

            return [
                'articles' => $articles,
                'links' => self::buildLinks($articles)
            ];
        });
    }

    public static function allWithTag($tag)
    {
        logger()->debug('Entered ' . static::class . '::' . __FUNCTION__);
        return static::populate()['articles']->filter(function($article) use ($tag) {
            return $article['tags']->contains($tag);
        });
    }

    public static function buildLinks($articles)
    {
        logger()->debug('Entered ' . static::class . '::' . __FUNCTION__);
        return $articles->map(function($article) {
            $links = [];

            foreach($article['links'] as $link) {
                $links[] = [$article['title'], $link];
            }

            return $links;
        })->values()->flatten(1);
    }

    public static function loadDirectory(string $directory): array
    {
        logger()->debug('Entered ' . static::class . '::' . __FUNCTION__);
        return collect(Storage::disk('articles')->allFiles())
            ->reject(fn($path) => Str::startsWith($path, '.obsidian/'))
            ->mapWithKeys(function($path) {
                $content = Str::of(Storage::disk('articles')->get($path));
                $tags = $content->matchAll('/(#+[a-zA-Z0-9(_)]{1,})/m');
                $links = $content->matchAll('/\[\[(.+?)\]\]/u')->reject(fn($string) => Str::endsWith($string, ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.tif', '.tiff']));

                return [
                    basename($path) => [
                        'path' => $path,
                        'title' => pathinfo($path)['filename'],
                        'tags' => $tags,
                        'links' => $links
                    ]
                ];
            })
            ->toArray();
    }

    public static function hasArticle($articleName): bool
    {
        logger()->debug('Entered ' . static::class . '::' . __FUNCTION__);
        return static::populate()['articles']->has($articleName . '.md');
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
        logger()->debug('Entered ' . static::class . '::' . __FUNCTION__);
        $info = static::populate()->get($articleName . '.md');
        $articlePath = $info['path'];
        if(!Storage::disk('articles')->exists($articlePath)) {
            throw new ArticleNotFoundException("No article '$articleName' was found.");
        }

        return new Article($articleName, $articlePath, Storage::disk('articles')->get($articlePath), $info['tags']);
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
        logger()->debug('Entered ' . static::class . '::' . __FUNCTION__);
        $localPath = Str::endsWith($articlePath, '.md')
            ? $articlePath
            : $articlePath . '.md';

        $fullArticlePath = Storage::disk('articles')->path($localPath);
        if(!Storage::disk('articles')->exists($localPath)) {
            if($articlePath == 'Home') {
                return new Article('', '', '_You can create a note in the root of your vault called **"Home"** to customize this page._');
            }

            return new Article('Oooops!', $fullArticlePath, "## The file '$articlePath' doesn't exist yet!");
        }

        $details = pathinfo($fullArticlePath);

        return new Article($details['filename'], $fullArticlePath, Storage::disk('articles')->get($localPath));
    }

    public static function hasImage(string $imageName)
    {
        logger()->debug('Entered ' . static::class . '::' . __FUNCTION__);
        return static::populate()['articles']->has($imageName);
    }
}
