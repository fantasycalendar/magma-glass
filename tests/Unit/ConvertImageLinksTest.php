<?php

use App\Models\Article;
use App\Services\ArticleParser\Pipeline\ConvertImageLinks;

beforeEach(function () {
    $mock = Mockery::mock(\App\Services\ArticleRepository::class);
    $mock->shouldReceive('hasImage')->andReturnUsing(function ($name) {
        return in_array($name, ['screenshot.png', 'photo.jpg']);
    });
    $mock->shouldReceive('getLinkData')->andReturn([
        'articles' => collect(),
        'links' => collect(),
        'basename_index' => [
            'screenshot.png' => ['images/screenshot.png'],
            'photo.jpg' => ['attachments/photo.jpg'],
        ],
    ]);

    app()->instance('articles', $mock);
});

test('image links to existing images are converted to img tags', function () {
    $article = new Article('Test', 'test.md', 'Here is an image: ![[screenshot.png]]');

    $pipe = new ConvertImageLinks();
    $result = $pipe->parse($article);

    expect($result->content)->toContain('<img');
    expect($result->content)->toContain('vault-image');
    expect($result->content)->toContain('screenshot.png');
});

test('image links with size parameter include width style', function () {
    $article = new Article('Test', 'test.md', '![[screenshot.png|300]]');

    $pipe = new ConvertImageLinks();
    $result = $pipe->parse($article);

    expect($result->content)->toContain('width: 300px');
    expect($result->content)->toContain('vault-image--sized');
});

test('image links to missing images show error message', function () {
    $article = new Article('Test', 'test.md', '![[nonexistent.png]]');

    $pipe = new ConvertImageLinks();
    $result = $pipe->parse($article);

    expect($result->content)->toContain('vault-image--missing');
    expect($result->content)->toContain('Image not found');
    expect($result->content)->not->toContain('placeholder.com');
});

test('multiple image formats are supported', function () {
    $extensions = ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'tif', 'tiff'];

    foreach ($extensions as $ext) {
        $article = new Article('Test', 'test.md', "![[photo.$ext]]");
        $pipe = new ConvertImageLinks();
        $result = $pipe->parse($article);

        // Should attempt to render (even if missing)
        expect($result->content)->not->toContain("![[photo.$ext]]");
    }
});
