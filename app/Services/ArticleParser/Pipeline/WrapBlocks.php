<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleParser\Pipe;

class WrapBlocks extends Pipe
{
    public function parse(Article $article): Article
    {
        return $article->setContent(
            "<div id='{$article->id}' class='article-block'>" . $article->content . "</div>"
        );
    }
}
