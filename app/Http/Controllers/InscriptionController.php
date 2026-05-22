<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InscriptionController extends Controller
{
    public function index(): View
    {
        return view('inscription');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:banque,assurance,industrie,commerce,service,technologie,sante,education,autre',
            'employee_count'  => 'nullable|integer|min:1|max:999999',
            'contact_name'    => 'required|string|max:255',
            'contact_email'   => 'required|email|max:255',
            'primary_color'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo'            => 'nullable|image|max:2048',
            'logo_url'        => 'nullable|string|max:2048',
        ]);

        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $logoUrl = Storage::url(
                $request->file('logo')->store('logos', 'public')
            );
        } elseif ($request->filled('logo_url')) {
            $logoUrl = $request->logo_url;
        }

        Entreprise::create([
            'name'            => $request->name,
            'slug'            => $this->uniqueSlug(Str::slug($request->name)),
            'type'            => $request->type,
            'employee_count'  => $request->employee_count,
            'contact_name'    => $request->contact_name,
            'contact_email'   => $request->contact_email,
            'primary_color'   => $request->primary_color ?? '#E30613',
            'secondary_color' => $request->secondary_color,
            'logo_url'        => $logoUrl,
            'is_active'       => false,
            'is_validated'    => false,
            'is_labelled'     => false,
        ]);

        return redirect()->route('inscription')->with('success', true);
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
