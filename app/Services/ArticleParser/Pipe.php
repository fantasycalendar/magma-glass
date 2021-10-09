<?php

namespace App\Services\ArticleParser;

use App\Models\Article;
use Closure;

abstract class Pipe
{
    public function handle(Article $article, Closure $next): Article
    {
        return $next($this->parse($article));
    }

    public abstract function parse(Article $article): Article;
}
