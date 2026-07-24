<?php

use App\Http\Controllers\API\AdminContentController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AiController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CitizenServiceController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\EmergencyController;
use App\Http\Controllers\API\LearningController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\OtpController;
use App\Http\Controllers\API\ReminderController;
use App\Http\Controllers\API\SyncController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\AdvisorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Nagarik+ API Routes  (prefix: /api/v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ── Public routes ─────────────────────────────────────────────────────

    Route::prefix('auth')->group(function () {
        Route::post('register',    [AuthController::class, 'register']);
        Route::post('login',       [AuthController::class, 'login']);
        Route::post('login/pin',   [AuthController::class, 'loginWithPin']);
        // OTP / PIN reset (public)
        Route::post('send-otp',    [OtpController::class, 'send']);
        Route::post('verify-otp',  [OtpController::class, 'verify']);
        Route::post('forgot-pin',  [OtpController::class, 'forgotPin']);
        Route::post('reset-pin',   [OtpController::class, 'resetPin']);
    });

    // Public: Citizen services (guides)
    Route::prefix('services')->group(function () {
        Route::get('/',               [CitizenServiceController::class, 'index']);
        Route::get('/{slug}',         [CitizenServiceController::class, 'show']);
        Route::get('/{slug}/offices', [CitizenServiceController::class, 'offices']);
    });

    // Public: Offices
    Route::get('offices', [CitizenServiceController::class, 'allOffices']);

    // Public: News
    Route::prefix('news')->group(function () {
        Route::get('/',           [NewsController::class, 'index']);
        Route::get('/categories', [NewsController::class, 'categories']);
        Route::get('/shorts',     [NewsController::class, 'shorts']);
        Route::get('/{id}',       [NewsController::class, 'show']);
        Route::get('/{id}/comments', [NewsController::class, 'getComments']);
    });

    // Public: Emergency contacts
    Route::get('emergency', [EmergencyController::class, 'index']);

    // Public: Banners
    Route::get('banners', [BannerController::class, 'index']);

    // Public: Home screen content
    Route::get('social-services', [HomeController::class, 'socialServices']);
    Route::get('vital-events', [HomeController::class, 'vitalEvents']);

    // Public: AI suggestions (no auth needed)
    Route::get('ai/suggestions', [AiController::class, 'suggestions']);

    // Public: Road signs
    Route::get('learning/road-signs', [LearningController::class, 'roadSigns']);
    Route::get('learning/shorts',     [LearningController::class, 'shorts']);
    Route::get('learning/tutorials',  [LearningController::class, 'shorts']);

    // Public: Quiz questions (guest preview)
    Route::get('learning/questions', [LearningController::class, 'questions']);

    // Public: Advisors
    Route::prefix('advisors')->group(function () {
        Route::get('/categories', [AdvisorController::class, 'categories']);
        Route::get('/', [AdvisorController::class, 'index']);
        Route::get('/{id}', [AdvisorController::class, 'show']);
    });


    // ── Authenticated routes ───────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::get('profile',  [AuthController::class, 'profile']);
            Route::put('profile',  [AuthController::class, 'updateProfile']);
            Route::post('pin',     [AuthController::class, 'setPin']);
            Route::post('logout',  [AuthController::class, 'logout']);
        });

        // Documents (Digital Locker)
        Route::prefix('documents')->group(function () {
            Route::get('/',               [DocumentController::class, 'index']);
            Route::post('/',              [DocumentController::class, 'store']);
            Route::get('/expiring',       [DocumentController::class, 'expiringSoon']);
            Route::get('/{id}',           [DocumentController::class, 'show']);
            Route::put('/{id}',           [DocumentController::class, 'update']);
            Route::delete('/{id}',        [DocumentController::class, 'destroy']);
            Route::get('/{id}/download',  [DocumentController::class, 'download']);
        });

        // News social features
        Route::prefix('news')->group(function () {
            Route::post('/{id}/like',    [NewsController::class, 'toggleLike']);
            Route::post('/{id}/bookmark', [NewsController::class, 'toggleBookmark']);
            Route::get('/bookmarks',     [NewsController::class, 'getBookmarks']);
            Route::post('/{id}/comments', [NewsController::class, 'addComment']);
            Route::delete('/comments/{commentId}', [NewsController::class, 'deleteComment']);
            Route::post('/{id}/share',   [NewsController::class, 'share']);
        });

        // Advisors
        Route::prefix('advisors')->group(function () {
            Route::post('/{advisorId}/book', [AdvisorController::class, 'bookConsultation']);
            Route::get('/consultations/my',  [AdvisorController::class, 'myConsultations']);
            Route::post('/{advisorId}/review', [AdvisorController::class, 'addReview']);
        });

        // Reminders
        Route::prefix('reminders')->group(function () {
            Route::get('/',        [ReminderController::class, 'index']);
            Route::post('/',       [ReminderController::class, 'store']);
            Route::put('/{id}',    [ReminderController::class, 'update']);
            Route::delete('/{id}', [ReminderController::class, 'destroy']);
        });

        // Learning (quiz submission requires auth)
        Route::prefix('learning')->group(function () {
            Route::post('submit',  [LearningController::class, 'submitAnswers']);
            Route::get('history',  [LearningController::class, 'history']);
        });

        // AI Assistant
        Route::prefix('ai')->group(function () {
            Route::post('chat',    [AiController::class, 'chat']);
            Route::get('history',  [AiController::class, 'history']);
        });

        // Notification preferences + device token
        Route::prefix('notifications')->group(function () {
            Route::get('preferences',    [NotificationController::class, 'preferences']);
            Route::put('preferences',    [NotificationController::class, 'updatePreferences']);
            Route::get('/',              [NotificationController::class, 'index']);
            Route::post('device-token',  [NotificationController::class, 'registerToken']);
        });

        // Cloud sync
        Route::prefix('sync')->group(function () {
            Route::get('status',   [SyncController::class, 'status']);
            Route::post('enable',  [SyncController::class, 'enable']);
            Route::post('disable', [SyncController::class, 'disable']);
            Route::post('trigger', [SyncController::class, 'trigger']);
        });
    });

    // ── Admin API (role-protected) ─────────────────────────────────────────
    Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
        // Stats & user management
        Route::get('stats',              [AdminController::class, 'stats']);
        Route::get('users',              [AdminController::class, 'listUsers']);
        Route::get('users/{id}',         [AdminController::class, 'showUser']);
        Route::post('users/{id}/ban',    [AdminController::class, 'banUser']);
        Route::delete('users/{id}',      [AdminController::class, 'deleteUser']);

        // Content CRUD
        Route::get('news',                    [AdminContentController::class, 'listNews']);
        Route::post('news',                   [AdminContentController::class, 'storeNews']);
        Route::put('news/{id}',              [AdminContentController::class, 'updateNews']);
        Route::post('news/{id}/publish',     [AdminContentController::class, 'publishNews']);
        Route::delete('news/{id}',           [AdminContentController::class, 'destroyNews']);

        Route::get('services',               [AdminContentController::class, 'listServices']);
        Route::post('services',              [AdminContentController::class, 'storeService']);
        Route::put('services/{id}',         [AdminContentController::class, 'updateService']);
        Route::delete('services/{id}',      [AdminContentController::class, 'destroyService']);

        Route::get('questions',              [AdminContentController::class, 'listQuestions']);
        Route::post('questions',             [AdminContentController::class, 'storeQuestion']);
        Route::put('questions/{id}',        [AdminContentController::class, 'updateQuestion']);
        Route::delete('questions/{id}',     [AdminContentController::class, 'destroyQuestion']);

        Route::get('road-signs',             [AdminContentController::class, 'listRoadSigns']);
        Route::post('road-signs',            [AdminContentController::class, 'storeRoadSign']);
        Route::put('road-signs/{id}',       [AdminContentController::class, 'updateRoadSign']);
        Route::delete('road-signs/{id}',    [AdminContentController::class, 'destroyRoadSign']);

        Route::get('emergency',              [AdminContentController::class, 'listEmergency']);
        Route::post('emergency',             [AdminContentController::class, 'storeEmergency']);
        Route::put('emergency/{id}',        [AdminContentController::class, 'updateEmergency']);
        Route::delete('emergency/{id}',     [AdminContentController::class, 'destroyEmergency']);

        Route::get('offices',                [AdminContentController::class, 'listOffices']);
        Route::post('offices',               [AdminContentController::class, 'storeOffice']);
        Route::put('offices/{id}',          [AdminContentController::class, 'updateOffice']);
        Route::delete('offices/{id}',       [AdminContentController::class, 'destroyOffice']);
    });
});
