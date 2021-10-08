<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuBuilder
{
    public static function build(): array
    {
        return static::createStructure('/');
    }

    private static function createStructure(string $path): array
    {
        $directories = collect(Storage::disk('articles')->directories($path))
            ->reject(fn($path) => Str::startsWith($path, '.obsidian'))
            ->map(function($directory){
            return [
                'title' => basename($directory),
                'children' => static::createStructure($directory)
            ];
        });

        $files = collect(Storage::disk('articles')->files($path))->map(function($file){
            $title = pathinfo($file)['filename'];

            return [
                'title' => $title,
                'filename' => wikilink($title)
            ];
        });

        return $directories->merge($files)->toArray();
    }
}
