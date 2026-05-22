<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use Illuminate\View\View;

class LabelController extends Controller
{
    public function index(): View
    {
        $entreprises = Entreprise::where('is_labelled', true)
            ->where('is_active', true)
            ->get();

        return view('label', compact('entreprises'));
    }
}
