<?php

namespace App\Console\Commands;

use App\Models\CampaignStats;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    protected $signature = 'app:create-admin
        {--name=      : Nom affiché}
        {--email=     : Adresse email}
        {--password=  : Mot de passe}';

    protected $description = 'Crée un compte administrateur HUG (usage production)';

    public function handle(): int
    {
        $name     = $this->option('name')     ?? $this->ask('Nom');
        $email    = $this->option('email')    ?? $this->ask('Email');
        $password = $this->option('password') ?? $this->secret('Mot de passe');

        if (User::where('email', $email)->exists()) {
            $this->error("Un utilisateur avec l'email « {$email} » existe déjà.");
            return self::FAILURE;
        }

        User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);

        CampaignStats::getInstance();

        $this->info("Admin créé : {$name} <{$email}>");
        $this->line('Connectez-vous sur /admin/login avec ces identifiants.');

        return self::SUCCESS;
    }
}
