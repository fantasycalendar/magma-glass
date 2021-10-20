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
        dd(RetrieveArticle::dispatchSync('Orlbar/Temple of the Undying Magister')->getBlocks());

        return;
    }

    public function index($articlePath = '')
    {
        if(request()->input('cold_boot')) {
            logger()->debug('Cold booting, clearing cache.');
            cache()->forget('articles_cache');
            cache()->forget('file_tree');
            logger()->debug('Cache cleared.');
        }

        return view('show_article', [
            'isIndex' => $articlePath == ''
        ]);
    }

    public function articleJson(Request $request)
    {
        logger()->debug('-------------new request!------------');
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

    public function tag($tag)
    {
        return view('tag_search', [
            'tagSearch' => $tag,
            'results' => ArticleCache::allWithTag($tag)
        ]);
    }

    public function linkData()
    {
        return ArticleCache::populate();
    }

    public function search()
    {
        return ArticleCache::populate()['articles']->filter(function($article){
            return Str::contains(strtolower($article['title']), strtolower(request()->input('searchTerm')));
        })->values();
    }
}
