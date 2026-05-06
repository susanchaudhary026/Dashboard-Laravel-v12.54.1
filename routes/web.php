<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileController; 
use App\Http\Controllers\UserController;

// --- Public Routes ---
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
Route::post('forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('reset-password/{token}', [AuthController::class, 'showReset'])->name('password.reset');
Route::post('reset-password', [AuthController::class, 'updatePassword'])->name('password.update');

// --- Authenticated Routes ---
Route::middleware(['auth'])->group(function () {
    
    // 1. Dashboard
    Route::get('/dashboard', [ArticleController::class, 'dashboard'])->name('dashboard');

    // 2. Articles (Note: Export MUST come before Resource)
    Route::get('/articles/export', [ArticleController::class, 'export'])->name('articles.export');
    Route::resource('articles', ArticleController::class);
    Route::patch('/articles/{id}/toggle-status', [ArticleController::class, 'toggleStatus'])->name('articles.toggleStatus');

    // 3. Categories
    Route::resource('categories', CategoryController::class);
    Route::patch('/categories/{id}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggleStatus');

    Route::get('/file-management', [FileController::class, 'index'])->name('files.index');
    Route::post('/file-management/upload', [FileController::class, 'upload'])->name('files.upload');
    Route::delete('/file-management/delete', [FileController::class, 'destroy'])->name('files.destroy');
    Route::post('/file-management/create-folder', [FileController::class, 'createFolder'])->name('files.createFolder');
    Route::post('/file-management/move', [FileController::class, 'moveFile'])->name('files.move');
    Route::post('/file-management/copy', [FileController::class, 'copyFile'])->name('files.copy');


    Route::get('/api/media', [App\Http\Controllers\FileController::class, 'getMediaJson'])->name('api.media');

    //user controller
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/role', [UserController::class, 'updateRole'])->name('users.updateRole');

});
