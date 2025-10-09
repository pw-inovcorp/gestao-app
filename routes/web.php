<?php

use App\Http\Controllers\EntityController;
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
});
