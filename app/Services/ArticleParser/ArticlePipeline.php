<?php

namespace App\Services\ArticleParser;

use App\Services\ArticleParser\Pipeline\ConvertWikilinks;
use Illuminate\Pipeline\Pipeline;

class ArticlePipeline
{
    public static $steps = [
        ConvertWikilinks::class,
    ];

    public static function process(string $articleHtml): string
    {
        return (new Pipeline(app()))
            ->send($articleHtml)
            ->through(self::$steps)
            ->then(function($articleHtml) {
                return $articleHtml;
            });
    }
}
