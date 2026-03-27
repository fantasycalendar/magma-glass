<?php

namespace App\Http\Controllers;

use App\Services\ArticleRepository;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        return view('show_article');
    }

    public function articleJson(Request $request): array
    {
        /** @var ArticleRepository $repository */
        $repository = app()->make('articles');
        $articlePath = $request->input('articlePath', '');

        $article = $this->resolveArticle($repository, $articlePath);

        return [
            'title' => $article->name,
            'content' => $article->getParsed(),
            'path' => $articlePath,
            'links' => $repository->getLinks(),
        ];
    }

    public function tag(string $tag): \Illuminate\View\View
    {
        return view('tag_search', [
            'tagSearch' => $tag,
            'results' => app()->make('articles')->allWithTag($tag),
        ]);
    }

    public function linkData(): array
    {
        return app()->make('articles')->getLinkData();
    }

    public function search(Request $request): \Illuminate\Support\Collection
    {
        return app()->make('articles')->search($request->input('searchTerm', ''));
    }

    /**
     * Resolve an article from a path, falling back to home pages.
     */
    private function resolveArticle(ArticleRepository $repository, string $articlePath): \App\Models\Article
    {
        if ($articlePath === '' || $articlePath === '/') {
            $articlePath = $this->resolveDefaultArticlePath() ?? '/Home';
        }

        return $repository->getByArticlePath($articlePath);
    }

    /**
     * Try to find a default home article in the vault root.
     */
    private function resolveDefaultArticlePath(): ?string
    {
        if (config('magmaglass.default_article_path')) {
            return config('magmaglass.default_article_path');
        }

        $files = collect(\Illuminate\Support\Facades\Storage::disk('articles')->files('/'));

        $pathsToTry = [
            'home.md',
            'start here.md',
            'index.md',
        ];

        return $files->filter(fn ($file) => in_array(\Illuminate\Support\Str::lower($file), $pathsToTry))->first();
    }
}
