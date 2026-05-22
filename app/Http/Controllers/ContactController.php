<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'type'    => 'required|string',
            'message' => 'required|string|max:5000',
        ]);

        // TODO: envoi mail ou stockage de la demande

        return back()->with('success', 'Votre message a bien été envoyé.');
    }
}
