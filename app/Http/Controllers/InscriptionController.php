<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Mail\NewRegistrationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
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
        App::setLocale(in_array($request->locale, ['fr', 'de', 'it', 'en']) ? $request->locale : 'fr');

        $request->validate([
            'name'            => 'required|string|max:255',
            'type'            => 'required|in:banque,assurance,industrie,commerce,service,technologie,sante,education,autre',
            'employee_count'  => 'required|integer|min:1|max:999999',
            'contact_name'    => 'required|string|max:255',
            'contact_email'   => 'required|email|max:255',
            'primary_color'   => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo'            => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,svg|max:2048',
            'logo_url'        => 'nullable|url|max:2048',
            'wants_trophy'    => 'boolean',
            'is_public'       => 'boolean',
        ]);

        $logoUrl = null;
        if ($request->hasFile('logo')) {
            $logoUrl = Storage::url(
                $request->file('logo')->store('logos', 'public')
            );
        } elseif ($request->filled('logo_url')) {
            $logoUrl = $request->logo_url;
        }

        $locale = in_array($request->locale, ['fr', 'de', 'it', 'en']) ? $request->locale : 'fr';

        $createData = [
            'name'            => $request->name,
            'slug'            => $this->uniqueSlug(Str::slug($request->name)),
            'access_token'    => Str::random(48),
            'type'            => $request->type,
            'employee_count'  => $request->employee_count,
            'contact_name'    => $request->contact_name,
            'contact_email'   => $request->contact_email,
            'primary_color'   => $request->primary_color,
            'secondary_color' => $request->secondary_color,
            'logo_url'        => $logoUrl,
            'is_active'       => false,
            'is_validated'    => false,
            'is_labelled'     => false,
            'wants_trophy'    => $request->boolean('wants_trophy'),
            'is_public'       => $request->boolean('is_public', true),
            'locale'          => $locale,
        ];

        try {
            $entreprise = Entreprise::create($createData);
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            $createData['slug'] = $this->uniqueSlug(Str::slug($request->name) . '-' . Str::random(4));
            $entreprise = Entreprise::create($createData);
        }

        Mail::to('info@donnez-votre-sang.ch')->send(new NewRegistrationNotification($entreprise));

        return redirect()->route('inscription')->with('success', true);
    }
    //génère le lien unique pour les entreprises
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
