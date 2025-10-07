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
    Route::put('/utilizadores/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('utilizadores/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
