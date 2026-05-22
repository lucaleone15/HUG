<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EntrepriseResource;
use App\Models\Entreprise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class EntrepriseController extends Controller
{
    public function index(): JsonResponse
    {
        $entreprises = Entreprise::withCount([
                'submissions as eligible_count' => fn($q) => $q->where('is_eligible', true),
                'submissions as submission_count',
            ])
            ->latest()
            ->paginate(25);

        return response()->json([
            'data' => EntrepriseResource::collection($entreprises->items()),
            'meta' => [
                'total'        => $entreprises->total(),
                'per_page'     => $entreprises->perPage(),
                'current_page' => $entreprises->currentPage(),
                'last_page'    => $entreprises->lastPage(),
            ],
        ]);
    }

    public function show(Entreprise $entreprise): EntrepriseResource
    {
        $entreprise->loadCount([
            'submissions as eligible_count'   => fn($q) => $q->where('is_eligible', true),
            'submissions as submission_count',
        ]);

        return new EntrepriseResource($entreprise);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'slug'            => 'nullable|string|max:255|unique:entreprises,slug|regex:/^[a-z0-9-]+$/',
            'type'            => 'required|in:banque,assurance,industrie,commerce,service,technologie,sante,education,autre',
            'logo_url'        => 'nullable|url',
            'primary_color'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'employee_count'  => 'nullable|integer|min:1',
            'contact_name'    => 'nullable|string|max:255',
            'contact_email'   => 'nullable|email|max:255',
            'is_active'       => 'boolean',
            'is_labelled'     => 'boolean',
            'is_validated'    => 'boolean',
            'trophy_rank'     => 'nullable|integer|min:1|max:255',
        ]);

        $data['slug'] ??= $this->uniqueSlug(Str::slug($request->name));

        $entreprise = Entreprise::create($data);

        return response()->json(new EntrepriseResource($entreprise), 201);
    }

    public function update(Request $request, Entreprise $entreprise): EntrepriseResource
    {
        $data = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'slug'            => "sometimes|string|max:255|unique:entreprises,slug,{$entreprise->id}|regex:/^[a-z0-9-]+$/",
            'type'            => 'sometimes|in:banque,assurance,industrie,commerce,service,technologie,sante,education,autre',
            'logo_url'        => 'nullable|url',
            'primary_color'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'employee_count'  => 'nullable|integer|min:1',
            'contact_name'    => 'nullable|string|max:255',
            'contact_email'   => 'nullable|email|max:255',
            'is_active'       => 'boolean',
            'is_labelled'     => 'boolean',
            'is_validated'    => 'boolean',
            'trophy_rank'     => 'nullable|integer|min:1|max:255',
        ]);

        $entreprise->update($data);

        return new EntrepriseResource($entreprise);
    }

    public function destroy(Entreprise $entreprise): Response
    {
        $entreprise->delete();

        return response()->noContent();
    }

    public function sendKit(Entreprise $entreprise): JsonResponse
    {
        // TODO: envoyer un email à $entreprise->contact_email avec le lien du kit
        // Mail::to($entreprise->contact_email)->send(new KitPromoMail($entreprise));

        return response()->json([
            'message' => "Kit envoyé à {$entreprise->contact_email}",
        ]);
    }

    private function uniqueSlug(string $base): string
    {
        $slug = $base ?: 'entreprise';
        $i = 1;
        while (Entreprise::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
