<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\ArticleBlock;
use App\Services\ArticleParser\Pipe;
use League\CommonMark\CommonMarkConverter;
use Parsedown;

class ConvertMarkdownToHtml extends Pipe
{
    public function parse(ArticleBlock $block): ArticleBlock
    {
        $parser = new CommonMarkConverter();

        return $block->setContent($parser->convertToHtml($block->content));
    }
}
