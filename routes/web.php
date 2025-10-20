<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\SupplierInvoiceController;
use App\Http\Controllers\SupplierOrderController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('home')
        : redirect()->route('login');
});


// Rotas de Guest
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

// Rotas autenticadas
Route::middleware('auth')->group(function () {


    Route::get('/home', function () {
        return Inertia::render('Home');
    })->name('home');


    Route::prefix('utilizadores')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:users.view')->name('index');
        Route::get('/criar', [UserController::class, 'create'])->middleware('permission:users.create')->name('create');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:users.create')->name('store');
        Route::get('/{user}/editar', [UserController::class, 'edit'])->name('edit'); // Permissão verificada no controller
        Route::patch('/{user}', [UserController::class, 'update'])->name('update'); // Permissão verificada no controller
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('destroy');
    });


    Route::prefix('entidades')->name('entities.')->group(function () {
        Route::post('/check-vies', [EntityController::class, 'checkVies'])->middleware('permission:entities.vies')->name('checkVies');
        Route::get('/', [EntityController::class, 'index'])->middleware('permission:entities.view')->name('index');
        Route::get('/criar', [EntityController::class, 'create'])->middleware('permission:entities.create')->name('create');
        Route::post('/', [EntityController::class, 'store'])->middleware('permission:entities.create')->name('store');
        Route::get('/{entity}', [EntityController::class, 'show'])->middleware('permission:entities.view')->name('show');
        Route::get('/{entity}/editar', [EntityController::class, 'edit'])->middleware('permission:entities.edit')->name('edit');
        Route::patch('/{entity}', [EntityController::class, 'update'])->middleware('permission:entities.edit')->name('update');
        Route::delete('/{entity}', [EntityController::class, 'destroy'])->middleware('permission:entities.delete')->name('destroy');
    });


    Route::prefix('contactos')->name('contacts.')->group(function () {
        Route::get('/', [ContactController::class, 'index'])->middleware('permission:contacts.view')->name('index');
        Route::get('/criar', [ContactController::class, 'create'])->middleware('permission:contacts.create')->name('create');
        Route::post('/', [ContactController::class, 'store'])->middleware('permission:contacts.create')->name('store');
        Route::get('/{contact}', [ContactController::class, 'show'])->middleware('permission:contacts.view')->name('show');
        Route::get('/{contact}/editar', [ContactController::class, 'edit'])->middleware('permission:contacts.edit')->name('edit');
        Route::patch('/{contact}', [ContactController::class, 'update'])->middleware('permission:contacts.edit')->name('update');
        Route::delete('/{contact}', [ContactController::class, 'destroy'])->middleware('permission:contacts.delete')->name('destroy');
    });


    Route::prefix('artigos')->name('articles.')->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->middleware('permission:articles.view')->name('index');
        Route::get('/criar', [ArticleController::class, 'create'])->middleware('permission:articles.create')->name('create');
        Route::post('/', [ArticleController::class, 'store'])->middleware('permission:articles.create')->name('store');
        Route::get('/{article}', [ArticleController::class, 'show'])->middleware('permission:articles.view')->name('show');
        Route::get('/{article}/editar', [ArticleController::class, 'edit'])->middleware('permission:articles.edit')->name('edit');
        Route::patch('/{article}', [ArticleController::class, 'update'])->middleware('permission:articles.edit')->name('update');
        Route::delete('/{article}', [ArticleController::class, 'destroy'])->middleware('permission:articles.delete')->name('destroy');
    });


    Route::prefix('propostas')->name('proposals.')->group(function () {
        Route::get('/', [ProposalController::class, 'index'])->middleware('permission:proposals.view')->name('index');
        Route::get('/criar', [ProposalController::class, 'create'])->middleware('permission:proposals.create')->name('create');
        Route::post('/', [ProposalController::class, 'store'])->middleware('permission:proposals.create')->name('store');
        Route::get('/{proposal}', [ProposalController::class, 'show'])->middleware('permission:proposals.view')->name('show');
        Route::get('/{proposal}/editar', [ProposalController::class, 'edit'])->middleware('permission:proposals.edit')->name('edit');
        Route::patch('/{proposal}', [ProposalController::class, 'update'])->middleware('permission:proposals.edit')->name('update');
        Route::patch('/{proposal}/status', [ProposalController::class, 'updateStatus'])->middleware('permission:proposals.edit')->name('updateStatus');
        Route::get('/{id}/pdf', [ProposalController::class, 'downloadPdf'])->middleware('permission:proposals.pdf')->name('pdf');
        Route::post('/{id}/converter-encomenda', [ProposalController::class, 'convertToOrder'])->middleware('permission:proposals.convert')->name('convertToOrder');
        Route::delete('/{proposal}', [ProposalController::class, 'destroy'])->middleware('permission:proposals.delete')->name('destroy');
    });


    Route::prefix('encomendas')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->middleware('permission:orders.view')->name('index');
        Route::get('/criar', [OrderController::class, 'create'])->middleware('permission:orders.create')->name('create');
        Route::post('/', [OrderController::class, 'store'])->middleware('permission:orders.create')->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->middleware('permission:orders.view')->name('show');
        Route::get('/{order}/editar', [OrderController::class, 'edit'])->middleware('permission:orders.edit')->name('edit');
        Route::patch('/{order}', [OrderController::class, 'update'])->middleware('permission:orders.edit')->name('update');
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->middleware('permission:orders.edit')->name('updateStatus');
        Route::get('/{order}/pdf', [OrderController::class, 'downloadPdf'])->middleware('permission:orders.pdf')->name('pdf');
        Route::post('/{order}/converter-fornecedor', [OrderController::class, 'convertToSupplierOrders'])->middleware('permission:orders.convert')->name('convertToSupplierOrders');
        Route::delete('/{order}', [OrderController::class, 'destroy'])->middleware('permission:orders.delete')->name('destroy');
    });


    Route::prefix('encomendas-fornecedor')->name('supplier-orders.')->group(function () {
        Route::get('/', [SupplierOrderController::class, 'index'])->middleware('permission:supplier-orders.view')->name('index');
        Route::get('/{supplierOrder}', [SupplierOrderController::class, 'show'])->middleware('permission:supplier-orders.view')->name('show');
        Route::patch('/{supplierOrder}/status', [SupplierOrderController::class, 'updateStatus'])->middleware('permission:supplier-orders.edit')->name('updateStatus');
        Route::get('/{supplierOrder}/pdf', [SupplierOrderController::class, 'downloadPdf'])->middleware('permission:supplier-orders.pdf')->name('pdf');
        Route::delete('/{supplierOrder}', [SupplierOrderController::class, 'destroy'])->middleware('permission:supplier-orders.delete')->name('destroy');
    });


    Route::prefix('faturas-fornecedor')->name('supplier-invoices.')->group(function () {
        Route::get('/', [SupplierInvoiceController::class, 'index'])->middleware('permission:supplier-invoices.view')->name('index');
        Route::get('/criar', [SupplierInvoiceController::class, 'create'])->middleware('permission:supplier-invoices.create')->name('create');
        Route::post('/', [SupplierInvoiceController::class, 'store'])->middleware('permission:supplier-invoices.create')->name('store');
        Route::get('/{supplierInvoice}', [SupplierInvoiceController::class, 'show'])->middleware('permission:supplier-invoices.view')->name('show');
        Route::patch('/{supplierInvoice}/status', [SupplierInvoiceController::class, 'updateStatus'])->middleware('permission:supplier-invoices.edit')->name('updateStatus');
        Route::get('/{supplierInvoice}/download/{type}', [SupplierInvoiceController::class, 'downloadFile'])->middleware('permission:supplier-invoices.payment')->name('download');
        Route::post('/{supplierInvoice}/comprovativo', [SupplierInvoiceController::class, 'uploadPaymentProof'])->middleware('permission:supplier-invoices.payment')->name('uploadProof');
        Route::delete('/{supplierInvoice}', [SupplierInvoiceController::class, 'destroy'])->middleware('permission:supplier-invoices.delete')->name('destroy');
    });
});
