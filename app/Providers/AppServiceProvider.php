<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Désactive l'enveloppe {"data": ...} sur tous les JsonResource.
        // Les réponses d'index utilisent déjà response()->json(['data' => ...]) explicitement.
        JsonResource::withoutWrapping();
    }
}
