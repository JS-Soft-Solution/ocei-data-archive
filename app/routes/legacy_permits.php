<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Permits\Electrician\ElectricianOperatorController;
use App\Http\Controllers\Permits\Electrician\ElectricianOfficeAssistantController;
use App\Http\Controllers\Permits\Electrician\ElectricianSecretaryController;
use App\Http\Controllers\Permits\Electrician\ElectricianChairmanController;
use App\Http\Controllers\Permits\Electrician\ElectricianAdminController;
use App\Http\Controllers\Permits\Electrician\ElectricianReportController;
use App\Http\Controllers\Permits\Supervisor\SupervisorOperatorController;
use App\Http\Controllers\Permits\Supervisor\SupervisorOfficeAssistantController;
use App\Http\Controllers\Permits\Supervisor\SupervisorSecretaryController;
use App\Http\Controllers\Permits\Supervisor\SupervisorChairmanController;
use App\Http\Controllers\Permits\Supervisor\SupervisorAdminController;
use App\Http\Controllers\Permits\Supervisor\SupervisorReportController;
use App\Http\Controllers\Permits\Contractor\ContractorOperatorController;
use App\Http\Controllers\Permits\Contractor\ContractorOfficeAssistantController;
use App\Http\Controllers\Permits\Contractor\ContractorSecretaryController;
use App\Http\Controllers\Permits\Contractor\ContractorChairmanController;
use App\Http\Controllers\Permits\Contractor\ContractorAdminController;
use App\Http\Controllers\Permits\Contractor\ContractorReportController;

