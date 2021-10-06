<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleParser\Pipe;
use Parsedown;

class ConvertMarkdownToHtml extends Pipe
{
    public function parse(Article $article): Article
    {
        return $article->setContent((new Parsedown())->setSafeMode(true)->text($article->content));
    }
}
