<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class EmailGeneratorController extends Controller
{
    public function index(): View
    {
        return view('email-generator');
    }
}
