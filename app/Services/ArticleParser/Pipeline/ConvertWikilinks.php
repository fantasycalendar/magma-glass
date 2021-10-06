<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Services\ArticleCache;

class ConvertWikilinks extends \App\Services\ArticleParser\Pipe
{

    public function parse(string $articleContent): string
    {
        return preg_replace_callback('/\[\[(.+?)\]\]/u', self::replaceWikilinks(), $articleContent);
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
