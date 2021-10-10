<?php

namespace App\Http\Controllers;

use App\Jobs\RetrieveArticle;
use App\Services\ArticleCache;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index($articlePath = '')
    {
        return view('show_article', [
            'isIndex' => $articlePath == '',
//            'article' => RetrieveArticle::dispatchSync(urldecode($articlePath))
        ]);
    }

    public function articleJson(Request $request)
    {
        $article = RetrieveArticle::dispatchSync($request->input('articlePath') ?? '');

        return [
            'title' => $article->name,
            'content' => $article->getParsed(),
            'path' => $request->input('articlePath') ?? '',
            'links' => ArticleCache::populate()['links']
        ];
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

    public function linkData()
    {
        return ArticleCache::populate();
    }
}
