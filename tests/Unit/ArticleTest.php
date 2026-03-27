<?php

use App\Models\Article;

test('article can be constructed with name, path, and contents', function () {
    $article = new Article('Test Note', 'test-note.md', '# Test Note\nSome content');

    expect($article->name)->toBe('Test Note');
    expect($article->path)->toBe('test-note.md');
    expect($article->content)->toBe('# Test Note\nSome content');
    expect($article->tags)->toBeEmpty();
});

test('article can be constructed with tags', function () {
    $article = new Article('Tagged', 'tagged.md', 'content', ['#php', '#laravel']);

    expect($article->tags)->toHaveCount(2);
    expect($article->tags->first())->toBe('#php');
});

test('nameIsHeading returns true when first line is a heading matching name', function () {
    $article = new Article('My Note', 'my-note.md', "# My Note\nSome content here");

    expect($article->nameIsHeading())->toBeTrue();
});

test('nameIsHeading returns false when first line differs from name', function () {
    $article = new Article('My Note', 'my-note.md', "# Different Title\nSome content here");

    expect($article->nameIsHeading())->toBeFalse();
});

test('nameIsHeading returns false when content has no heading', function () {
    $article = new Article('My Note', 'my-note.md', 'Just plain text content');

    expect($article->nameIsHeading())->toBeFalse();
});

test('nameIsHeading handles single-line content', function () {
    $article = new Article('My Note', 'my-note.md', '# My Note');

    expect($article->nameIsHeading())->toBeTrue();
});

test('nameIsHeading detects heading after front matter', function () {
    $article = new Article('My Note', 'my-note.md', "---\ntitle: Custom\n---\n# My Note\nSome content");

    expect($article->nameIsHeading())->toBeTrue();
});

test('nameIsHeading returns false when heading after front matter differs', function () {
    $article = new Article('My Note', 'my-note.md', "---\ntitle: Custom\n---\n# Different\nSome content");

    expect($article->nameIsHeading())->toBeFalse();
});

test('stripNameHeading removes heading from content without front matter', function () {
    $article = new Article('My Note', 'my-note.md', "# My Note\nSome content here");

    $article->stripNameHeading();

    expect($article->content)->not->toContain('# My Note');
    expect($article->content)->toContain('Some content here');
});

test('stripNameHeading removes heading from content with front matter', function () {
    $article = new Article('My Note', 'my-note.md', "---\ntitle: Custom\n---\n# My Note\nSome content");

    $article->stripNameHeading();

    expect($article->content)->not->toContain('# My Note');
    expect($article->content)->toContain('---');
    expect($article->content)->toContain('Some content');
});

test('setContent replaces content and returns self', function () {
    $article = new Article('Test', 'test.md', 'original');

    $result = $article->setContent('new content');

    expect($result)->toBe($article);
    expect($article->content)->toBe('new content');
});

test('setFrontMatter stores front matter and returns self', function () {
    $article = new Article('Test', 'test.md', 'content');

    $result = $article->setFrontMatter(['title' => 'Custom Title', 'draft' => true]);

    expect($result)->toBe($article);
    expect($article->getFrontMatter())->toHaveCount(2);
    expect($article->getFrontMatter()->get('title'))->toBe('Custom Title');
});
