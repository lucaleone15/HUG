<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Api\Admin\CampaignStatsController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\EntrepriseController as AdminEntrepriseController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\SubmissionController;
use Illuminate\Support\Facades\Route;

// --- Auth ---
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);
});

// --- Public ---
Route::get('/leaderboard', [LeaderboardController::class, 'index']);
Route::get('/stats',       [StatsController::class,      'index']);
Route::post('/analytics',  [AnalyticsController::class,  'store']);

// --- Admin ---
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::apiResource('entreprises', AdminEntrepriseController::class);
    Route::post('entreprises/{entreprise}/send-kit', [AdminEntrepriseController::class, 'sendKit']);

    Route::get('submissions',              [SubmissionController::class, 'index']);
    Route::get('submissions/{submission}', [SubmissionController::class, 'show']);

    Route::get('analytics', [AdminAnalyticsController::class, 'index']);

    Route::get('campaign-stats', [CampaignStatsController::class, 'show']);
    Route::put('campaign-stats', [CampaignStatsController::class, 'update']);

    Route::get('report', [ReportController::class, 'show']);
});
