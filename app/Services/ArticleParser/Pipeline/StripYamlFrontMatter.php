<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\ArticleBlock;
use App\Services\ArticleParser\Pipe;

class StripYamlFrontMatter extends Pipe
{
    public function parse(ArticleBlock $block): ArticleBlock
    {
        return $block->setContent(preg_replace('/^-{3}(\n|\r|\r\n)((.*)(\n|\r|\r\n))*-{3}/', '', $block->content));
    }
}
