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
use App\Http\Controllers\Api\Admin\TropheeController;
use App\Http\Controllers\Api\Admin\CollecteController;
use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);
});

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->middleware('throttle:60,1');
Route::get('/stats',       [StatsController::class,      'index'])->middleware('throttle:60,1');
Route::post('/analytics',  [AnalyticsController::class,  'store'])->middleware('throttle:120,1');

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::get('entreprises',         [AdminEntrepriseController::class, 'index']);
    Route::post('entreprises',        [AdminEntrepriseController::class, 'store']);
    Route::get('entreprises/{id}',    [AdminEntrepriseController::class, 'show']);
    Route::post('entreprises/{id}',   [AdminEntrepriseController::class, 'update']);
    Route::delete('entreprises/{id}', [AdminEntrepriseController::class, 'destroy']);
    Route::post('entreprises/{id}/send-link', [AdminEntrepriseController::class, 'sendLink']);

    Route::get('entreprises/{id}/collectes',  [CollecteController::class, 'index']);
    Route::post('entreprises/{id}/collectes', [CollecteController::class, 'store']);
    Route::put('collectes/{collecte}',         [CollecteController::class, 'update']);
    Route::delete('collectes/{collecte}',      [CollecteController::class, 'destroy']);

    Route::get('trophees',  [TropheeController::class, 'index']);
    Route::put('trophees',  [TropheeController::class, 'reorder']);

    Route::get('submissions',              [SubmissionController::class, 'index']);
    Route::get('submissions/{submission}', [SubmissionController::class, 'show']);

    Route::get('analytics', [AdminAnalyticsController::class, 'index']);

    Route::get('campaign-stats', [CampaignStatsController::class, 'show']);
    Route::put('campaign-stats', [CampaignStatsController::class, 'update']);

    Route::get('report', [ReportController::class, 'show']);

    Route::get('users',         [UserController::class, 'index']);
    Route::post('users',        [UserController::class, 'store']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
});
