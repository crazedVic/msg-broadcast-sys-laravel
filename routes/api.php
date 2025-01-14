<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserBroadcastController;

// Public routes
Route::get('/user/token', function() {
    $user = \App\Models\User::find(2);
    $token = $user->createToken('test-token')->plainTextToken;
    return response()->json([
        'token' => $token
    ]);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/token/verify', function (Request $request) {
        return response()->json(['message' => 'Token is valid'], 200);
    });
    
    // Broadcast routes
    Route::get('/broadcasts', [UserBroadcastController::class, 'apiIndex']);
    Route::put('/broadcasts/{broadcast}/state', [UserBroadcastController::class, 'apiUpdateState']);
});
