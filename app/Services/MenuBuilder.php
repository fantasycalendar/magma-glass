<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuBuilder
{
    private static $icons = [
        'md' => 'fa-file-alt text-blue-400 dark:text-blue-500 group-hover:text-blue-500 dark:group-hover:text-blue-300',
        'image' => 'fa-file-image text-gray-400 dark:text-gray-400 group-hover:text-gray-500 dark:group-hover:text-gray-100',
        'document' => 'fa-file-pdf text-red-400 dark:text-red-500 group-hover:text-red-500 dark:group-hover:text-red-300',
        'folder' => 'fa-folder text-yellow-400 dark:text-yellow-600 group-hover:text-yellow-500 dark:group-hover:text-orange-300',
        'audio' => 'fa-file-audio text-purple-400 dark:text-purple-500 group-hover:text-purple-500 dark:group-hover:text-purple-300',
        'video' => 'fa-file-audio text-green-400 dark:text-green-500 group-hover:text-green-500 dark:group-hover:text-green-300',
        'multimedia' => 'fa-photo-video text-indigo-400 dark:text-indigo-500 group-hover:text-indigo-500 dark:group-hover:text-indigo-300',
        'archive' => 'fa-photo-video text-indigo-400 dark:text-indigo-500 group-hover:text-indigo-500 dark:group-hover:text-indigo-300',
        'unknown' => 'fa-file text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-300',
    ];

    private static $fileTypesByExtension = [
        'md' => 'md',
        'png' => 'image',
        'jpg' => 'image',
        'jpeg' => 'image',
        'gif' => 'image',
        'bmp' => 'image',
        'svg' => 'image',
        'mp3' => 'audio',
        'wav' => 'audio',
        'm4a' => 'audio',
        'ogg' => 'audio',
        '3gp' => 'audio',
        'flac' => 'audio',
        'mp4' => 'video',
        'ogv' => 'video',
        'webm' => 'multimedia',
        'pdf' => 'document',
        'zip' => 'archive',
        '7z' => 'archive',
        'rar' => 'archive',
        'gz' => 'archive',
    ];

    public static function build()
    {
        return cache()->remember('file_tree', config('magmaglass.cache_ttl'), function() {
            return static::createStructure('/');
        });
    }

    private static function createStructure(string $path)
    {
        $directories = collect(Storage::disk('articles')->directories($path))
            ->reject(fn($path) => Str::startsWith($path, '.obsidian'))
            ->reject(fn($path) => in_array(explode('/', $path)[0] ?? '', config('magmaglass.ignored_paths')))
            ->map(function($directory){
            return [
                'title' => basename($directory),
                'children' => static::createStructure($directory),
                'icon' => static::$icons['folder'],
                'path' => $directory
            ];
        });

        $files = collect(Storage::disk('articles')->files($path))->map(function($file){
            $fileInfo = pathinfo($file);
            $title = $fileInfo['filename'];
            $icon = in_array(strtolower($title), ['home', 'start here', 'index'])
                ? 'fa-home'
                : static::resolveIcon($fileInfo['extension']);

            return [
                'title' => $title,
                'url' => wikilink($title),
                'icon' => $icon,
                'path' => $file
            ];
        });

        return $directories->merge($files)->map(function($item){
            // Prepend an 'l' because this is used as a 'ref' in Alpine, and
            // JavaScript properties cannot start with a number.
            $item['ref'] = 'l' . sha1($item['path']);

            return $item;
        });
    }

    /**
     * Resolves a list of icon classes based on file extension
     *
     * @param $extension
     * @return string
     */
    public static function resolveIcon($extension): string
    {
        return self::$icons[self::$fileTypesByExtension[$extension]]
            ?? self::$icons['unknown'];
    }
}
