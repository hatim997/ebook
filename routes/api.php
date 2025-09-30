<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\API\Frontend\BookLawController;
use App\Http\Controllers\Api\Frontend\HomeController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [LogoutController::class, 'logout']);

    //Home apis
    Route::get('/user/home', [HomeController::class, 'home']);

    //Book Laws apis
    Route::get('/user/add-favourite/{id}', [BookLawController::class, 'addToFavourite']);
    Route::get('/user/mark-read/{id}', [BookLawController::class, 'markAsReadLaw']);
    Route::get('/user/laws', [BookLawController::class, 'getLaws']);

});


// Authentication Routes (Login and Register) for guests
Route::post('/login', [LoginController::class, 'login_attempt']);
Route::post('/register', [RegisterController::class, 'register_attempt']);
