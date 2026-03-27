<?php

namespace App\Services\ArticleParser\Pipeline;

use App\Models\Article;
use App\Services\ArticleParser\InlineParser\ArticleTagParser;
use App\Services\ArticleParser\Pipe;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableSection;
use League\CommonMark\MarkdownConverter;

class ConvertMarkdownToHtml extends Pipe
{
    public function parse(Article $article): Article
    {
        $parser = $this->createParser();

        $parsed = $parser->convertToHtml($article->content);

        if ($parsed instanceof RenderedContentWithFrontMatter) {
            $article->setFrontMatter($parsed->getFrontMatter());
        }

        return $article->setContent($parsed);
    }

    private static function defaultAttributesConfig(): array
    {
        return [
            'default_attributes' => [
                Table::class => [
                    'class' => 'article-table',
                ],
                TableSection::class => [
                    'class' => static function (TableSection $node) {
                        if ($node->getType() === 'head') {
                            return 'article-table__head';
                        }

                        if ($node->getType() === 'body') {
                            return 'article-table__body';
                        }

                        return null;
                    },
                ],
                TableCell::class => [
                    'class' => static function (TableCell $node) {
                        if ($node->getType() === 'header') {
                            return 'article-table__header-cell';
                        }

                        if ($node->getType() === 'data') {
                            return 'article-table__data-cell';
                        }

                        return null;
                    },
                ],
                BlockQuote::class => [
                    'class' => 'article-blockquote',
                ],
                Image::class => [
                    'style' => static function (Image $node) {
                        $imageAlt = $node->firstChild()?->getLiteral() ?? '';

                        if (preg_match('/(\|\d+)$/', $imageAlt, $result)) {
                            $width = substr($result[1], 1);

                            return ["width: {$width}px;"];
                        }

                        return null;
                    },
                ],
            ],
        ];
    }

    private function createParser(): MarkdownConverter
    {
        $environment = new Environment(static::defaultAttributesConfig());

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new DefaultAttributesExtension());
        $environment->addExtension(new FrontMatterExtension());

        $environment->addInlineParser(new ArticleTagParser());

        return new MarkdownConverter($environment);
    }
}
