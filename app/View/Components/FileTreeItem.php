<?php

namespace App\View\Components;

use App\Services\MenuBuilder;
use Illuminate\View\Component;

class FileTreeItem extends Component
{
    private $contents;
    /**
     * @var int|mixed
     */
    private $level;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($contents = null, $level = 0)
    {
        $this->contents = $contents;
        $this->level = $level + 1;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.file-tree-item', [
            'contents' => collect($this->contents),
            'level' => $this->level,
        ]);
    }
}
