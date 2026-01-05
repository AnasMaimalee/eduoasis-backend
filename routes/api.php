<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Wallet\WalletController;
use App\Http\Controllers\Api\Wallet\PaymentController;
use App\Http\Controllers\Api\Wallet\ServiceRequestController;
use App\Http\Controllers\Api\ServiceProc\ServiceProcController;
use App\Http\Controllers\Api\Service\JambResult\JambResultController;
use App\Http\Controllers\Api\Service\JambAdmissionLetter\JambAdmissionLetterController;
use App\Http\Controllers\Api\Service\JambUploadStatus\JambUploadStatusController;
use App\Http\Controllers\Api\Service\JambAdmissionStatus\JambAdmissionStatusController;
use App\Http\Controllers\Api\Service\JambAdmissionResultNotification\JambAdmissionResultNotificationController;
use App\Http\Controllers\Api\Dashboard\AdminDashboardController;
use App\Http\Controllers\Api\Dashboard\UserDashboardController;
use App\Http\Controllers\Api\Dashboard\SuperAdminDashboardController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [MeController::class, 'register']);
    Route::post('/login', [MeController::class, 'login']);
});
Route::middleware('auth:api')->group(function () {

    // USER PROFILE
    Route::get('/me', [MeController::class, 'me']);
    Route::post('/me/create-administrator', [MeController::class, 'createAdministrator']);

    // WALLET
    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'index']);
        Route::get('/me', [WalletController::class, 'me']);
        Route::get('/transactions', [WalletController::class, 'transactions']);
        Route::post('/initialize', [PaymentController::class, 'initialize']);
        Route::post('/verify', [PaymentController::class, 'verify']);
    });

    // SERVICES CATALOG (CRUD)
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceProcController::class, 'index']);
        Route::post('/', [ServiceProcController::class, 'store']);
        Route::get('/{service}', [ServiceProcController::class, 'show']);
        Route::put('/{service}', [ServiceProcController::class, 'update']);
        Route::delete('/{service}', [ServiceProcController::class, 'destroy']);
    });

    // GENERIC SERVICE REQUEST
    Route::post('/service/request', [ServiceRequestController::class, 'request']);

    // JAMB RESULT SERVICE
    Route::prefix('services/jamb-result')->group(function () {

        // ================= USER =================
        Route::get('/my', [JambResultController::class, 'my'])
            ->middleware('role:user');

        Route::get('/administrator', [JambResultController::class, 'processedByAdmin'])
            ->middleware('role:administrator');

        Route::post('/', [JambResultController::class, 'store'])
            ->middleware('role:user');

        // ================= ADMIN =================
        Route::get('/pending', [JambResultController::class, 'pending'])
            ->middleware('role:administrator');

        Route::post('/{id}/take', [JambResultController::class, 'take'])
            ->middleware('role:administrator');

        Route::post('/{jambRequest}/complete', [JambResultController::class, 'complete'])
            ->middleware('role:administrator');

        // ================= SUPER ADMIN =================
        Route::post('/{jambRequest}/approve', [JambResultController::class, 'approve'])
            ->middleware('role:superadmin');

        Route::post('/{jambRequest}/reject', [JambResultController::class, 'reject'])
            ->middleware('role:superadmin');

        Route::get('/all', [JambResultController::class, 'all'])
            ->middleware('role:superadmin');
    });

});


Route::middleware('auth:api')->group(function () {

    Route::prefix('services/jamb-admission-letter')->group(function () {

        /**
         * =================
         * USER
         * =================
         */
        // Submit request
        Route::post('/', [JambAdmissionLetterController::class, 'store']);

        // User's own requests
        Route::get('/my', [JambAdmissionLetterController::class, 'my']);
        Route::get('/administrator', [JambAdmissionLetterController::class, 'processedByAdmin'])
            ->middleware('role:administrator');


        /**
         * =================
         * ADMIN
         * =================
         */
        // View pending jobs
        Route::get('/pending', [JambAdmissionLetterController::class, 'pending'])
            ->middleware('role:administrator');

        // Take job
        Route::post('/{id}/take', [JambAdmissionLetterController::class, 'take'])
            ->middleware('role:administrator');

        // Complete job (upload letter)
        Route::post('/{id}/complete', [JambAdmissionLetterController::class, 'complete'])
            ->middleware('role:administrator');

        /**
         * =================
         * SUPER ADMIN
         * =================
         */
        // Approve job
        Route::post('/{id}/approve', [JambAdmissionLetterController::class, 'approve'])
            ->middleware('role:superadmin');

        // Reject job
        Route::post('/{id}/reject', [JambAdmissionLetterController::class, 'reject'])
            ->middleware('role:superadmin');

        // View all jobs
        Route::get('/all', [JambAdmissionLetterController::class, 'all'])
            ->middleware('role:superadmin');
    });
});


