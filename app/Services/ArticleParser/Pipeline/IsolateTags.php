<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\ArticleBlock;
use Illuminate\Support\Str;

class IsolateTags extends \App\Services\ArticleParser\Pipe
{
    public static string $format = "<a class='px-2 mr-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' href='%s'>%s</a>";
    public static string $pattern = "/(^|\s)(#+[a-zA-Z0-9\-(_)]{1,})/m";

    public function parse(ArticleBlock $block): ArticleBlock
    {
        if($block->isCodeBlock()) {
            return $block;
        }

        return $block->setContent(preg_replace_callback(self::$pattern, [static::class, 'replace'], $block->content));
    }

    /**
     * @param $matches
     * @return string
     */
    private static function replace($matches): string
    {
        return sprintf(
            static::$format,
            route('tag', ['tag' => Str::replace('#', '', $matches[2])]),
            $matches[2]
        );
    }
}
