<?php

use App\Models\Article;
use App\Services\ArticleParser\Pipeline\ConvertMarkdownToHtml;

test('ConvertMarkdownToHtml converts markdown to HTML', function () {
    $article = new Article('Test', 'test.md', "## Hello World\n\nThis is a paragraph.");

    $pipe = new ConvertMarkdownToHtml();
    $result = $pipe->parse($article);

    expect($result->content)->toContain('<h2>Hello World</h2>');
    expect($result->content)->toContain('<p>This is a paragraph.</p>');
});

test('ConvertMarkdownToHtml handles code blocks', function () {
    $article = new Article('Test', 'test.md', "```php\n\$foo = 'bar';\n```");

    $pipe = new ConvertMarkdownToHtml();
    $result = $pipe->parse($article);

    expect($result->content)->toContain('<code');
    expect($result->content)->toContain('$foo');
});

test('ConvertMarkdownToHtml handles tables', function () {
    $article = new Article('Test', 'test.md', "| Header |\n| --- |\n| Cell |");

    $pipe = new ConvertMarkdownToHtml();
    $result = $pipe->parse($article);

    expect($result->content)->toContain('<table');
    expect($result->content)->toContain('article-table');
    expect($result->content)->toContain('Header');
    expect($result->content)->toContain('Cell');
});

test('ConvertMarkdownToHtml handles blockquotes', function () {
    $article = new Article('Test', 'test.md', "> This is a quote");

    $pipe = new ConvertMarkdownToHtml();
    $result = $pipe->parse($article);

    expect($result->content)->toContain('<blockquote');
    expect($result->content)->toContain('article-blockquote');
});

test('ConvertMarkdownToHtml extracts front matter', function () {
    $article = new Article('Test', 'test.md', "---\ntitle: Custom\ndraft: true\n---\n\nContent here");

    $pipe = new ConvertMarkdownToHtml();
    $result = $pipe->parse($article);

    expect($result->getFrontMatter()->get('title'))->toBe('Custom');
    expect($result->getFrontMatter()->get('draft'))->toBeTrue();
    expect($result->content)->toContain('Content here');
});

test('ConvertMarkdownToHtml handles GFM autolinks', function () {
    $article = new Article('Test', 'test.md', "Visit https://example.com for more.");

    $pipe = new ConvertMarkdownToHtml();
    $result = $pipe->parse($article);

    expect($result->content)->toContain('href="https://example.com"');
});
