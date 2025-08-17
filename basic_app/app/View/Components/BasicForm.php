<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BasicForm extends Component
{
    /**
     * Create a new component instance.
     */
    public array $fields;
    public string $action;
    public $model,$method;
    public function __construct($fields,$action,$model = null,$method ="POST")
    {
        $this->fields = $fields;
        $this->action = $action;
        $this->model =$model;
        $this->method =$method;
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.BasicForm');
    }
}
