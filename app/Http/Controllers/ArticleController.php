<?php

namespace App\Http\Controllers;

use App\Jobs\RetrieveArticle;
use App\Services\ArticleCache;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function test()
    {
//        dd(RetrieveArticle::dispatchSync('Orlbar/Temple of the Undying Magister')->getBlocks());
        dd(RetrieveArticle::dispatchSync('How to/Format your notes')->getParsed());

        return;
    }

    public function index()
    {
        app()->make('articles')->clearCache();

        return view('show_article');
    }

    public function articleJson(Request $request)
    {
        logger()->debug('-------------new request!------------');
        $article = RetrieveArticle::dispatchSync($request->input('articlePath') ?? '');

        return [
            'title' => $article->name,
            'content' => $article->getParsed(),
            'path' => $request->input('articlePath') ?? '',
            'links' => app()->make('articles')->getLinks()
        ];
    }

    public function noSuchArticle(Request $request)
    {
        $article = $request->get('articlePath');

        return "Article '$article' does not exist";
    }

    public function tag($tag)
    {
        return view('tag_search', [
            'tagSearch' => $tag,
            'results' => app()->make('articles')->allWithTag($tag)
        ]);
    }

    public function linkData()
    {
        return app()->make('articles')->getLinkData();
    }

    public function search()
    {
        return app()->make('articles')->search(request()->input('searchTerm'));
    }
}
