<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleParser\Pipe;

class ConvertImageLinks extends Pipe
{
    public static string $pattern = '/(\!\[\[)(.*)(.png|.jpg|.jpeg|.gif|.bmp|.tif|.tiff)(\|\d+)?(\]\])/';

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

    private static function replaceImageLinks(array $matches): string
    {
        $filename = $matches[2] . $matches[3];

        if (app()->make('articles')->hasImage($filename)) {
            $link = wikilink($filename);
            $escapedFilename = e($filename);

            if (strlen($matches[4] ?? '')) {
                $width = substr($matches[4], 1);

                return sprintf(
                    "<div class='vault-image vault-image--sized'><img style='width: %dpx' class='cursor-pointer' @click=\"\$dispatch('open-image', '%s')\" src='%s' /><div class='vault-image__caption'>%s</div></div>",
                    (int) $width,
                    $link,
                    $link,
                    $escapedFilename
                );
            }

            return sprintf(
                "<div class='vault-image'><img class='m-auto text-center cursor-pointer' @click=\"\$dispatch('open-image', '%s')\" src='%s' /><div class='vault-image__caption'>%s</div></div>",
                $link,
                $link,
                $escapedFilename
            );
        }

        return sprintf(
            "<div class='vault-image vault-image--missing'><span>Image not found: %s</span></div>",
            e($filename)
        );
    }
}
