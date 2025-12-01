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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;

Route::middleware(['auth'])->group(function () {

    // Password Change Routes
    Route::get('/change-password', [ChangePasswordController::class, 'edit'])->name('password.change');
    Route::put('/change-password', [ChangePasswordController::class, 'update'])->name('password.update');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Debug route (remove in production)
    Route::get('/notifications/debug', function () {
        return view('notifications.debug');
    })->name('notifications.debug');

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

    // Legacy Permits Module Routes
    require __DIR__ . '/legacy_permits.php';

});
