<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleCache;

class ConvertImageLinks extends \App\Services\ArticleParser\Pipe
{

    public function parse(Article $article): Article
    {
        return $article->setContent(preg_replace_callback('/(\!\[\[)(.*)(.png|.jpg|.jpeg|.gif|.bmp|.tif|.tiff)(\]\])/', [static::class, 'replaceImageLinks'], $article->content));
    }

    /**
     * Replaces image links with the image they reference
     *
     * @param $matches
     * @return string
     */
    private static function replaceImageLinks($matches): string
    {
        // Filename . Ext
        $filename = $matches[2] . $matches[3];

        if(ArticleCache::hasImage($filename)) {
            return sprintf("<img src='%s' />", wikilink($filename));
        }

        return "<a href='http://placeholder.com/'><img src='https://via.placeholder.com/800x120?text=No+Image+Found' /></a>";
    }
}
