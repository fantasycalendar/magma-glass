<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleCache;

class ConvertWikilinks extends \App\Services\ArticleParser\Pipe
{

    public function parse(Article $article): Article
    {
        return $article->setContent(preg_replace_callback('/\[\[(.+?)\]\]/u', self::replaceWikilinks(), $article->content));
    }

    private static function replaceWikilinks()
    {
        return function($matches) {
            if(ArticleCache::hasArticle($matches[1])) {
                return sprintf("<a href='%s'>%s</a>", wikilink($matches[1]), $matches[1]);
            }

            return sprintf("<a class='text-red-400' href='#'>%s</a>", $matches[1]);
        };
    }

}
