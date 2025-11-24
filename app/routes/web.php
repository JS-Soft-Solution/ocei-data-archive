<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//Route::get('/', function () {
//    return view('dashboard');
//})->name('dashboard');
Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    // Example: Routes only for Super Admin
    Route::middleware(['role:super_admin'])->group(function() {
        Route::resource('users', UserController::class);
    });

    // Example: Routes for Office Assistant and up
    Route::middleware(['role:super_admin,office_assistant'])->group(function() {
        Route::get('/files', function() { return "Manage Files"; });
    });

});
