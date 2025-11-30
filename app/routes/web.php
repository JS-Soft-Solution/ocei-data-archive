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

Route::middleware('auth')->group(function () {
    // Ex-Electrician Routes
    Route::prefix('ex-electrician')->name('ex-electrician.')->group(function () {
        // Operator
        Route::middleware('role:data_entry_operator')->prefix('operator')->name('operator.')->group(function () {
            Route::get('search', [\App\Http\Controllers\ExElectrician\OperatorController::class, 'search'])->name('search');
            Route::post('{record}/claim', [\App\Http\Controllers\ExElectrician\OperatorController::class, 'claim'])->name('claim');
            Route::resource('applications', \App\Http\Controllers\ExElectrician\OperatorController::class)->only(['index', 'create', 'store', 'edit', 'update', 'show']);
            Route::post('applications/bulk-submit', [\App\Http\Controllers\ExElectrician\OperatorController::class, 'bulkSubmit'])->name('bulk-submit');
            // Tabbed form: POST /operator/applications/{id}/tab/{tab} for save/next
            Route::post('applications/{id}/tab/{tab}', [\App\Http\Controllers\ExElectrician\OperatorController::class, 'saveTab'])->name('save-tab');
            Route::post('applications/{id}/submit', [\App\Http\Controllers\ExElectrician\OperatorController::class, 'submit'])->name('submit');
        });

        // Office Assistant
        Route::middleware('role:office_assistant')->prefix('office-assistant')->name('office-assistant.')->group(function () {
            Route::get('pending', [\App\Http\Controllers\ExElectrician\OfficeAssistantController::class, 'pending'])->name('pending');
            Route::post('{record}/approve', [\App\Http\Controllers\ExElectrician\OfficeAssistantController::class, 'approve'])->name('approve');
            Route::post('{record}/reject', [\App\Http\Controllers\ExElectrician\OfficeAssistantController::class, 'reject'])->name('reject');
            Route::post('bulk-approve', [ElectricianOfficeAssistantController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('bulk-reject', [ElectricianOfficeAssistantController::class, 'bulkReject'])->name('bulk-reject');
        });

        // Secretary
        Route::middleware('role:secretary')->prefix('secretary')->name('secretary.')->group(function () {
            Route::get('pending', [ElectricianSecretaryController::class, 'pending'])->name('pending');
            Route::post('{record}/final-approve', [ElectricianSecretaryController::class, 'finalApprove'])->name('final-approve');
            Route::post('{record}/reject', [ElectricianSecretaryController::class, 'reject'])->name('reject');
            Route::post('bulk-final-approve', [ElectricianSecretaryController::class, 'bulkFinalApprove'])->name('bulk-final-approve');
            Route::post('bulk-reject', [ElectricianSecretaryController::class, 'bulkReject'])->name('bulk-reject');
        });

        // Chairman (read-only)
        Route::middleware('role:chairman')->prefix('chairman')->name('chairman.')->group(function () {
            Route::get('approved', [ElectricianChairmanController::class, 'approved'])->name('approved');
        });

        // Super Admin
        Route::middleware('role:super_admin')->prefix('admin')->name('admin.')->group(function () {
            Route::resource('applications', ElectricianAdminController::class);
            Route::post('{record}/override-status', [ElectricianAdminController::class, 'overrideStatus'])->name('override-status');
        });

        // Reports (shared, role-based access via policy)
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('export-pdf', [ElectricianReportController::class, 'exportPdf'])->name('export-pdf');
            Route::get('export-excel', [ElectricianReportController::class, 'exportExcel'])->name('export-excel');
            Route::get('{record}/history', [ElectricianReportController::class, 'history'])->name('history');
        });
    });

    // Repeat prefix/group for ex-supervisor and ex-contractor, swapping controllers (e.g., SupervisorOperatorController)
});
