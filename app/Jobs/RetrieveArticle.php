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
    private $articleName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $articleName)
    {
        $this->articleName = $articleName;
    }

    /**
     * Execute the job.
     *
     * @return Article
     * @throws \App\Exceptions\ArticleNotFoundException
     */
    public function handle(): Article
    {
        if($this->articleName == '') {
            $this->articleName = config('magmaglass.index_file', 'index');
        }

        return ArticleCache::getByArticleName($this->articleName);
    }
}
