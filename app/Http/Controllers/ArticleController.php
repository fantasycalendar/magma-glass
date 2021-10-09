<?php

namespace App\Http\Controllers;

use App\Jobs\RetrieveArticle;
use App\Services\ArticleCache;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index($articlePath = '')
    {
        dd(ArticleCache::populate()['links']);

        return view('show_article', [
            'isIndex' => $articlePath == '',
            'article' => RetrieveArticle::dispatchSync($articlePath)
        ]);
    }

    public function noSuchArticle(Request $request)
    {
        $article = $request->get('articlePath');

        return "Article '$article' does not exist";
    }

    public function tag(Request $request)
    {
        return view('tag_search', [
            'tagSearch' => $request->input('tag'),
            'results' => ArticleCache::allWithTag($request->input('tag'))
        ]);
    }
}
