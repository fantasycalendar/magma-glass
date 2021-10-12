<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('test', function(){
//    dd(\App\Services\MenuBuilder::build());
//});
Route::view('test', 'test');
Route::get('/image/{path}', [\App\Http\Controllers\ImageController::class, 'image'])->name('image');
Route::get('/tag/', [\App\Http\Controllers\ArticleController::class, 'tag'])->name('tag');
Route::get('/linkData', [\App\Http\Controllers\ArticleController::class, 'linkData'])->name('linkData');
Route::get('/get-article/', [\App\Http\Controllers\ArticleController::class, 'articleJson'])->name('articleJson');
Route::get('/search/', [\App\Http\Controllers\ArticleController::class, 'search'])->name('search');
Route::get('/no_such_article', [\App\Http\Controllers\ArticleController::class, 'noSuchArticle'])->name('no_such_article');
Route::get('/a/{articlePath?}', [\App\Http\Controllers\ArticleController::class, 'index'])->name('article')->where('articlePath', '.*');
Route::view('/', 'show_article');

require __DIR__.'/auth.php';
