<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleCache;

class ConvertWikilinks extends \App\Services\ArticleParser\Pipe
{

    public function parse(Article $article): Article
    {
        return $article->setContent(preg_replace_callback('/\[\[(.+?)\]\]/u', [static::class, 'replaceWikiLinks'], $article->content));
    }

    private static function replaceWikilinks($matches)
    {
        if(ArticleCache::hasArticle($matches[1])) {
            return sprintf("<a class='text-teal-600 dark:text-teal-500 font-semibold' href='javascript:' @click='\$dispatch(\"article-change\", \"%s\")'>%s</a>", wikilink($matches[1]), $matches[1]);
        }

        return sprintf("<a class='text-rose-700 dark:text-rose-500 font-semibold' href='javascript:' @click='\$dispatch(\"article-change\", \"%s\")'>%s</a>", $matches[1], $matches[1]);
    }

}
