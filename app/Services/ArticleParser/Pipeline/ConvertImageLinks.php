<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleCache;

class ConvertImageLinks extends \App\Services\ArticleParser\Pipe
{
    public static string $pattern = '/(\!\[\[)(.*)(.png|.jpg|.jpeg|.gif|.bmp|.tif|.tiff)(\]\])/';

    public function parse(Article $article): Article
    {
        return $article->setContent(
            preg_replace_callback(
                static::$pattern,
                [static::class, 'replaceImageLinks'],
                $article->content
            )
        );
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

        if(app()->make('articles')->hasImage($filename)) {
            $link = wikilink($filename);
            return sprintf("<div class='flex flex-col p-6 border bg-gray-100 shadow-sm m-6 dark:bg-gray-700 dark:border-gray-600 rounded text-center'><img class='m-auto text-center cursor-pointer' @click=\"\$dispatch('open-image', '%s')\" src='%s' /><div class='text-gray-400 pt-6'>%s</div></div>", $link, $link, $filename);
        }

        return "<a href='http://placeholder.com/'><img src='https://via.placeholder.com/800x120?text=No+Image+Found' /></a>";
    }
}
