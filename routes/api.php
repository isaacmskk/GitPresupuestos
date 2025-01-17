<?php

use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PostControllerAdvance;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Api\PresupuestoController;
use App\Http\Controllers\Api\TransaccionController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\BankAccountController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('forget-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('forget.password.post');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.reset');

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::apiResource('users', UserController::class);

    Route::post('users/updateimg', [UserController::class, 'updateimg']); //Listar

    Route::apiResource('posts', PostControllerAdvance::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('roles', RoleController::class);

    Route::get('role-list', [RoleController::class, 'getList']);
    Route::get('role-permissions/{id}', [PermissionController::class, 'getRolePermissions']);
    Route::put('/role-permissions', [PermissionController::class, 'updateRolePermissions']);
    Route::apiResource('permissions', PermissionController::class);

    Route::get('category-list', [CategoryController::class, 'getList']);
    Route::get('/user', [ProfileController::class, 'user']);
    Route::put('/user', [ProfileController::class, 'update']);

    Route::get('abilities', function (Request $request) {
        return $request->user()->roles()->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values()
            ->toArray();
    });
    //categorias
    Route::get('/categorias', action: [CategoriaController::class, 'index']);
    Route::post('/categorias', [CategoriaController::class, 'store']);

    //presupuestos
    Route::apiResource('presupuestos', PresupuestoController::class);
    Route::get('presupuestos', [PresupuestoController::class, 'index']);
    Route::post('presupuestos', [PresupuestoController::class, 'store']);
    Route::put('presupuestos/{id}', [PresupuestoController::class, 'update']);
    Route::delete('presupuestos/{id}', [PresupuestoController::class, 'destroy']);

    //transacciones
    Route::apiResource('transacciones', TransaccionController::class);
    Route::get('transacciones', [TransaccionController::class, 'index']);
    Route::post('transacciones', [TransaccionController::class, 'store']);
    Route::put('transacciones/{id}', [TransaccionController::class, 'update']);
    Route::delete('transacciones/{id}', [TransaccionController::class, 'destroy']);

    //banco
    Route::get('/api/bank-accounts', [BankAccountController::class, 'getBankAccounts']);
    Route::get('/api/bank-accounts/{accountId}/transactions', [BankAccountController::class, 'getTransactions']);
});

Route::get('category-list', [CategoryController::class, 'getList']);

Route::get('get-posts', [PostControllerAdvance::class, 'getPosts']);
Route::get('get-category-posts/{id}', [PostControllerAdvance::class, 'getCategoryByPosts']);
Route::get('get-post/{id}', [PostControllerAdvance::class, 'getPost']);
