<?php

namespace App\Services;

use App\Models\Article;
use Parsedown;

class ArticleParser
{
    public static function parse(Article $article): string
    {
        $htmlified = (new Parsedown())->setSafeMode(true)
            ->text($article->contents);

        $htmlified = preg_replace_callback('/\[\[(.+?)\]\]/u', function($matches) {
            if(ArticleCache::hasArticle($matches[1])) {
                return sprintf("<a href='%s'>%s</a>", wikilink($matches[1]), $matches[1]);
            }

            return sprintf("<a class='text-red-400' href='#'>%s</a>", $matches[1]);
        },$htmlified);

        return $htmlified;
    }
}
