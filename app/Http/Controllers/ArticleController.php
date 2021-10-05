<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        return view('obsidian_article',[
            'title' => 'A title',
            'content' => 'Some content!'
        ]);
    }
}
