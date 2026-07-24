<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminDocumentController;
use App\Http\Controllers\Admin\AdminNewsController;
use App\Http\Controllers\Admin\AdminServiceController;
use App\Http\Controllers\Admin\AdminOfficeController;
use App\Http\Controllers\Admin\AdminQuizController;
use App\Http\Controllers\Admin\AdminRoadSignController;
use App\Http\Controllers\Admin\AdminEmergencyController;
use App\Http\Controllers\Admin\AdminBannerController;
use App\Http\Controllers\Admin\AdminSocialServiceController;
use App\Http\Controllers\Admin\AdminVitalEventController;
use App\Http\Controllers\Admin\AdminAdvisorController;
use App\Http\Controllers\Admin\AdminHospitalController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminReminderController;
use App\Http\Controllers\Admin\AdminAiController;
use App\Http\Controllers\Admin\AdminShortsController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Web\AuthController as WebAuthController;
use Illuminate\Support\Facades\Route;

// ── Public Homepage ───────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ── User Portal ───────────────────────────────────────────────────────────
Route::prefix('')->name('user.')->group(function () {
    Route::get('login',   [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('login',  [WebAuthController::class, 'login'])->name('login.post');
    Route::get('register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('logout', [WebAuthController::class, 'logout'])->name('logout');

    Route::get('/pdf-tools', function () {
        return view('user.pdf-tools');
    })->name('pdf-tools');

    Route::get('/pdf-tool', function () {
        return view('user.pdf-tools');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', function () {
            return view('user.dashboard');
        })->name('dashboard');
        
        Route::get('/documents', function () {
            return view('user.documents');
        })->name('documents');
        
        Route::get('/guides', [\App\Http\Controllers\Web\GuideController::class, 'index'])->name('guides');
        Route::get('/guides/{slug}', [\App\Http\Controllers\Web\GuideController::class, 'show'])->name('guides.show');
        
        Route::get('/news', [\App\Http\Controllers\Web\NewsController::class, 'index'])->name('news');
        
        Route::get('/learning', function () {
            return view('user.learning');
        })->name('learning');
        
        Route::get('/profile', function () {
            return view('user.profile');
        })->name('profile');
    });
});

// ── Admin Portal ──────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login',   [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login',  [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('users',            AdminUserController::class)->only(['index', 'show', 'destroy']);
        Route::post('users/{user}/ban',    [AdminUserController::class, 'ban'])->name('users.ban');
        Route::resource('documents',        AdminDocumentController::class)->only(['index', 'show']);
        Route::resource('news',             AdminNewsController::class);
        Route::resource('services',         AdminServiceController::class);
        Route::resource('offices',          AdminOfficeController::class);
        Route::resource('quiz',             AdminQuizController::class);
        Route::resource('road-signs',       AdminRoadSignController::class);
        Route::resource('emergency',        AdminEmergencyController::class);
        Route::resource('banners',          AdminBannerController::class);
        Route::resource('social-services',  AdminSocialServiceController::class);
        Route::resource('vital-events',     AdminVitalEventController::class);
        Route::resource('advisors',         AdminAdvisorController::class);
        Route::get('consultations',         [AdminAdvisorController::class, 'consultations'])->name('advisors.consultations');
        Route::resource('hospitals',        AdminHospitalController::class);

        Route::get('notifications',          [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/broadcast', [AdminNotificationController::class, 'broadcast'])->name('notifications.broadcast');

        Route::resource('reminders',        AdminReminderController::class)->only(['index', 'destroy']);

        Route::get('ai/logs',        [AdminAiController::class, 'logs'])->name('ai.logs');
        Route::get('ai/suggestions', [AdminAiController::class, 'suggestions'])->name('ai.suggestions');
        Route::resource('shorts',    AdminShortsController::class);

        Route::get('settings',  [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [AdminSettingsController::class, 'update'])->name('settings.update');
    });
});
