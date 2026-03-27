<?php

namespace App\Services;

use App\Exceptions\ArticleNotFoundException;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleRepository
{
    private array $cache;

    public function __construct()
    {
        $this->cache = cache()->remember(
            'articles_cache',
            config('magmaglass.cache_ttl'),
            fn () => $this->buildCache()
        );
    }

    /**
     * Build the full article cache from the articles disk.
     * Keys articles by their lowercase relative path (without extension for .md files).
     */
    private function buildCache(): array
    {
        $articles = collect(Storage::disk('articles')->allFiles())
            ->reject(fn ($path) => Str::startsWith($path, '.obsidian/'))
            ->reject(fn ($path) => Str::startsWith($path, '.git/'))
            ->reject(fn ($path) => in_array(explode('/', $path)[0] ?? '', config('magmaglass.ignored_paths')))
            ->mapWithKeys(function ($path) {
                $content = '';
                $tags = collect();
                $links = collect();

                if (Str::endsWith($path, '.md')) {
                    $content = Str::of(Storage::disk('articles')->get($path));
                    $tags = $content->matchAll('/\B(\#[a-zA-Z-]+\b)(?!;)/m');
                    $links = $content->matchAll('/\[\[(.+?)\]\]/u')
                        ->reject(fn ($string) => Str::endsWith($string, ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.tif', '.tiff']));
                }

                return [
                    strtolower($path) => [
                        'path' => $path,
                        'title' => pathinfo($path, PATHINFO_FILENAME),
                        'tags' => $tags,
                        'links' => $links,
                        'last_modified' => Carbon::parse(Storage::disk('articles')->lastModified($path)),
                    ],
                ];
            })
            ->toArray();

        return [
            'articles' => collect($articles),
            'links' => self::buildLinks(collect($articles)),
            // Build a fast lookup: lowercase basename -> array of matching cache keys
            'basename_index' => self::buildBasenameIndex($articles),
        ];
    }

    /**
     * Build a reverse index from lowercase basename to full cache keys,
     * enabling O(1) lookup by article name alone.
     */
    private static function buildBasenameIndex(array $articles): array
    {
        $index = [];
        foreach ($articles as $key => $article) {
            $basename = strtolower(basename($article['path']));
            $index[$basename][] = $key;
        }

        return $index;
    }

    private static function buildLinks(Collection $articles): Collection
    {
        return $articles->map(function ($article) {
            $links = [];

            foreach ($article['links'] as $link) {
                $links[] = [$article['title'], $link];
            }

            return $links;
        })->values()->flatten(1);
    }

    /**
     * Check if an article exists by name (basename without extension).
     */
    public function hasArticle(string $articleName): bool
    {
        $basename = strtolower($articleName) . '.md';
        $index = $this->cache['basename_index'] ?? [];

        return isset($index[$basename]);
    }

    /**
     * Retrieve an article by its name (basename without extension).
     *
     * @throws ArticleNotFoundException
     */
    public function getByArticleName(string $articleName): Article
    {
        $basename = strtolower($articleName) . '.md';
        $index = $this->cache['basename_index'] ?? [];

        if (! isset($index[$basename]) || empty($index[$basename])) {
            throw new ArticleNotFoundException("No article '$articleName' was found.");
        }

        // Use the first match
        $cacheKey = $index[$basename][0];
        $info = $this->cache['articles'][$cacheKey] ?? null;

        if (! $info) {
            throw new ArticleNotFoundException("No article '$articleName' was found.");
        }

        $articlePath = $info['path'];

        if (! Storage::disk('articles')->exists($articlePath)) {
            throw new ArticleNotFoundException("No article '$articleName' was found.");
        }

        return new Article(
            $info['title'],
            $articlePath,
            Storage::disk('articles')->get($articlePath),
            $info['tags']->toArray()
        );
    }

    /**
     * Retrieve an article by its full path relative to the articles disk.
     */
    public function getByArticlePath(string $articlePath): Article
    {
        $localPath = Str::endsWith($articlePath, '.md')
            ? $articlePath
            : $articlePath . '.md';

        if (! Storage::disk('articles')->exists($localPath)) {
            if (static::pathIsHome($articlePath)) {
                return new Article(
                    '',
                    '',
                    "<div class='p-5 bg-gray-700 text-center text-xl font-italic'>You can create a note in the root of your vault called <strong>\"Home\"</strong>, <strong>\"Index\"</strong>, or <strong>\"Start here\"</strong> to customize this page.</div>"
                );
            }

            return new Article('Not Found', $localPath, "## The file '$articlePath' doesn't exist yet!");
        }

        $filename = pathinfo($localPath, PATHINFO_FILENAME);

        return new Article(
            $filename,
            $localPath,
            Storage::disk('articles')->get($localPath)
        );
    }

    /**
     * Check if an image exists in the vault by filename.
     */
    public function hasImage(string $imageName): bool
    {
        $basename = strtolower(basename($imageName));
        $index = $this->cache['basename_index'] ?? [];

        return isset($index[$basename]);
    }

    /**
     * Get the relative path to an image file by its name.
     */
    public function getImagePath(string $imageName): ?string
    {
        $basename = strtolower(basename($imageName));
        $index = $this->cache['basename_index'] ?? [];

        if (! isset($index[$basename]) || empty($index[$basename])) {
            return null;
        }

        $cacheKey = $index[$basename][0];

        return $this->cache['articles'][$cacheKey]['path'] ?? null;
    }

    /**
     * Find all articles with a given tag.
     */
    public function allWithTag(string $tag): Collection
    {
        return $this->cache['articles']->filter(function ($article) use ($tag) {
            return $article['tags']->contains("#$tag");
        });
    }

    /**
     * Search articles by title substring match.
     */
    public function search(string $searchTerm): Collection
    {
        return $this->cache['articles']->filter(function ($article) use ($searchTerm) {
            return Str::contains(strtolower($article['title']), strtolower($searchTerm));
        })->values();
    }

    /**
     * Get link data for the article graph.
     */
    public function getLinks(): Collection
    {
        return $this->cache['links'];
    }

    /**
     * Get the full cache data (articles + links) for the link data endpoint.
     */
    public function getLinkData(): array
    {
        return $this->cache;
    }

    /**
     * Clear all cached article data. Callable from artisan commands or webhooks.
     */
    public function clearCache(): void
    {
        cache()->forget('articles_cache');
        cache()->forget('file_tree');
    }

    /**
     * Check whether a path corresponds to a home/index page.
     */
    private static function pathIsHome(string $articlePath): bool
    {
        $normalized = strtolower(trim($articlePath, '/'));

        return in_array($normalized, ['home', 'start here', 'index', 'home.md', 'start here.md', 'index.md']);
    }
}
