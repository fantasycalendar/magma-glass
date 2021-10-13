<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\ArticleCache;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RetrieveArticle
{
    use Dispatchable, SerializesModels;

    /**
     * @var string
     */
    private $articlePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $articlePath)
    {
        $this->articlePath = ($articlePath == '/' || $articlePath == '')
            ? $this->resolveDefaultArticlePath() ?? '/Home'
            : $articlePath;
    }

    /**
     * Execute the job.
     *
     * @return Article
     * @throws \App\Exceptions\ArticleNotFoundException
     */
    public function handle(): Article
    {
        return ArticleCache::getByArticlePath($this->articlePath);
    }

    private function resolveDefaultArticlePath()
    {
        if(config('magmaglass.default_article_path')) {
            return config('magmaglass.default_article_path');
        }

        $files = collect(Storage::disk('articles')->files('/'));

        $pathsToTry = [
            'home.md',
            'start here.md',
            'index.md',
        ];

        return $files->filter(fn($file) => in_array(Str::lower($file), $pathsToTry))->first();
    }
}
