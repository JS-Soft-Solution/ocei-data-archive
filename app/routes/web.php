<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\ForgotPasswordController;

Route::middleware('guest')->group(function () {
    // Step 1: Show form to enter User ID / Email / Phone
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');

    // Step 2: Handle form submission, find user, send OTP
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetOtp'])->name('password.email'); // Keeping name standard for compatibility

    // Step 3: Show OTP Verification Form
    Route::get('/forgot-password/verify/{userId}', [ForgotPasswordController::class, 'showOtpVerifyForm'])->name('password.otp.verify');

    // Step 4: Handle OTP Submission
    Route::post('/forgot-password/verify/{userId}', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.submit');

    // Step 5: Show Password Reset Form (only if OTP verified in session)
    Route::get('/reset-password/{userId}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');

    // Step 6: Handle Final Password Update
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

//Route::get('/', function () {
//    return view('dashboard');
//})->name('dashboard');
Route::middleware(['auth'])->group(function () {

    // Password Change Routes
    Route::get('/change-password', [ChangePasswordController::class, 'edit'])->name('password.change');
    Route::put('/change-password', [ChangePasswordController::class, 'update'])->name('password.update');

    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    // Example: Routes only for Super Admin
    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::put('/users/{user}/password-reset', [UserController::class, 'resetPassword'])
            ->name('users.password.reset');
    });

    // Example: Routes for Office Assistant and up
    Route::middleware(['role:super_admin,office_assistant'])->group(function () {
        Route::get('/files', function () {
            return "Manage Files";
        });
    });

});


// Helper function to generate routes for a permit type
function permitRoutes($prefix, $controller, $name) {
    Route::prefix($prefix)->name($name . '.')->group(function () use ($controller) {
        // Operator
        Route::middleware('role:data_entry_operator,super_admin')->group(function () use ($controller) {
            Route::get('/search', [$controller, 'search'])->name('search');
            Route::get('/drafts', [$controller, 'drafts'])->name('drafts');
            Route::get('/rejected', [$controller, 'rejected'])->name('rejected');
            Route::get('/submitted', [$controller, 'submitted'])->name('submitted');
            Route::get('/approved-list', [$controller, 'approvedListOperator'])->name('approved.operator');
            Route::get('/create', [$controller, 'create'])->name('create');
            Route::post('/store', [$controller, 'store'])->name('store');
            Route::get('/{id}/edit', [$controller, 'edit'])->name('edit');
            Route::put('/{id}/update', [$controller, 'update'])->name('update');
            Route::post('/{id}/submit', [$controller, 'submitToOffice'])->name('submit');
        });

        // Office Assistant
        Route::middleware('role:office_assistant,super_admin')->group(function () use ($controller) {
            Route::get('/pending', [$controller, 'pendingForAssistant'])->name('pending');
            Route::get('/rejected-by-me', [$controller, 'rejectedByAssistant'])->name('rejected.assistant');
            Route::get('/approved-by-me', [$controller, 'approvedByAssistant'])->name('approved.assistant');
            Route::post('/{id}/verify', [$controller, 'verify'])->name('verify');
            Route::post('/{id}/reject', [$controller, 'reject'])->name('reject');
        });

        // Secretary
        Route::middleware('role:secretary,super_admin')->group(function () use ($controller) {
            Route::get('/secretary/pending', [$controller, 'pendingForSecretary'])->name('secretary.pending');
            Route::get('/secretary/approved', [$controller, 'approvedBySecretary'])->name('secretary.approved');
            Route::post('/{id}/approve', [$controller, 'approve'])->name('approve');
            Route::post('/{id}/secretary-reject', [$controller, 'reject'])->name('secretary.reject');
        });

        // Shared / Reporting
        Route::middleware('role:chairman,super_admin,secretary')->group(function () use ($controller) {
            Route::get('/export/{type}', [$controller, 'export'])->name('export');
        });
    });
}

// Generate Routes
permitRoutes('permits/electrician', \App\Http\Controllers\Permit\ElectricianController::class, 'permits.electrician');
permitRoutes('permits/supervisor', \App\Http\Controllers\Permit\SupervisorController::class, 'permits.supervisor');
permitRoutes('permits/contractor', \App\Http\Controllers\Permit\ContractorController::class, 'permits.contractor');
