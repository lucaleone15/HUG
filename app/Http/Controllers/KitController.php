<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class KitController extends Controller
{
    public function index(): View
    {
        return view('kit-promo');
    }
}
