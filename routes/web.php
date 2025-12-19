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
use App\Http\Controllers\AtualizacoesController;

use App\Http\Controllers\WebViewsController;


use App\Http\Controllers\StripeController;



// Home: banners + galerias em destaque
Route::get('/', [HomeController::class, 'index'])->name('home');

// Galerias
Route::get('/galerias', [WebViewsController::class, 'GaleriaIndex'])->name('galerias.web.index');
Route::get('/galerias/{id}', [WebViewsController::class, 'GaleriaShow'])->name('galerias.web.show');
Route::get('/galerias/{id}/edit', [WebViewsController::class, 'GaleriaEdit'])
    ->middleware('auth')
    ->name('galerias.web.edit');
Route::get('/CriarGaleria', [WebViewsController::class, 'GaleriaCreate'])->name('galerias.web.create');

// Fotos dentro da galeria
Route::get('/galerias/{id}/fotos', [WebViewsController::class, 'FotosIndex'])->name('fotos.web.index');
Route::get('/galerias/{id}/fotos/{foto}', [WebViewsController::class, 'FotosShow'])->name('fotos.web.show');

// Carrinho: visualizar e adicionar fotos
Route::get('/carrinho', [CarrinhoController::class, 'index'])->name('carrinho.index');
Route::post('/carrinho/adicionar', [CarrinhoController::class, 'store'])->name('carrinho.store');
Route::delete('/carrinho/remover/{id}', [CarrinhoController::class, 'destroy'])->name('carrinho.destroy');

// Route::post('/carrinho/checkout', [CarrinhoController::class, 'checkout'])->name('carrinho.checkout')->middleware('auth');


Route::get('/pedidos', function () { return view('pedidos.index'); })->name('pedidos.web.index');
Route::get('/pedidos/criar', function () { return view('pedidos.create'); })->name('pedidos.web.create');

Route::get('/inventario', [WebViewsController::class, 'inventarioIndex'])
    ->middleware('auth')
    ->name('inventario.web.index');
Route::get('/inventario/{id}', function ($id) { return view('inventario.show', ['id' => $id]); })
    ->middleware('auth')
    ->name('inventario.web.show');

// Inventário: acessar fotos compradas
// Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
// Route::get('/inventario/{id}', [InventarioController::class, 'show'])->name('inventario.show');

// Favoritos: salvar e listar favoritos
Route::get('/favoritos', [FavoritoController::class, 'index'])->name('favoritos.index');
Route::post('/favoritos/adicionar', [FavoritoController::class, 'store'])->name('favoritos.store');
Route::delete('/favoritos/remover/{id}', [FavoritoController::class, 'destroy'])->name('favoritos.destroy');

// Perfil: editar dados e ver pedidos
Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

Route::get('/categorias', [WebViewsController::class, 'CategoriaIndex'])->name('categorias.web.index');
Route::get('/categoria/{id}', [WebViewsController::class, 'CategoriaShow'])->name('categoria.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/updates', action: [AtualizacoesController::class, 'index'])->name('updates.index');
Route::get('/updates/update', [AtualizacoesController::class, 'update'])->name('updates.update');

Route::middleware(['auth'])->group(function () {
    // rota que o form vai submeter (POST)
    Route::post('/stripe/checkout/{id}', [StripeController::class, 'createCheckoutSession'])
         ->name('stripe.checkout');

    Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');
    Route::get('/stripe/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');
});
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');

Route::get('/watermark-test/{foto}/{tipo?}', [FotoController::class, 'testWatermark']);

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminCategoriaController;
use App\Http\Controllers\Admin\AdminImageSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminGaleriaController;
use App\Http\Controllers\Admin\AdminFotoController;
use App\Http\Controllers\Admin\AdminPedidoController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin.access']) // later you can add ->middleware(['auth','admin'])
    ->group(function () {

        // Dashboard
        Route::get('/', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // Categorias
        Route::get('/categorias', [AdminCategoriaController::class, 'index'])
            ->name('categorias.index');
        Route::get('/categorias/create', [AdminCategoriaController::class, 'create'])
            ->name('categorias.create');
        Route::post('/categorias', [AdminCategoriaController::class, 'store'])
            ->name('categorias.store');
        Route::get('/categorias/{id}/edit', [AdminCategoriaController::class, 'edit'])
            ->name('categorias.edit');
        Route::put('/categorias/{id}', [AdminCategoriaController::class, 'update'])
            ->name('categorias.update');
        Route::delete('/categorias/{id}', [AdminCategoriaController::class, 'destroy'])
            ->name('categorias.destroy');

        // Image Settings (global configs, watermark, quality, etc)
        Route::get('/image-settings', [AdminImageSettingController::class, 'index'])
            ->name('image-settings.index');
        Route::post('/image-settings', [AdminImageSettingController::class, 'update'])
            ->name('image-settings.update');
        Route::resource('/users', AdminUserController::class);


        Route::resource('/roles', AdminRoleController::class)
            ->except(['show']);

            
        Route::get('/galerias', [AdminGaleriaController::class, 'index'])
            ->name('galerias.index');

        Route::get('/galerias/{galeria}', [AdminGaleriaController::class, 'show'])
            ->name('galerias.show');
        
        Route::delete('/galerias/{galeria}', [AdminGaleriaController::class, 'destroy'])
            ->name('galerias.destroy');

        Route::delete('/fotos/{foto}', [AdminFotoController::class, 'destroy'])
            ->name('fotos.destroy');  
        
        Route::get('/pedidos', [AdminPedidoController::class, 'index'])
            ->name('pedidos.index');

        Route::get('/pedidos/{pedido}', [AdminPedidoController::class, 'show'])
            ->name('pedidos.show');


        // Future examples
        // Route::resource('/banners', AdminBannerController::class);
    });


require __DIR__.'/auth.php';
