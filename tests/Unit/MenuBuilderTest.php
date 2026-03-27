<?php

use App\Services\MenuBuilder;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('articles');

    Storage::disk('articles')->put('Home.md', '# Home');
    Storage::disk('articles')->put('Notes/Daily.md', '# Daily');
    Storage::disk('articles')->put('Notes/Weekly.md', '# Weekly');
    Storage::disk('articles')->put('images/photo.png', 'fake-image');
    Storage::disk('articles')->put('.obsidian/config.json', '{}');

    cache()->forget('file_tree');
    config(['magmaglass.cache_ttl' => 3600]);
    config(['magmaglass.ignored_paths' => []]);

    // The MenuBuilder uses wikilink() which requires the articles singleton
    $mockRepo = Mockery::mock(\App\Services\ArticleRepository::class);
    $mockRepo->shouldReceive('getLinkData')->andReturn([
        'articles' => collect(),
        'links' => collect(),
        'basename_index' => [
            'home.md' => ['home.md'],
            'daily.md' => ['notes/daily.md'],
            'weekly.md' => ['notes/weekly.md'],
            'photo.png' => ['images/photo.png'],
        ],
    ]);

    app()->instance('articles', $mockRepo);
});

test('menu builder creates a hierarchical structure', function () {
    $tree = MenuBuilder::build();

    expect($tree)->toBeInstanceOf(\Illuminate\Support\Collection::class);
    expect($tree->count())->toBeGreaterThan(0);
});

test('menu builder excludes .obsidian directory', function () {
    $tree = MenuBuilder::build();

    $titles = $tree->pluck('title')->toArray();

    expect($titles)->not->toContain('.obsidian');
});

test('menu builder includes directories as items with children', function () {
    $tree = MenuBuilder::build();

    $directories = $tree->filter(fn ($item) => isset($item['children']));

    expect($directories)->not->toBeEmpty();
});

test('menu builder assigns folder icon to directories', function () {
    $tree = MenuBuilder::build();

    $folder = $tree->firstWhere('title', 'Notes');

    expect($folder)->not->toBeNull();
    expect($folder['icon'])->toContain('fa-folder');
});

test('menu builder assigns home icon to home files', function () {
    $tree = MenuBuilder::build();

    $homeFile = $tree->firstWhere('title', 'Home');

    expect($homeFile)->not->toBeNull();
    expect($homeFile['icon'])->toContain('fa-home');
});

test('menu builder generates unique refs for each item', function () {
    $tree = MenuBuilder::build();

    $refs = $tree->pluck('ref')->toArray();

    expect(count($refs))->toBe(count(array_unique($refs)));
});

test('resolveIcon returns correct icon for known extensions', function () {
    expect(MenuBuilder::resolveIcon('md'))->toContain('fa-file-alt');
    expect(MenuBuilder::resolveIcon('png'))->toContain('fa-file-image');
    expect(MenuBuilder::resolveIcon('pdf'))->toContain('fa-file-pdf');
    expect(MenuBuilder::resolveIcon('mp3'))->toContain('fa-file-audio');
});
