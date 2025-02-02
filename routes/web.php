<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserBroadcastController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/user/broadcasts', [UserBroadcastController::class, 'index'])->name('user.broadcasts.index');
    Route::get('/user/broadcasts/{id}', [UserBroadcastController::class, 'show'])->name('user.broadcasts.show');
    Route::delete('/user/broadcasts/{id}', [UserBroadcastController::class, 'softDelete'])->name('user.broadcasts.soft-delete');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    \App\Http\Middleware\EnsureUserIsAdmin::class,
])->prefix('admin')->as('admin.')->group(function () {
    Route::resource('broadcasts', \App\Http\Controllers\Admin\AdminBroadcastController::class);
});

