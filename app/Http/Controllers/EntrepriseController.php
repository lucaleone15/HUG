<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use Illuminate\View\View;

class EntrepriseController extends Controller
{
    public function show(Entreprise $entreprise): View
    {
        abort_if(! $entreprise->is_active, 404);

        return view('entreprise.show', compact('entreprise'));
    }
}
