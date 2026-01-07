<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\GaleriaController;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\FotoDestacadaController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\ConfiguracaoController;
use App\Http\Controllers\StripeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ----------------- AUTENTICAÇÃO -----------------
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// ----------------- USERS -----------------
Route::apiResource('users', UserController::class);
Route::post('/users/{id}/roles/{role_id}', [UserController::class, 'assignRole']);
Route::delete('/users/{id}/roles/{role_id}', [UserController::class, 'removeRole']);

// ----------------- ROLES -----------------
Route::apiResource('roles', RoleController::class);

// ----------------- BANNERS -----------------
Route::apiResource('banners', BannerController::class);

// ----------------- CATEGORIAS -----------------
Route::apiResource('categorias', CategoriaController::class);

// ----------------- GALERIAS -----------------
Route::apiResource('galerias', GaleriaController::class);

// ----------------- FOTOS -----------------
Route::apiResource('fotos', FotoController::class);
Route::post('/galerias/{galeria}/fotos/reorder', [GaleriaController::class, 'reorderFotos'])->name('galerias.fotos.reorder');

// ----------------- FOTOS DESTACADAS -----------------
Route::apiResource('fotos-destacadas', FotoDestacadaController::class);

// ----------------- FAVORITOS -----------------
Route::get('/favoritos', [FavoritoController::class, 'index']);
Route::post('/favoritos', [FavoritoController::class, 'store']);
Route::delete('/favoritos/{id}', [FavoritoController::class, 'destroy']);

// ----------------- CARRINHOS -----------------
Route::apiResource('carrinhos', CarrinhoController::class)->except(['update']);

Route::post('/carrinhos/{id}/fotos', [CarrinhoController::class, 'addFoto']);
Route::put('/carrinhos/{id}/fotos/{fid}', [CarrinhoController::class, 'updateFoto']);
Route::delete('/carrinhos/{id}/fotos/{fid}', [CarrinhoController::class, 'removeFoto']);

Route::post('/carrinhos/{id}/checkout', [StripeController::class, 'createCheckoutSession'])->middleware('auth:sanctum');

// ----------------- PEDIDOS -----------------
Route::apiResource('pedidos', PedidoController::class);

// ----------------- INVENTÁRIO -----------------
Route::get('/inventario', [InventarioController::class, 'index']);
Route::get('/inventario/{id}', [InventarioController::class, 'show']);

// ----------------- CONFIGURAÇÕES -----------------
Route::apiResource('configuracoes', ConfiguracaoController::class);


Route::post('/stripe/checkout/{id}', [StripeController::class, 'createCheckoutSession'])->name("stripe.api.checkout"); // apiCheckout substituto
