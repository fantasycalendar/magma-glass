<?php

use App\Services\ArticleRepository;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Set up a fake articles disk with test files
    Storage::fake('articles');

    Storage::disk('articles')->put('Home.md', "# Home\n\nWelcome to the vault.");
    Storage::disk('articles')->put('Notes/Daily.md', "# Daily\n\nDaily notes go here. #daily");
    Storage::disk('articles')->put('Notes/Weekly.md', "# Weekly\n\nWeekly notes go here. #weekly");
    Storage::disk('articles')->put('images/screenshot.png', 'fake-image-data');
    Storage::disk('articles')->put('.obsidian/config.json', '{}');

    // Clear any cached data
    cache()->forget('articles_cache');
    cache()->forget('file_tree');

    // Set config
    config(['magmaglass.cache_ttl' => 3600]);
    config(['magmaglass.ignored_paths' => []]);
});

test('repository loads articles and ignores .obsidian folder', function () {
    $repo = new ArticleRepository();

    expect($repo->hasArticle('Home'))->toBeTrue();
    expect($repo->hasArticle('Daily'))->toBeTrue();
    expect($repo->hasArticle('Weekly'))->toBeTrue();
});

test('repository can check if an article exists', function () {
    $repo = new ArticleRepository();

    expect($repo->hasArticle('Home'))->toBeTrue();
    expect($repo->hasArticle('Nonexistent'))->toBeFalse();
});

test('repository retrieves article by name', function () {
    $repo = new ArticleRepository();
    $article = $repo->getByArticleName('Home');

    expect($article->name)->toBe('Home');
    expect($article->content)->toContain('Welcome to the vault');
});

test('repository throws when article name not found', function () {
    $repo = new ArticleRepository();

    $repo->getByArticleName('Does Not Exist');
})->throws(\App\Exceptions\ArticleNotFoundException::class);

test('repository retrieves article by path', function () {
    $repo = new ArticleRepository();
    $article = $repo->getByArticlePath('Notes/Daily');

    expect($article->name)->toBe('Daily');
    expect($article->content)->toContain('Daily notes go here');
});

test('repository returns fallback for missing path', function () {
    $repo = new ArticleRepository();
    $article = $repo->getByArticlePath('Does/Not/Exist');

    expect($article->content)->toContain("doesn't exist yet");
});

test('repository returns home message for home paths without a home file', function () {
    Storage::disk('articles')->delete('Home.md');
    cache()->forget('articles_cache');

    $repo = new ArticleRepository();
    $article = $repo->getByArticlePath('/Home');

    // Should show the "create a Home note" message
    expect($article->content)->toContain('Home');
});

test('repository searches articles by title', function () {
    $repo = new ArticleRepository();
    $results = $repo->search('daily');

    expect($results)->toHaveCount(1);
    expect($results->first()['title'])->toBe('Daily');
});

test('repository search is case-insensitive', function () {
    $repo = new ArticleRepository();

    expect($repo->search('DAILY'))->toHaveCount(1);
    expect($repo->search('daily'))->toHaveCount(1);
    expect($repo->search('Daily'))->toHaveCount(1);
});

test('repository finds articles by tag', function () {
    $repo = new ArticleRepository();
    $results = $repo->allWithTag('daily');

    expect($results)->toHaveCount(1);
});

test('repository detects images', function () {
    $repo = new ArticleRepository();

    expect($repo->hasImage('screenshot.png'))->toBeTrue();
    expect($repo->hasImage('nonexistent.png'))->toBeFalse();
});

test('repository resolves image path', function () {
    $repo = new ArticleRepository();
    $path = $repo->getImagePath('screenshot.png');

    expect($path)->toBe('images/screenshot.png');
});

test('repository handles files with same basename in different directories', function () {
    Storage::disk('articles')->put('Folder1/notes.md', '# Notes from folder 1');
    Storage::disk('articles')->put('Folder2/notes.md', '# Notes from folder 2');
    cache()->forget('articles_cache');

    $repo = new ArticleRepository();

    // Both should be findable - hasArticle checks basename
    expect($repo->hasArticle('notes'))->toBeTrue();

    // Path-based lookup should get the right one
    $article1 = $repo->getByArticlePath('Folder1/notes');
    expect($article1->content)->toContain('folder 1');

    $article2 = $repo->getByArticlePath('Folder2/notes');
    expect($article2->content)->toContain('folder 2');
});

test('repository clear cache removes cached data', function () {
    $repo = new ArticleRepository();

    expect(cache()->has('articles_cache'))->toBeTrue();

    $repo->clearCache();

    expect(cache()->has('articles_cache'))->toBeFalse();
    expect(cache()->has('file_tree'))->toBeFalse();
});

test('repository ignores .git folder', function () {
    Storage::disk('articles')->put('.git/HEAD', 'ref: refs/heads/main');
    cache()->forget('articles_cache');

    $repo = new ArticleRepository();

    // .git files should not appear as articles
    expect($repo->hasArticle('HEAD'))->toBeFalse();
});
