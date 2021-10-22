<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Models\ArticleBlock;

class StyleTables extends \App\Services\ArticleParser\Pipe
{
    public function parse(Article $article): Article
    {
        $replacements = [
            '<table>' => '<table class="min-w-full divide-y divide-gray-800 mb-8">',
            '<thead>' =>  '<thead class="bg-gray-800">',
            '<th>' => '<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">',
            '<tbody>' => '<tbody class="bg-gray-600 divide-y divide-gray-800">',
            '<td>' => '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">'
        ];

        return $article->setContent(str_replace(
            array_keys($replacements),
            array_values($replacements),
            $article->content
        ));
    }
}
