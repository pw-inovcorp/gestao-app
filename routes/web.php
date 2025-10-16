<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\SupplierOrderController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('home')
        : redirect()->route('login');
});


Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return Inertia::render('Auth/Login');
    })->name('login');

    Route::get('/register', function () {
        return Inertia::render('Auth/Register');
    })->name('register');

    Route::get('/forgot-password', function () {
        return Inertia::render('Auth/ForgotPassword');
    })->name('password.request');
});


Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return Inertia::render('Home');
    })->name('home');

    Route::get('/entidades',[EntityController::class,'index'])->name('entities.index');

    Route::get('/contactos',[ContactController::class,'index'])->name('contacts.index');

    Route::get('/artigos',[ArticleController::class,'index'])->name('articles.index');

});

Route::middleware('role:admin')->group(function () {
    Route::get('/utilizadores', [UserController::class, 'index'])->name('users.index');
    Route::get('/utilizadores/criar', [UserController::class, 'create'])->name('users.create');
    Route::post('/utilizadores', [UserController::class, 'store'])->name('users.store');
    Route::get('/utilizadores/{user}/editar', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/utilizadores/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('utilizadores/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/entidades/criar', [EntityController::class, 'create'])->name('entities.create');
    Route::post('/entidades', [EntityController::class, 'store'])->name('entities.store');
    Route::get('/entidades/{entity}', [EntityController::class, 'show'])->name('entities.show');
    Route::get('/entidades/{entity}/editar', [EntityController::class, 'edit'])->name('entities.edit');
    Route::patch('/entidades/{entity}', [EntityController::class, 'update'])->name('entities.update');
    Route::delete('/entidades/{entity}', [EntityController::class, 'destroy'])->name('entities.destroy');

    Route::post('/entidades/check-vies', [EntityController::class, 'checkVies'])->name('entities.checkVies');

    Route::get('/contactos/criar', [ContactController::class, 'create'])->name('contacts.create');
    Route::post('/contactos', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/contactos/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::get('/contactos/{contact}/editar', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::patch('/contactos/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contactos/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

    Route::get('/artigos/criar', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/artigos', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/artigos/{article}', [ArticleController::class, 'show'])->name('article.show');
    Route::get('/artigos/{article}/editar', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::patch('/artigos/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/artigos/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');

    Route::get('/propostas', [ProposalController::class, 'index'])->name('proposals.index');
    Route::get('propostas/criar', [ProposalController::class, 'create'])->name('proposals.create');
    Route::get('propostas/{proposal}', [ProposalController::class, 'show'])->name('proposals.show');
    Route::post('propostas', [ProposalController::class, 'store'])->name('proposals.store');
    Route::get('propostas/{proposal}/editar', [ProposalController::class, 'edit'])->name('proposals.edit');
    Route::patch('propostas/{proposal}', [ProposalController::class, 'update'])->name('proposals.update');
    Route::delete('propostas/{proposal}', [ProposalController::class, 'destroy'])->name('proposals.destroy');

    Route::get('/propostas/{id}/pdf', [ProposalController::class, 'downloadPdf'])->name('proposal.pdf');
    Route::post('/propostas/{id}/converter-encomenda', [ProposalController::class, 'convertToOrder'])->name('proposals.convertToOrder');

    Route::get('/encomendas', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/encomendas/criar', [OrderController::class, 'create'])->name('orders.create');
    Route::get('/encomendas/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/encomendas', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/encomendas/{order}/editar', [OrderController::class, 'edit'])->name('orders.edit');
    Route::patch('/encomendas/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::patch('/encomendas/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::delete('/encomendas/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    Route::get('/encomendas/{order}/pdf', [OrderController::class, 'downloadPdf'])->name('orders.pdf');
    Route::post('/encomendas/{order}/converter-fornecedor', [OrderController::class, 'convertToSupplierOrders'])->name('orders.convertToSupplierOrders');

    Route::get('/encomendas-fornecedor', [SupplierOrderController::class, 'index'])->name('supplier-orders.index');
    Route::get('/encomendas-fornecedor/{supplierOrder}', [SupplierOrderController::class, 'show'])->name('supplier-orders.show');
    Route::patch('/encomendas-fornecedor/{supplierOrder}/status', [SupplierOrderController::class, 'updateStatus'])->name('supplier-orders.updateStatus');
    Route::delete('/encomendas-fornecedor/{supplierOrder}', [SupplierOrderController::class, 'destroy'])->name('supplier-orders.destroy');
    Route::get('/encomendas-fornecedor/{supplierOrder}/pdf', [SupplierOrderController::class, 'downloadPdf'])->name('supplier-orders.pdf');
});
