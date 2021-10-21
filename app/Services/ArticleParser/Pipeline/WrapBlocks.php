<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\ArticleBlock;
use App\Services\ArticleParser\Pipe;

class WrapBlocks extends Pipe
{
    public function parse(ArticleBlock $block): ArticleBlock
    {
        return $block->setContent(
            "<div id='{$block->id}' class='article-block'>" . $block->content . "</div>"
        );
    }
}
