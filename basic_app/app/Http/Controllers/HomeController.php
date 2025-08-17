<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session; // ✅ Correct import

use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Show the application dashboard.
     *
     * @param string $lang
     * @return \Illuminate\Contracts\Support\Renderable
     */

public function index()
{

    return view('home');


}

}
