<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collecte;
use App\Models\Entreprise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CollecteController extends Controller
{
    public function index(int $id): JsonResponse
    {
        $entreprise = Entreprise::findOrFail($id);

        $collectes = $entreprise->collectes()->latest()->get()->map(fn(Collecte $c) => [
            'id'         => $c->id,
            'ondoc_url'  => $c->ondoc_url,
            'rdv_date'   => $c->rdv_date?->toDateString(),
            'label'      => $c->label,
            'is_active'  => $c->is_active,
            'created_at' => $c->created_at->toDateTimeString(),
        ]);

        return response()->json(['data' => $collectes]);
    }

    public function store(Request $request, int $id): JsonResponse
    {
        $entreprise = Entreprise::findOrFail($id);

        $data = $request->validate([
            'ondoc_url' => 'required|url|max:2048',
            'rdv_date'  => 'nullable|date',
            'label'     => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $collecte = $entreprise->collectes()->create($data);

        return response()->json([
            'id'         => $collecte->id,
            'ondoc_url'  => $collecte->ondoc_url,
            'rdv_date'   => $collecte->rdv_date?->toDateString(),
            'label'      => $collecte->label,
            'is_active'  => $collecte->is_active,
            'created_at' => $collecte->created_at->toDateTimeString(),
        ], 201);
    }

    public function update(Request $request, Collecte $collecte): JsonResponse
    {
        $data = $request->validate([
            'ondoc_url' => 'sometimes|url|max:2048',
            'rdv_date'  => 'nullable|date',
            'label'     => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $collecte->update($data);

        return response()->json([
            'id'         => $collecte->id,
            'ondoc_url'  => $collecte->ondoc_url,
            'rdv_date'   => $collecte->rdv_date?->toDateString(),
            'label'      => $collecte->label,
            'is_active'  => $collecte->is_active,
            'created_at' => $collecte->created_at->toDateTimeString(),
        ]);
    }

    public function destroy(Collecte $collecte): Response
    {
        $collecte->delete();

        return response()->noContent();
    }
}
