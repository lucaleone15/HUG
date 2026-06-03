<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use Illuminate\View\View;

class TropheeController extends Controller
{
    public function index(): View
    {
        $winners = Entreprise::whereNotNull('trophy_rank')
            ->where('is_active', true)
            ->where('is_public', true)
            ->orderBy('trophy_rank')
            ->get();

        return view('trophee', compact('winners'));
    }
}
