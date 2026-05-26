<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminEntrepriseResource;
use App\Models\Entreprise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EntrepriseController extends Controller
{
    public function index(): JsonResponse
    {
        $entreprises = Entreprise::withCount([
                'submissions as eligible_count'  => fn($q) => $q->where('is_eligible', true),
                'submissions as submission_count',
            ])
            ->latest()
            ->paginate(25);

        return response()->json([
            'data' => AdminEntrepriseResource::collection($entreprises->items()),
            'meta' => [
                'total'        => $entreprises->total(),
                'per_page'     => $entreprises->perPage(),
                'current_page' => $entreprises->currentPage(),
                'last_page'    => $entreprises->lastPage(),
            ],
        ]);
    }

    public function show(int $id): AdminEntrepriseResource
    {
        $entreprise = Entreprise::findOrFail($id);
        $entreprise->loadCount([
            'submissions as eligible_count'  => fn($q) => $q->where('is_eligible', true),
            'submissions as submission_count',
        ]);

        return new AdminEntrepriseResource($entreprise);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'slug'            => 'nullable|string|max:255|unique:entreprises,slug|regex:/^[a-z0-9-]+$/',
            'type'            => 'required|in:banque,assurance,industrie,commerce,service,technologie,sante,education,autre',
            'logo_url'        => 'nullable|string|max:2048',
            'logo'            => 'nullable|image|max:2048',
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

        if ($request->hasFile('logo')) {
            $data['logo_url'] = Storage::url(
                $request->file('logo')->store('logos', 'public')
            );
        }

        $data['slug'] ??= $this->uniqueSlug(Str::slug($request->name));

        $entreprise = Entreprise::create($data);

        return response()->json(new AdminEntrepriseResource($entreprise), 201);
    }

    public function update(Request $request, int $id): AdminEntrepriseResource
    {
        $entreprise = Entreprise::findOrFail($id);

        $data = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'slug'            => "sometimes|string|max:255|unique:entreprises,slug,{$id}|regex:/^[a-z0-9-]+$/",
            'type'            => 'sometimes|in:banque,assurance,industrie,commerce,service,technologie,sante,education,autre',
            'logo_url'        => 'nullable|string|max:2048',
            'logo'            => 'nullable|image|max:2048',
            'primary_color'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'employee_count'  => 'nullable|integer|min:1',
            'contact_name'    => 'nullable|string|max:255',
            'contact_email'   => 'nullable|email|max:255',
            'is_active'       => 'boolean',
            'is_labelled'     => 'boolean',
            'is_validated'    => 'boolean',
            'trophy_rank'     => 'nullable|integer|min:1|max:255',
            'wants_trophy'    => 'boolean',
            'rdv_url'         => 'nullable|url|max:2048',
            'rdv_date'        => 'nullable|date',
        ]);

        if ($request->hasFile('logo')) {
            // Supprimer l'ancien fichier si stocké localement
            if ($entreprise->logo_url && str_starts_with($entreprise->logo_url, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $entreprise->logo_url));
            }
            $data['logo_url'] = Storage::url(
                $request->file('logo')->store('logos', 'public')
            );
        }

        $justValidated = !$entreprise->is_validated && ($data['is_validated'] ?? false);

        $entreprise->update($data);

        if ($justValidated && $entreprise->contact_email) {
            $locale = in_array($request->locale, ['fr', 'de', 'it', 'en']) ? $request->locale : 'fr';
            app()->setLocale($locale);
            Mail::to($entreprise->contact_email)
                ->send(new \App\Mail\CompanyConfirmationLink($entreprise));
        }

        return new AdminEntrepriseResource($entreprise);
    }

    public function destroy(int $id): Response
    {
        $entreprise = Entreprise::findOrFail($id);

        if ($entreprise->logo_url && str_starts_with($entreprise->logo_url, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $entreprise->logo_url));
        }

        $entreprise->delete();

        return response()->noContent();
    }

    public function sendKit(int $id): JsonResponse
    {
        $entreprise = Entreprise::findOrFail($id);

        // TODO: Mail::to($entreprise->contact_email)->send(new KitPromoMail($entreprise));

        return response()->json([
            'message' => "Kit envoyé à {$entreprise->contact_email}",
        ]);
    }

    public function sendLink(Request $request, int $id): JsonResponse
    {
        $entreprise = Entreprise::findOrFail($id);

        abort_if(! $entreprise->is_active, 422, 'Entreprise non active.');
        abort_if(! $entreprise->contact_email, 422, 'Aucun email de contact défini.');

        $locale = in_array($request->locale, ['fr', 'de', 'it', 'en']) ? $request->locale : 'fr';
        app()->setLocale($locale);

        Mail::to($entreprise->contact_email)
            ->send(new \App\Mail\CompanyConfirmationLink($entreprise));

        return response()->json([
            'message' => "Lien envoyé à {$entreprise->contact_email}",
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
