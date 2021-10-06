<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleParser\Pipe;

class ConvertBannerYaml extends Pipe
{
    public function parse(Article $article): Article
    {
        return $article->setContent(preg_replace('/^-{3}(\n|\r|\r\n)((.*)(\n|\r|\r\n))*-{3}/', '', $article->content));
    }
}
