<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::githubWebhooks('update-source');

Route::get('/image/{path}', [\App\Http\Controllers\ImageController::class, 'image'])->name('image')->where('path', '.*');
Route::get('/tag/{tag}', [\App\Http\Controllers\ArticleController::class, 'tag'])->name('tag')->where('tag', '.*');
Route::get('/linkData', [\App\Http\Controllers\ArticleController::class, 'linkData'])->name('linkData');
Route::get('/get-article/', [\App\Http\Controllers\ArticleController::class, 'articleJson'])->name('articleJson');
Route::get('/search/', [\App\Http\Controllers\ArticleController::class, 'search'])->name('search');
Route::get('/a/{articlePath?}', [\App\Http\Controllers\ArticleController::class, 'index'])->name('article')->where('articlePath', '.*');
Route::redirect('/', '/a/');

require __DIR__ . '/auth.php';