/*
|--------------------------------------------------------------------------
| Legacy Permit Management Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // ========== ATTACHMENT ROUTES (Shared) ==========
    Route::post('/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
    Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::get('/attachments/{attachment}/preview', [AttachmentController::class, 'preview'])->name('attachments.preview');
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // ========== ELECTRICIAN PERMIT ROUTES ==========

    // Data Entry Operator Routes
    Route::prefix('ex-electrician/operator')->name('ex-electrician.operator.')->group(function () {
        Route::get('/', [ElectricianOperatorController::class, 'index'])->name('index');
        Route::get('/pending', [ElectricianOperatorController::class, 'pending'])->name('pending');
        Route::get('/rejected', [ElectricianOperatorController::class, 'rejected'])->name('rejected');
        Route::get('/approved', [ElectricianOperatorController::class, 'approved'])->name('approved');
        Route::get('/create', [ElectricianOperatorController::class, 'create'])->name('create');
        Route::post('/', [ElectricianOperatorController::class, 'store'])->name('store');
        Route::get('/{application}/edit', [ElectricianOperatorController::class, 'edit'])->name('edit');
        Route::get('/{application}', [ElectricianOperatorController::class, 'show'])->name('show');
        Route::put('/{application}', [ElectricianOperatorController::class, 'update'])->name('update');
        Route::post('/{application}/save-tab', [ElectricianOperatorController::class, 'saveTab'])->name('save-tab');
        Route::post('/{application}/submit', [ElectricianOperatorController::class, 'submit'])->name('submit');
        Route::post('/bulk-submit', [ElectricianOperatorController::class, 'bulkSubmit'])->name('bulk-submit');
        Route::post('/claim', [ElectricianOperatorController::class, 'claim'])->name('claim');
        Route::delete('/{application}', [ElectricianOperatorController::class, 'destroy'])->name('destroy');
    });

    // Office Assistant Routes
    Route::prefix('ex-electrician/office-assistant')->name('ex-electrician.office-assistant.')->group(function () {
        Route::get('/pending', [ElectricianOfficeAssistantController::class, 'pending'])->name('pending');
        Route::get('/rejected', [ElectricianOfficeAssistantController::class, 'rejected'])->name('rejected');
        Route::get('/approved', [ElectricianOfficeAssistantController::class, 'approved'])->name('approved');
        Route::get('/{application}', [ElectricianOfficeAssistantController::class, 'show'])->name('show');
        Route::post('/{application}/approve', [ElectricianOfficeAssistantController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [ElectricianOfficeAssistantController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [ElectricianOfficeAssistantController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [ElectricianOfficeAssistantController::class, 'bulkReject'])->name('bulk-reject');
    });

    // Secretary Routes
    Route::prefix('ex-electrician/secretary')->name('ex-electrician.secretary.')->group(function () {
        Route::get('/pending', [ElectricianSecretaryController::class, 'pending'])->name('pending');
        Route::get('/rejected', [ElectricianSecretaryController::class, 'rejected'])->name('rejected');
        Route::get('/approved', [ElectricianSecretaryController::class, 'approved'])->name('approved');
        Route::get('/{application}', [ElectricianSecretaryController::class, 'show'])->name('show');
        Route::post('/{application}/approve', [ElectricianSecretaryController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [ElectricianSecretaryController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [ElectricianSecretaryController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [ElectricianSecretaryController::class, 'bulkReject'])->name('bulk-reject');
    });

    // Chairman Routes
    Route::prefix('ex-electrician/chairman')->name('ex-electrician.chairman.')->group(function () {
        Route::get('/', [ElectricianChairmanController::class, 'index'])->name('index');
        Route::get('/{application}', [ElectricianChairmanController::class, 'show'])->name('show');
    });

    // Super Admin Routes
    Route::prefix('ex-electrician/admin')->name('ex-electrician.admin.')->group(function () {
        Route::get('/', [ElectricianAdminController::class, 'index'])->name('index');
        Route::get('/{application}/edit', [ElectricianAdminController::class, 'edit'])->name('edit');
        Route::put('/{application}', [ElectricianAdminController::class, 'update'])->name('update');
        Route::delete('/{application}', [ElectricianAdminController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [ElectricianAdminController::class, 'restore'])->name('restore');
        Route::post('/{application}/change-status', [ElectricianAdminController::class, 'changeStatus'])->name('change-status');
    });

    // Report Routes
    Route::prefix('ex-electrician/reports')->name('ex-electrician.reports.')->group(function () {
        Route::get('/', [ElectricianReportController::class, 'index'])->name('index');
        Route::get('/preview', [ElectricianReportController::class, 'preview'])->name('preview');
        Route::get('/export-excel', [ElectricianReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [ElectricianReportController::class, 'exportPdf'])->name('export-pdf');
    });

    // ========== SUPERVISOR PERMIT ROUTES ==========
    // (Following same pattern as Electrician - 45+ routes)

    // Data Entry Operator Routes
    Route::prefix('ex-supervisor/operator')->name('ex-supervisor.operator.')->group(function () {
        Route::get('/', [SupervisorOperatorController::class, 'index'])->name('index');
        Route::get('/pending', [SupervisorOperatorController::class, 'pending'])->name('pending');
        Route::get('/rejected', [SupervisorOperatorController::class, 'rejected'])->name('rejected');
        Route::get('/approved', [SupervisorOperatorController::class, 'approved'])->name('approved');
        Route::get('/create', [SupervisorOperatorController::class, 'create'])->name('create');
        Route::post('/', [SupervisorOperatorController::class, 'store'])->name('store');
        Route::get('/{application}/edit', [SupervisorOperatorController::class, 'edit'])->name('edit');
        Route::get('/{application}', [SupervisorOperatorController::class, 'show'])->name('show');
        Route::put('/{application}', [SupervisorOperatorController::class, 'update'])->name('update');
        Route::post('/{application}/save-tab', [SupervisorOperatorController::class, 'saveTab'])->name('save-tab');
        Route::post('/{application}/submit', [SupervisorOperatorController::class, 'submit'])->name('submit');
        Route::post('/bulk-submit', [SupervisorOperatorController::class, 'bulkSubmit'])->name('bulk-submit');
        Route::post('/claim', [SupervisorOperatorController::class, 'claim'])->name('claim');
        Route::delete('/{application}', [SupervisorOperatorController::class, 'destroy'])->name('destroy');
    });

    // Office Assistant Routes
    Route::prefix('ex-supervisor/office-assistant')->name('ex-supervisor.office-assistant.')->group(function () {
        Route::get('/pending', [SupervisorOfficeAssistantController::class, 'pending'])->name('pending');
        Route::get('/rejected', [SupervisorOfficeAssistantController::class, 'rejected'])->name('rejected');
        Route::get('/approved', [SupervisorOfficeAssistantController::class, 'approved'])->name('approved');
        Route::get('/{application}', [SupervisorOfficeAssistantController::class, 'show'])->name('show');
        Route::post('/{application}/approve', [SupervisorOfficeAssistantController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [SupervisorOfficeAssistantController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [SupervisorOfficeAssistantController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [SupervisorOfficeAssistantController::class, 'bulkReject'])->name('bulk-reject');
    });

    // Secretary Routes
    Route::prefix('ex-supervisor/secretary')->name('ex-supervisor.secretary.')->group(function () {
        Route::get('/pending', [SupervisorSecretaryController::class, 'pending'])->name('pending');
        Route::get('/rejected', [SupervisorSecretaryController::class, 'rejected'])->name('rejected');
        Route::get('/approved', [SupervisorSecretaryController::class, 'approved'])->name('approved');
        Route::get('/{application}', [SupervisorSecretaryController::class, 'show'])->name('show');
        Route::post('/{application}/approve', [SupervisorSecretaryController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [SupervisorSecretaryController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [SupervisorSecretaryController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [SupervisorSecretaryController::class, 'bulkReject'])->name('bulk-reject');
    });

    // Chairman Routes
    Route::prefix('ex-supervisor/chairman')->name('ex-supervisor.chairman.')->group(function () {
        Route::get('/', [SupervisorChairmanController::class, 'index'])->name('index');
        Route::get('/{application}', [SupervisorChairmanController::class, 'show'])->name('show');
    });

    // Report Routes
    Route::prefix('ex-supervisor/reports')->name('ex-supervisor.reports.')->group(function () {
        Route::get('/', [SupervisorReportController::class, 'index'])->name('index');
        Route::get('/preview', [SupervisorReportController::class, 'preview'])->name('preview');
        Route::get('/export-excel', [SupervisorReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [SupervisorReportController::class, 'exportPdf'])->name('export-pdf');
    });

    // Super Admin Routes
    Route::prefix('ex-supervisor/admin')->name('ex-supervisor.admin.')->group(function () {
        Route::get('/', [SupervisorAdminController::class, 'index'])->name('index');
        Route::get('/{application}/edit', [SupervisorAdminController::class, 'edit'])->name('edit');
        Route::put('/{application}', [SupervisorAdminController::class, 'update'])->name('update');
        Route::delete('/{application}', [SupervisorAdminController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [SupervisorAdminController::class, 'restore'])->name('restore');
        Route::post('/{application}/change-status', [SupervisorAdminController::class, 'changeStatus'])->name('change-status');
    });

    // ========== CONTRACTOR PERMIT ROUTES ==========
    // (Following same pattern as Electrician - 45+ routes)

    // Data Entry Operator Routes
    Route::prefix('ex-contractor/operator')->name('ex-contractor.operator.')->group(function () {
        Route::get('/', [ContractorOperatorController::class, 'index'])->name('index');
        Route::get('/pending', [ContractorOperatorController::class, 'pending'])->name('pending');
        Route::get('/rejected', [ContractorOperatorController::class, 'rejected'])->name('rejected');
        Route::get('/approved', [ContractorOperatorController::class, 'approved'])->name('approved');
        Route::get('/create', [ContractorOperatorController::class, 'create'])->name('create');
        Route::post('/', [ContractorOperatorController::class, 'store'])->name('store');
        Route::get('/{application}/edit', [ContractorOperatorController::class, 'edit'])->name('edit');
        Route::get('/{application}', [ContractorOperatorController::class, 'show'])->name('show');
        Route::put('/{application}', [ContractorOperatorController::class, 'update'])->name('update');
        Route::post('/{application}/save-tab', [ContractorOperatorController::class, 'saveTab'])->name('save-tab');
        Route::post('/{application}/submit', [ContractorOperatorController::class, 'submit'])->name('submit');
        Route::post('/bulk-submit', [ContractorOperatorController::class, 'bulkSubmit'])->name('bulk-submit');
        Route::post('/claim', [ContractorOperatorController::class, 'claim'])->name('claim');
        Route::delete('/{application}', [ContractorOperatorController::class, 'destroy'])->name('destroy');
    });

    // Office Assistant Routes
    Route::prefix('ex-contractor/office-assistant')->name('ex-contractor.office-assistant.')->group(function () {
        Route::get('/pending', [ContractorOfficeAssistantController::class, 'pending'])->name('pending');
        Route::get('/rejected', [ContractorOfficeAssistantController::class, 'rejected'])->name('rejected');
        Route::get('/approved', [ContractorOfficeAssistantController::class, 'approved'])->name('approved');
        Route::get('/{application}', [ContractorOfficeAssistantController::class, 'show'])->name('show');
        Route::post('/{application}/approve', [ContractorOfficeAssistantController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [ContractorOfficeAssistantController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [ContractorOfficeAssistantController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [ContractorOfficeAssistantController::class, 'bulkReject'])->name('bulk-reject');
    });

    // Secretary Routes
    Route::prefix('ex-contractor/secretary')->name('ex-contractor.secretary.')->group(function () {
        Route::get('/pending', [ContractorSecretaryController::class, 'pending'])->name('pending');
        Route::get('/rejected', [ContractorSecretaryController::class, 'rejected'])->name('rejected');
        Route::get('/approved', [ContractorSecretaryController::class, 'approved'])->name('approved');
        Route::get('/{application}', [ContractorSecretaryController::class, 'show'])->name('show');
        Route::post('/{application}/approve', [ContractorSecretaryController::class, 'approve'])->name('approve');
        Route::post('/{application}/reject', [ContractorSecretaryController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [ContractorSecretaryController::class, 'bulkApprove'])->name('bulk-approve');
        Route::post('/bulk-reject', [ContractorSecretaryController::class, 'bulkReject'])->name('bulk-reject');
    });

    // Chairman Routes
    Route::prefix('ex-contractor/chairman')->name('ex-contractor.chairman.')->group(function () {
        Route::get('/', [ContractorChairmanController::class, 'index'])->name('index');
        Route::get('/{application}', [ContractorChairmanController::class, 'show'])->name('show');
    });

    // Report Routes
    Route::prefix('ex-contractor/reports')->name('ex-contractor.reports.')->group(function () {
        Route::get('/', [ContractorReportController::class, 'index'])->name('index');
        Route::get('/preview', [ContractorReportController::class, 'preview'])->name('preview');
        Route::get('/export-excel', [ContractorReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [ContractorReportController::class, 'exportPdf'])->name('export-pdf');
    });

    // Super Admin Routes
    Route::prefix('ex-contractor/admin')->name('ex-contractor.admin.')->group(function () {
        Route::get('/', [ContractorAdminController::class, 'index'])->name('index');
        Route::get('/{application}/edit', [ContractorAdminController::class, 'edit'])->name('edit');
        Route::put('/{application}', [ContractorAdminController::class, 'update'])->name('update');
        Route::delete('/{application}', [ContractorAdminController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [ContractorAdminController::class, 'restore'])->name('restore');
        Route::post('/{application}/change-status', [ContractorAdminController::class, 'changeStatus'])->name('change-status');
    });
});

/*
 * TOTAL ROUTE COUNT: ~145 routes
 *  - Attachments: 3
 *  - Electrician: 47
 *  - Supervisor: 47 (pattern shown above, full implementation needed)
 *  - Contractor: 47 (pattern shown above, full implementation needed)
 */
