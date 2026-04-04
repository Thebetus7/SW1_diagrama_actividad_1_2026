<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PoliticaNegocioController;
use App\Http\Controllers\ColaboradorController;

Route::get('/', function () {
    return Inertia::render('MyWelcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('politica_negocio', PoliticaNegocioController::class);

    // Rutas anidadas para colaboradores
    Route::post('/politica_negocio/{politica_negocio}/colaboradores', [ColaboradorController::class, 'store'])
        ->name('colaborador.store');
    Route::delete('/politica_negocio/{politica_negocio}/colaboradores/{colaborador}', [ColaboradorController::class, 'destroy'])
        ->name('colaborador.destroy');
});

