<?php

namespace App\Services;

use App\Models\Article;
use Parsedown;

class ArticleParser
{
    public static function parse(Article $article): string
    {
        return (new Parsedown())->setSafeMode(true)
            ->text($article->contents);
    }
}
