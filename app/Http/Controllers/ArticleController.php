<?php

namespace App\Http\Controllers;

use App\Jobs\RetrieveArticle;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index($articlePath = '')
    {
        return view('obsidian_article', [
            'isIndex' => $articlePath == '',
            'article' => RetrieveArticle::dispatchSync($articlePath)
        ]);
    }
}
