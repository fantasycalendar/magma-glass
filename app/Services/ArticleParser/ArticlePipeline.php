<?php

namespace App\Services\ArticleParser;

use App\Models\Article;
use App\Services\ArticleParser\Pipeline\StripYamlFrontMatter;
use App\Services\ArticleParser\Pipeline\ConvertImageLinks;
use App\Services\ArticleParser\Pipeline\ConvertMarkdownToHtml;
use App\Services\ArticleParser\Pipeline\ConvertWikilinks;
use App\Services\ArticleParser\Pipeline\IsolateTags;
use App\Services\ArticleParser\Pipeline\StyleTables;
use App\Services\ArticleParser\Pipeline\WrapBlocks;
use Illuminate\Pipeline\Pipeline;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableSection;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Block\Paragraph;

class ArticlePipeline
{
    public static $steps = [
        StripYamlFrontMatter::class,
        IsolateTags::class,
        ConvertImageLinks::class,
        ConvertWikilinks::class,
//        ConvertMarkdownToHtml::class,
//        StyleTables::class,
//        WrapBlocks::class,
    ];

    public static function process(Article $article): Article
    {
        $environment = new Environment(static::defaultAttributesConfig());

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new DefaultAttributesExtension());

        $parser = new MarkdownConverter($environment);
        $article->setContent($parser->convertToHtml($article->content));

//        return $article;
        return (new Pipeline(app()))
            ->send($article)
            ->through(self::$steps)
            ->then(function(Article $article) {
                return $article;
            });
    }

    private static function defaultAttributesConfig()
    {
        return [
            'default_attributes' => [
                Table::class => [
                    'class' => ['min-w-full', 'divide-y', 'divide-gray-800', 'mb-8'],
                ],
                TableSection::class => [
                    'class' => static function (TableSection $node) {
                        if($node->getType() == 'head') {
                            return 'bg-gray-700';
                        }

                        if($node->getType() == 'body') {
                            return 'bg-gray-700 divide-y divide-gray-800';
                        }

                        return null;
                    }
                ],
                TableCell::class => [
                    'class' => static function (TableCell $node) {
                        if($node->getType() == 'header') {
                            return 'px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider';
                        }

                        if($node->getType() == 'data') {
                            return 'px-6 py-4 whitespace-nowrap text-sm text-gray-200';
                        }

                        return null;
                    }
                ],
                BlockQuote::class => [
                    'class' => ['p-2', 'm-2', 'border-l-2', 'border-gray-600', 'bg-gray-700', ''],
                ],
//                Paragraph::class => [
//                    'class' => ['text-center', 'font-comic-sans'],
//                ],
//                Link::class => [
//                    'class' => 'btn btn-link',
//                    'target' => '_blank',
//                ],
            ],
        ];
    }
}
