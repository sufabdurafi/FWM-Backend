<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Public route
Route::get('/', function () {
    return view('welcome');
});

// Protected admin routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index']);
});