<?php

namespace App\Http\Livewire;

use App\Models\Article;
use Livewire\Component;

class ArticlePage extends Component
{
    public $articlePath;
    protected $listeners = ['changeArticle'];

    private Article $article;

    public function mount($articlePath = 'Home')
    {
        $this->articlePath = $articlePath;
        $this->changeArticle($articlePath);
    }

    public function render()
    {
        return view('livewire.article-page',[
            'article' => $this->article
        ]);
    }

    public function changeArticle($articlePath)
    {
        logger()->info($articlePath);
        $this->article = app()->make('articles')->getByArticlePath($articlePath);
    }
}
