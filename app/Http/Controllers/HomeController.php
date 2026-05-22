<?php

namespace App\Http\Controllers;

use App\Models\CampaignStats;
use App\Models\Entreprise;
use App\Models\Submission;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $stats = CampaignStats::getInstance();
        $eligibleCount = Submission::where('is_eligible', true)->count();
        $entreprisesCount = Entreprise::where('is_active', true)->count();

        return view('home', compact('stats', 'eligibleCount', 'entreprisesCount'));
    }
}
