<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleParser\Pipe;

class WrapBlocks extends Pipe
{
    public function parse(Article $article): Article
    {
        return $block->setContent(
            "<div id='{$block->id}' class='article-block'>" . $block->content . "</div>"
        );
    }
}
