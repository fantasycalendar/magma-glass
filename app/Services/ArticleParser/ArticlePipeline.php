<?php

namespace App\Services\ArticleParser;

use App\Models\Article;
use App\Services\ArticleParser\Pipeline\ConvertBannerYaml;
use App\Services\ArticleParser\Pipeline\ConvertMarkdownToHtml;
use App\Services\ArticleParser\Pipeline\ConvertWikilinks;
use App\Services\ArticleParser\Pipeline\StyleTables;
use Illuminate\Pipeline\Pipeline;

class ArticlePipeline
{
    public static $steps = [
        ConvertBannerYaml::class,
        ConvertMarkdownToHtml::class,
        StyleTables::class,
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
