<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class MainTable extends Component
{
    public $fields;
    public $value;
    public $details_route;
    public $edit_route;
    public $delete_route;
    public $reactive_route;

    public function __construct($fields, $value, $details_route = null, $edit_route = null, $delete_route = null, $reactive_route = null)
    {
        $this->fields = $fields;
        $this->value = $value;
        $this->details_route = $details_route;
        $this->edit_route = $edit_route;
        $this->delete_route = $delete_route;
        $this->reactive_route = $reactive_route;
    }

    public function render(): View
    {
        return view('components.main_table', [
            'fields' => $this->fields,
            'value' => $this->value,
            'details_route' => $this->details_route,
            'edit_route' => $this->edit_route,
            'delete_route' => $this->delete_route,
            'reactive_route' => $this->reactive_route,
        ]);
    }
}
