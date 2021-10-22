<?php

namespace App\Services;

use App\Exceptions\ArticleNotFoundException;
use App\Models\Article;
use App\Services\ArticleParser\Pipeline\IsolateTags;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleCache
{
    /**
     * @var mixed
     */
    private $cache;

    public function __construct()
    {
        $this->cache = cache()->remember('articles_cache', config('magmaglass.cache_ttl'), function() {
            $articles = collect(static::loadDirectory());

            return [
                'articles' => $articles,
                'links' => self::buildLinks($articles)
            ];
        });
    }

    public function allWithTag($tag)
    {
        return $this->cache['articles']->filter(function($article) use ($tag) {
            return $article['tags']->contains("#$tag");
        });
    }

    public static function buildLinks($articles)
    {
        return $articles->map(function($article) {
            $links = [];

            foreach($article['links'] as $link) {
                $links[] = [$article['title'], $link];
            }

            return $links;
        })->values()->flatten(1);
    }

    public static function loadDirectory(): array
    {
        return collect(Storage::disk('articles')->allFiles())
            ->reject(fn($path) => Str::startsWith($path, '.obsidian/'))
            ->mapWithKeys(function($path) {
                $content = Str::of(Storage::disk('articles')->get($path));
                $tags = $content->matchAll(IsolateTags::$pattern);
                $links = $content->matchAll('/\[\[(.+?)\]\]/u')->reject(fn($string) => Str::endsWith($string, ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.tif', '.tiff']));

                return [
                    strtolower(basename($path)) => [
                        'path' => $path,
                        'title' => pathinfo($path)['filename'],
                        'tags' => $tags,
                        'links' => $links,
                        'last_modified' => Carbon::parse(Storage::disk('articles')->lastModified($path))
                    ]
                ];
            })
            ->toArray();
    }

    public function hasArticle($articleName): bool
    {
        return $this->cache['articles']->has(strtolower($articleName) . '.md');
    }

    /**
     * Retreives a given Article by name
     *
     * @param $articleName
     * @return Article
     * @throws ArticleNotFoundException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getByArticleName($articleName): Article
    {
        $info = $this->cache->get($articleName . '.md');
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
    public function getByArticlePath($articlePath): Article
    {
        $localPath = Str::endsWith($articlePath, '.md')
            ? $articlePath
            : $articlePath . '.md';

        $fullArticlePath = Storage::disk('articles')->path($localPath);
        if(!Storage::disk('articles')->exists($localPath)) {
            if(static::pathIsHome($articlePath)) {
                return new Article('', '', "<div class='p-5 bg-gray-700 text-center text-xl font-italic'>You can create a note in the root of your vault called <strong>\"Home\"</strong>, <strong>\"Index\"</strong>, or <strong>\"Start here\"</strong> to customize this page.</div>");
            }

            return new Article('Oooops!', $fullArticlePath, "## The file '$articlePath' doesn't exist yet!");
        }

        $details = pathinfo($fullArticlePath);

        return new Article($details['filename'], $fullArticlePath, Storage::disk('articles')->get($localPath));
    }

    public function hasImage(string $imageName)
    {
        return $this->cache['articles']->has(strtolower(basename($imageName)));
    }

    /**
     * Checks whether a 'not found' path is a home path.
     *
     * @param $articlePath
     * @return bool
     */
    private function pathIsHome($articlePath): bool
    {
        return in_array(strtolower($articlePath), ['/home.md', '/start here.md', '/index.md']);
    }

    public function getLinks()
    {
        return $this->cache['links'];
    }

    public function getLinkData()
    {
        return $this->cache;
    }

    public function search($searchTerm)
    {
        return $this->cache->filter(function($article) use ($searchTerm) {
            return Str::contains(strtolower($article['title']), strtolower($searchTerm));
        })->values();
    }

    public function getImagePath($imageName)
    {
        return $this->cache['articles'][strtolower(basename($imageName))]['path'];
    }

    public function clearCache()
    {
        if(request()->input('cold_boot')) {
            logger()->debug('Cold booting, clearing cache.');
            cache()->forget('articles_cache');
            cache()->forget('file_tree');
            logger()->debug('Cache cleared.');
        }
    }
}
