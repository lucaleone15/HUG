<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminEntrepriseResource;
use App\Models\Entreprise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TropheeController extends Controller
{
    public function index(): JsonResponse
    {
        $ranked   = Entreprise::whereNotNull('trophy_rank')->orderBy('trophy_rank')->get();
        $unranked = Entreprise::whereNull('trophy_rank')->where('is_active', true)->orderBy('name')->get();

        return response()->json([
            'ranked'   => AdminEntrepriseResource::collection($ranked),
            'unranked' => AdminEntrepriseResource::collection($unranked),
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ranking'   => 'required|array',
            'ranking.*' => 'integer|exists:entreprises,id',
        ]);

        Entreprise::whereNotNull('trophy_rank')->update(['trophy_rank' => null]);

        foreach ($data['ranking'] as $pos => $id) {
            Entreprise::where('id', $id)->update(['trophy_rank' => $pos + 1]);
        }

        return response()->json(['message' => 'Classement mis à jour.']);
    }
}
