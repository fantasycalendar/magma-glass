<?php

use App\Models\Article;
use App\Services\ArticleParser\Pipeline\ConvertWikilinks;

beforeEach(function () {
    // Mock the articles repository
    $mock = Mockery::mock(\App\Services\ArticleRepository::class);
    $mock->shouldReceive('hasArticle')->andReturnUsing(function ($name) {
        return in_array($name, ['Existing Note', 'Another Page']);
    });
    $mock->shouldReceive('getLinkData')->andReturn([
        'articles' => collect(),
        'links' => collect(),
        'basename_index' => [
            'existing note.md' => ['existing note.md'],
            'another page.md' => ['folder/another page.md'],
        ],
    ]);

    app()->instance('articles', $mock);
});

test('wikilinks to existing articles get the wikilink class', function () {
    $article = new Article('Test', 'test.md', 'Check out [[Existing Note]] for details.');

    $pipe = new ConvertWikilinks();
    $result = $pipe->parse($article);

    expect($result->content)->toContain("class='wikilink'");
    expect($result->content)->toContain('Existing Note');
    expect($result->content)->not->toContain('wikilink--missing');
});

test('wikilinks to non-existing articles get the missing class', function () {
    $article = new Article('Test', 'test.md', 'See [[Missing Page]] for info.');

    $pipe = new ConvertWikilinks();
    $result = $pipe->parse($article);

    expect($result->content)->toContain('wikilink--missing');
    expect($result->content)->toContain('Missing Page');
});

test('wikilinks with aliases use the alias as display text', function () {
    $article = new Article('Test', 'test.md', 'Check [[Existing Note|my custom label]].');

    $pipe = new ConvertWikilinks();
    $result = $pipe->parse($article);

    expect($result->content)->toContain('my custom label');
});

test('multiple wikilinks in one article are all converted', function () {
    $article = new Article('Test', 'test.md', 'See [[Existing Note]] and [[Missing Page]].');

    $pipe = new ConvertWikilinks();
    $result = $pipe->parse($article);

    // Should have two links total
    expect(substr_count($result->content, '<a '))->toBe(2);
});

test('wikilinks use href="#" instead of javascript:', function () {
    $article = new Article('Test', 'test.md', 'See [[Existing Note]].');

    $pipe = new ConvertWikilinks();
    $result = $pipe->parse($article);

    expect($result->content)->not->toContain("href='javascript:'");
    expect($result->content)->toContain("href='#'");
});
