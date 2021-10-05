<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\ArticleCache;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

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
        $this->articlePath = $articlePath;
    }

    /**
     * Execute the job.
     *
     * @return Article
     * @throws \App\Exceptions\ArticleNotFoundException
     */
    public function handle(): Article
    {
        if($this->articlePath == '') {
            $this->articlePath = config('magmaglass.index_file', 'index');
        }

        return ArticleCache::getByArticlePath($this->articlePath);
    }
}
