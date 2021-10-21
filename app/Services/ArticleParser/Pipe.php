<?php

namespace App\Services\ArticleParser;

use App\Models\Article;
use App\Models\ArticleBlock;
use Closure;

abstract class Pipe
{
    public function handle(Article $article, Closure $next): Article
    {
        return $next($this->parseAll($article));
    }

    /**
     * @param Article $article
     * @return Article
     */
    protected function parseAll(Article $article): Article
    {
        $article->blocks = $article->blocks->map(function($block){
            return static::parse($block);
        });

        return $article;
    }

    public abstract function parse(ArticleBlock $article): ArticleBlock;
}
