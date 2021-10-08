<?php

namespace App\View\Components;

use App\Services\MenuBuilder;
use Illuminate\View\Component;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('layouts.app', [
            'menuJson' => MenuBuilder::build()
        ]);
    }
}
