<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UploadImage extends Component
{
    public $image;
    public $label;
    public $name;
    public $id;

    public function __construct($image = null, $label = 'Upload Image', $name = 'image', $id = 'image')
    {
        $this->image = $image;
        $this->label = $label;
        $this->name = $name;
        $this->id = $id;
    }

    public function render()
    {
        return view('components.upload-image');
    }
}
