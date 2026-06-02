<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\EntrepriseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\KitController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReportPreviewController;
use App\Http\Controllers\TropheeController;
use Illuminate\Support\Facades\Route;

Route::get('/',          [HomeController::class, 'index'])->name('home');
Route::get('/trophee',   [TropheeController::class, 'index'])->name('trophee');
Route::get('/label',     [LabelController::class, 'index'])->name('label');
Route::get('/kit-promo', [KitController::class, 'index'])->name('kit-promo');
Route::get('/contact',     [ContactController::class, 'index'])->name('contact');
Route::post('/contact',    [ContactController::class, 'store'])->name('contact.store');

Route::get('/inscription', [InscriptionController::class, 'index'])->name('inscription');
Route::post('/inscription',[InscriptionController::class, 'store'])->name('inscription.store');

Route::get('/c/{entreprise}',             [EntrepriseController::class, 'show'])->name('entreprise.show');
Route::get('/c/{entreprise}/quiz',        [QuizController::class, 'show'])->name('quiz.show');
Route::post('/c/{entreprise}/quiz',       [QuizController::class, 'store'])->name('quiz.store');
Route::get('/c/{entreprise}/quiz/result', [QuizController::class, 'result'])->name('quiz.result');

Route::get('/report-preview/{token}', [ReportPreviewController::class, 'show'])->name('report.preview');

Route::get('/{any}', fn() => view('app'))->where('any', '.*');
