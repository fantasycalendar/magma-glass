<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleParser\Pipe;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Parsedown;

class ConvertMarkdownToHtml extends Pipe
{
    public function parse(Article $article): Article
    {
        $parser = new GithubFlavoredMarkdownConverter();
        dump($article->content);

        $article->setContent($parser->convertToHtml($article->content));

        dump($article->content);

        return $article;
    }
}
