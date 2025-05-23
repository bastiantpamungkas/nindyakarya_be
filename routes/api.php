<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ProgresController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']); 

Route::middleware(['auth:api'])->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('change_password', [AuthController::class, 'change_password']);

    Route::prefix('dashboard')->group(function () {
        Route::get('counts', [DashboardController::class, 'counts']);
    });

    Route::prefix('roles')->group(function () {
        Route::get('list', [RolePermissionController::class, 'role']);
        Route::get('permissions', [RolePermissionController::class, 'permission']);
        Route::get('permissions_category', [RolePermissionController::class, 'permission_category']);
        Route::post('store', [RolePermissionController::class, 'store']);
        Route::get('edit-role/{id}', [RolePermissionController::class, 'editRole']);
        Route::put('update-role/{id}', [RolePermissionController::class, 'updateRole']);
        Route::delete('delete/{id}', [RolePermissionController::class, 'delete']);
    });

    Route::prefix('projects')->group(function () {
        Route::get('list', [ProjectController::class, 'list']);
        Route::get('detail/{id}', [ProjectController::class, 'detail']);
        Route::post('store', [ProjectController::class, 'store']);
        Route::put('update/{id}', [ProjectController::class, 'update']);
        Route::delete('delete/{id}', [ProjectController::class, 'delete']);
    });

    Route::prefix('progres')->group(function () {
        Route::get('list', [ProgresController::class, 'list']);
        Route::get('detail/{id}', [ProgresController::class, 'detail']);
        Route::post('store', [ProgresController::class, 'store']);
        Route::post('update/{id}', [ProgresController::class, 'update']);
        Route::put('update-status/{id}', [ProgresController::class, 'update_status']);
        Route::delete('delete/{id}', [ProgresController::class, 'delete']);
    });

    Route::prefix('users')->group(function () {
        Route::get('list', [UserController::class, 'list']);
        Route::get('detail/{id}', [UserController::class, 'detail']);
        Route::post('store', [UserController::class, 'store']);
        Route::put('update/{id}', [UserController::class, 'update']);
        Route::delete('delete/{id}', [UserController::class, 'delete']);
    });
    
    // Router lainnya
    
});

