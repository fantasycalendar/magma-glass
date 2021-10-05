<?php

namespace App\Http\Controllers;

use App\Jobs\RetrieveArticle;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index($article)
    {
        return view('obsidian_article', [
            'article' => RetrieveArticle::dispatchSync($article ?? '')
        ]);
    }
}
