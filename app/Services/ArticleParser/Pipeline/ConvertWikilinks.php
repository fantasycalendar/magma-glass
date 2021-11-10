<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleCache;
use Illuminate\Support\Str;

class ConvertWikilinks extends \App\Services\ArticleParser\Pipe
{
    public static string $pattern = '/\[\[(.+?)\]\]/u';

    public function parse(Article $article): Article
    {
        return $article->setContent(
            preg_replace_callback(
                static::$pattern,
                [static::class, 'replaceWikiLinks'],
                $article->content
            )
        );
    }

    private static function replaceWikilinks($matches)
    {
        $linkTarget = $matches[1];
        $linkLabel = $matches[1];

        if(Str::contains($linkTarget, '|')) {
            $parts = explode('|', $linkTarget);

            $linkTarget = $parts[0];
            $linkLabel = $parts[1] ?? '';
        }

        if(app()->make('articles')->hasArticle($linkTarget)) {
            return sprintf("<a class='text-teal-600 dark:text-teal-500 font-semibold' href='javascript:' @click='\$dispatch(\"article-change\", `%s`)'>%s</a>", wikilink($linkTarget), addslashes($linkLabel));
        }

        return sprintf("<a class='text-rose-700 dark:text-rose-500 font-semibold' href='javascript:' @click='\$dispatch(\"article-change\", `%s`)'>%s</a>", addslashes($linkLabel), $linkTarget);
    }

}
