<?php

namespace App\Services\ArticleParser;

use App\Models\Article;
use App\Services\ArticleParser\Pipeline\ConvertImageLinks;
use App\Services\ArticleParser\Pipeline\ConvertMarkdownToHtml;
use App\Services\ArticleParser\Pipeline\ConvertWikilinks;
use Illuminate\Pipeline\Pipeline;

class ArticlePipeline
{
    public static $steps = [
        ConvertMarkdownToHtml::class,
        ConvertImageLinks::class,
        ConvertWikilinks::class,
    ];

    public static function process(Article $article): Article
    {
        return (new Pipeline(app()))
            ->send($article)
            ->through(self::$steps)
            ->then(function(Article $article) {
                return $article;
            });
    }
}
