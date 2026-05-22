<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AnalyticsController extends Controller
{
    // Toujours 204 — ne jamais bloquer le front (fire & forget)
    public function store(Request $request): Response
    {
        try {
            $data = $request->validate([
                'type'          => 'required|in:' . implode(',', AnalyticsEvent::TYPES),
                'entreprise_id' => 'nullable|exists:entreprises,id',
                'session_token' => 'nullable|string|max:36',
                'metadata'      => 'nullable|array',
            ]);

            AnalyticsEvent::create($data);
        } catch (\Throwable) {
            // Silently swallowed — on ne bloque jamais le front
        }

        return response()->noContent();
    }
}
