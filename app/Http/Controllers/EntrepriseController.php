<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use Illuminate\View\View;

class EntrepriseController extends Controller
{
    public function show(Entreprise $entreprise): View
    {
        abort_if(! $entreprise->is_active, 404);

        $entreprise->loadCount([
            'submissions',
            'submissions as eligible_count' => fn ($q) => $q->where('is_eligible', true),
        ]);

        $collectes = $entreprise->collectes()->orderByDesc('rdv_date')->orderByDesc('created_at')->get();

        return view('entreprise.show', compact('entreprise', 'collectes'));
    }
}
