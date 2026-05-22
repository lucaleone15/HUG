<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardResource;
use App\Models\Entreprise;
use Illuminate\Http\JsonResponse;

class LeaderboardController extends Controller
{
    public function index(): JsonResponse
    {
        $entreprises = Entreprise::where('is_active', true)
            ->withCount([
                'submissions as eligible_count' => fn($q) => $q->where('is_eligible', true),
            ])
            ->orderByDesc('eligible_count')
            ->get();

        return response()->json(LeaderboardResource::collection($entreprises));
    }
}
