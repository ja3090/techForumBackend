<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}/{page}', [CategoryController::class, 'show']);
Route::get('/threads/{id}/{page}', [ThreadController::class, 'show']);
Route::get('/threads', [ThreadController::class, 'index']);
Route::get('/posts', [PostController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post('/threads', [ThreadController::class, 'store']);
    Route::delete('/threads/{id}', [ThreadController::class, 'destroy']);
    Route::put('/threads/{id}', [ThreadController::class, 'update']);
    Route::post('/posts/{threadId}', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    Route::post('/users/logout', [UserController::class, 'logout']);
    Route::post('/users/make-admin', [UserController::class, 'makeAdmin']);
});

Route::post('/users/login', [UserController::class, 'store']);
Route::post('/users/register', [UserController::class, 'create']);
