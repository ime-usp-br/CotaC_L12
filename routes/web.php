<?php

use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Área pública - Balcão (sem autenticação)
Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
