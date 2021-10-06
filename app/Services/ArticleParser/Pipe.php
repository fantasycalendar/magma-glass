<?php

namespace App\Services\ArticleParser;

use Closure;

abstract class Pipe
{
    public function handle(string $articleContent, Closure $next): string
    {
        return $next($this->parse($articleContent));
    }

    public abstract function parse(string $articleContent): string;
}
