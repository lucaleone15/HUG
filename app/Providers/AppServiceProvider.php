<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        JsonResource::withoutWrapping();

        View::composer('emails.*', function ($view) {
            $view->with('EC', [
                'brand'        => '#E30613',
                'brand_dark'   => '#C5000E',
                'brand_border' => '#A8000C',
                'hero'         => '#0D0D0D',
                'ink'          => '#111111',
                'dark'         => '#111111',
                'page_bg'      => '#F0EDE8',
                'recap_bg'     => '#F0EBE0',
                'recap_border' => '#C4B080',
                'white'        => '#ffffff',
                'card_bg'      => '#fafafa',
                'dark_border'  => '#2A2A2A',
                'recap_text'   => '#8A7A60',
                'recap_ink'    => '#1A1A1A',
                'body_text'    => '#555555',
                'text'         => '#333333',
                'step_text'    => '#666666',
                'label'        => '#aaaaaa',
                'muted'        => '#888888',
                'on_dark_lo'   => 'rgba(255,255,255,0.35)',
                'on_dark_mid'  => 'rgba(255,255,255,0.4)',
                'on_dark_hi'   => 'rgba(255,255,255,0.65)',
                'on_dark_full' => '#ffffff',
                'on_brand_lo'  => 'rgba(255,255,255,0.45)',
                'on_brand_mid' => 'rgba(255,255,255,0.5)',
                'on_brand_hi'  => 'rgba(255,255,255,0.65)',
                'status_text'  => 'rgba(255,255,255,0.6)',
                'status_date'  => 'rgba(255,255,255,0.75)',
                'on_dark_sep'  => 'rgba(255,255,255,0.15)',
                'cross'        => 'rgba(255,255,255,0.35)',
                'body_font'    => "'Cooper Hewitt','Helvetica Neue',Helvetica,Arial,sans-serif",
            ]);
        });
    }
}
