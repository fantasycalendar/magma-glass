<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleCache;

class IsolateTags extends \App\Services\ArticleParser\Pipe
{

    public function parse(Article $article): Article
    {
        return $article->setContent(preg_replace_callback('/(#+[a-zA-Z0-9(_)]{1,})/m', [static::class, 'replaceWikiLinks'], $article->content));
    }

    private static function replaceWikilinks($matches)
    {
        return sprintf("[%s](%s)", $matches[1], route('tag', ['tag' => $matches[1]]));
    }
}