Route::middleware('auth:api')->group(function () {

    Route::prefix('services/jamb-upload-status')->group(function () {

        /**
         * =================
         * USER
         * =================
         */
        // Submit request
        Route::post('/', [JambUploadStatusController::class, 'store']);

        // User's own requests
        Route::get('/my', [JambUploadStatusController::class, 'my']);
        Route::get('/administrator', [JambUploadStatusController::class, 'processedByAdmin']);

        /**
         * =================
         * ADMIN
         * =================
         */
        // View pending jobs
        Route::get('/pending', [JambUploadStatusController::class, 'pending'])
            ->middleware('role:administrator');

        // Take job
        Route::post('/{id}/take', [JambUploadStatusController::class, 'take'])
            ->middleware('role:administrator');

        // Complete job (upload letter)
        Route::post('/{id}/complete', [JambUploadStatusController::class, 'complete'])
            ->middleware('role:administrator');

        /**
         * =================
         * SUPER ADMIN
         * =================
         */
        // Approve job
        Route::post('/{id}/approve', [JambUploadStatusController::class, 'approve'])
            ->middleware('role:superadmin');

        // Reject job
        Route::post('/{id}/reject', [JambUploadStatusController::class, 'reject'])
            ->middleware('role:superadmin');

        // View all jobs
        Route::get('/all', [JambUploadStatusController::class, 'all'])
            ->middleware('role:superadmin');
    });
});


Route::middleware('auth:api')->group(function () {

    Route::prefix('services/jamb-admission-status')->group(function () {

        /**
         * =================
         * USER
         * =================
         */
        // Submit request
        Route::post('/', [JambAdmissionStatusController::class, 'store']);

        // User's own requests
        Route::get('/my', [JambAdmissionStatusController::class, 'my']);
        Route::get('/administrator', [JambAdmissionStatusController::class, 'processedByAdmin']);

        /**
         * =================
         * ADMIN
         * =================
         */
        // View pending jobs
        Route::get('/pending', [JambAdmissionStatusController::class, 'pending'])
            ->middleware('role:administrator');

        // Take job
        Route::post('/{id}/take', [JambAdmissionStatusController::class, 'take'])
            ->middleware('role:administrator');

        // Complete job (upload letter)
        Route::post('/{id}/complete', [JambAdmissionStatusController::class, 'complete'])
            ->middleware('role:administrator');

        /**
         * =================
         * SUPER ADMIN
         * =================
         */
        // Approve job
        Route::post('/{id}/approve', [JambAdmissionStatusController::class, 'approve'])
            ->middleware('role:superadmin');

        // Reject job
        Route::post('/{id}/reject', [JambAdmissionStatusController::class, 'reject'])
            ->middleware('role:superadmin');

        // View all jobs
        Route::get('/all', [JambAdmissionStatusController::class, 'all'])
            ->middleware('role:superadmin');
    });
});


Route::middleware('auth:api')->group(function () {

    Route::prefix('services/jamb-admission-result-notification')->group(function () {

        /**
         * =================
         * USER
         * =================
         */
        // Submit request
        Route::post('/', [JambAdmissionResultNotificationController::class, 'store']);

        // User's own requests
        Route::get('/my', [JambAdmissionResultNotificationController::class, 'my']);
        Route::get('/administrator', [JambAdmissionResultNotificationController::class, 'processedByAdmin'])
            ->middleware('role:administrator');

        /**
         * =================
         * ADMIN
         * =================
         */
        // View pending jobs
        Route::get('/pending', [JambAdmissionResultNotificationController::class, 'pending'])
            ->middleware('role:administrator');

        // Take job
        Route::post('/{id}/take', [JambAdmissionResultNotificationController::class, 'take'])
            ->middleware('role:administrator');

        // Complete job (upload letter)
        Route::post('/{id}/complete', [JambAdmissionResultNotificationController::class, 'complete'])
            ->middleware('role:administrator');

        /**
         * =================
         * SUPER ADMIN
         * =================
         */
        // Approve job
        Route::post('/{id}/approve', [JambAdmissionResultNotificationController::class, 'approve'])
            ->middleware('role:superadmin');

        // Reject job
        Route::post('/{id}/reject', [JambAdmissionResultNotificationController::class, 'reject'])
            ->middleware('role:superadmin');

        // View all jobs
        Route::get('/all', [JambAdmissionResultNotificationController::class, 'all'])
            ->middleware('role:superadmin');
    });
});

Route::middleware(['auth:api'])->group(function () {

    Route::get('/dashboard/user', [UserDashboardController::class, 'index']);

    Route::middleware('role:administrator')->group(function () {
        Route::get('/dashboard/admin', [AdminDashboardController::class, 'index']);
    });

    Route::middleware('role:superadmin')->group(function () {
        Route::get('/dashboard/superadmin', [SuperAdminDashboardController::class, 'index']);
    });

});

