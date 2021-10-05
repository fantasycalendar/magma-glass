<?php

namespace App\Http\Controllers;

use App\Jobs\RetrieveArticle;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        return view('obsidian_article', [
            'article' => RetrieveArticle::dispatchSync($request->input('article') ?? '')
        ]);
    }
}
