<?php

use App\Http\Controllers\EntregaController;
use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

// Área pública - Balcão (sem autenticação)
Route::get('/pedidos', function () {
    return view('pedidos.index');
})->name('pedidos.index');

Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');

// Área pública - Entrega (sem autenticação)
Route::get('/entregas/pendentes', [EntregaController::class, 'index'])->name('entregas.pendentes');
Route::put('/entregas/{pedido}', [EntregaController::class, 'update'])->name('entregas.update');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
