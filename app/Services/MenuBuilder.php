<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuBuilder
{
    private static $icons = [
        'md' => 'fa-file text-blue-400 dark:text-blue-500 group-hover:text-blue-500 dark:group-hover:text-blue-300',
        'png' => 'fa-file-image text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-100',
        'jpg' => 'fa-file-image text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-100',
        'jpeg' => 'fa-file-image text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-100',
        'gif' => 'fa-file-image text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-100',
        'pdf' => 'fa-file-pdf text-red-400 dark:text-red-500 group-hover:text-red-500 dark:group-hover:text-red-300',
    ];

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
            $fileInfo = pathinfo($file);
            $title = $fileInfo['filename'];

            return [
                'title' => $title,
                'filename' => wikilink($title),
                'icon' => self::$icons[$fileInfo['extension']]
            ];
        });

        return $directories->merge($files)->toArray();
    }
}
