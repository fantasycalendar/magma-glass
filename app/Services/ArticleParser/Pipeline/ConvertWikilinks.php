<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleParser\Pipe;
use Illuminate\Support\Str;

class ConvertWikilinks extends Pipe
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

    private static function replaceWikilinks(array $matches): string
    {
        $linkTarget = $matches[1];
        $linkLabel = $matches[1];

        if (Str::contains($linkTarget, '|')) {
            $parts = explode('|', $linkTarget);

            $linkTarget = $parts[0];
            $linkLabel = $parts[1] ?? '';
        }

        if (app()->make('articles')->hasArticle($linkTarget)) {
            $path = wikilink($linkTarget);

            return sprintf(
                "<a class='wikilink' href='#' @click.prevent='\$dispatch(\"article-change\", `%s`)'>%s</a>",
                self::escapeForTemplateLiteral($path),
                e($linkLabel)
            );
        }

        return sprintf(
            "<a class='wikilink wikilink--missing' href='#' @click.prevent='\$dispatch(\"article-change\", `%s`)'>%s</a>",
            self::escapeForTemplateLiteral($linkTarget),
            e($linkLabel)
        );
    }

    /**
     * Escape a string for use inside a JS template literal (backticks).
     * Only backticks and ${} interpolation need escaping -- apostrophes are safe.
     */
    private static function escapeForTemplateLiteral(string $value): string
    {
        return str_replace(['\\', '`', '$'], ['\\\\', '\\`', '\\$'], $value);
    }
}
