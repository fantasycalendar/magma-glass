<?php

namespace App\Services\ArticleParser;

use App\Models\Article;
use Closure;

abstract class Pipe
{
    public function handle(Article $articleContent, Closure $next): Article
    {
        return $next($this->parse($articleContent));
    }

    public abstract function parse(Article $articleContent): Article;
}
