<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('contact');
    }

    public function store(Request $request): RedirectResponse
    {
        App::setLocale(in_array($request->locale, ['fr', 'de', 'it', 'en']) ? $request->locale : 'fr');

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'type'    => 'required|string|in:type_general,type_partnership,type_technical,type_eligibility,type_collecte_request,type_other',
            'message' => 'required|string|max:5000',
        ]);

        $locale = in_array($request->locale, ['fr', 'de', 'it', 'en']) ? $request->locale : 'fr';

        Mail::to('info@donnez-votre-sang.ch')->send((new ContactFormMail(
            senderName:    $request->name,
            senderEmail:   $request->email,
            type:          $request->type,
            userMessage:   $request->message,
        ))->locale($locale));

        return back()->with('success', 'Votre message a bien été envoyé.');
    }
}
