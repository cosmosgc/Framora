<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\GaleriaController;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\PerfilController;



// Home: banners + galerias em destaque
Route::get('/', [HomeController::class, 'index'])->name('home');

// Galerias: lista e escolhe uma
Route::get('/galerias', [GaleriaController::class, 'index'])->name('galerias.index');
Route::get('/galerias/{id}', [GaleriaController::class, 'show'])->name('galerias.show');

// Fotos: lista fotos dentro da galeria
Route::get('/galerias/{id}/fotos', [FotoController::class, 'index'])->name('fotos.index');
Route::get('/galerias/{id}/fotos/{foto}', [FotoController::class, 'show'])->name('fotos.show');

// Carrinho: visualizar e adicionar fotos
Route::get('/carrinho', [CarrinhoController::class, 'index'])->name('carrinho.index');
Route::post('/carrinho/adicionar', [CarrinhoController::class, 'store'])->name('carrinho.store');
Route::delete('/carrinho/remover/{id}', [CarrinhoController::class, 'destroy'])->name('carrinho.destroy');

// Inventário: acessar fotos compradas
Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
Route::get('/inventario/{id}', [InventarioController::class, 'show'])->name('inventario.show');

// Favoritos: salvar e listar favoritos
Route::get('/favoritos', [FavoritoController::class, 'index'])->name('favoritos.index');
Route::post('/favoritos/adicionar', [FavoritoController::class, 'store'])->name('favoritos.store');
Route::delete('/favoritos/remover/{id}', [FavoritoController::class, 'destroy'])->name('favoritos.destroy');

// Perfil: editar dados e ver pedidos
Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
