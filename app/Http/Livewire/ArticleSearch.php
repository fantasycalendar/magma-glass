<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ArticleSearch extends Component
{
    public $showSearchResults = false;
    public $searchTerm = '';

    protected $queryString = ['searchTerm'];

    public function render()
    {
        return view('livewire.article-search',[
            'searchResults' => app()->make('articles')->search($this->searchTerm)
        ]);
    }

    public function search($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }
}
