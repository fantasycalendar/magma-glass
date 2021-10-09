<?php

namespace App\Http\Controllers;

use App\Services\ArticleCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function image($image)
    {
        return response()->file(Storage::disk('articles')->path(ArticleCache::populate()[$image]));
    }
}
