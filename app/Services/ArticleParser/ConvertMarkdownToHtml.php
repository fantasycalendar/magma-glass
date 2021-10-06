<?php

namespace App\Services\ArticleParser;

use App\Models\Article;
use Parsedown;

class ConvertMarkdownToHtml extends Pipe
{
    public function parse(Article $article): Article
    {
        return $article->setContent((new Parsedown())->setSafeMode(true)->text($article->content));
    }
}
