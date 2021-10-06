<?php

namespace App\Services;

use App\Models\Article;
use App\Services\ArticleParser\ArticlePipeline;
use Parsedown;

class ArticleParser
{
    public static function parse(Article $article): string
    {
        $articleHtml = (new Parsedown())->setSafeMode(true)->text($article->contents);

        return ArticlePipeline::process($articleHtml);
    }
}
