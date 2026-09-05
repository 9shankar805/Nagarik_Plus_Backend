<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminArFilterController;
use App\Http\Controllers\Admin\AdminDocumentController;
use App\Http\Controllers\Admin\AdminDocumentTemplateController;
use App\Http\Controllers\Admin\AdminFormSubmissionController;
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
use App\Http\Controllers\Admin\AdminLearningCategoryController;
use App\Http\Controllers\Admin\AdminLearningChapterController;
use App\Http\Controllers\Admin\AdminMockTestController;
use App\Http\Controllers\Admin\AdminCompetitionController;
use App\Http\Controllers\Admin\AdminFlashcardController;
use App\Http\Controllers\Admin\AdminDailyQuizController;
use App\Http\Controllers\Admin\AdminAchievementController;
use App\Http\Controllers\Admin\AdminProgramController;
use App\Http\Controllers\Admin\AdminVideoClassController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminParentalController;
use App\Http\Controllers\Admin\AdminDoubtController;
use App\Http\Controllers\Admin\AdminChapterRatingController;
use App\Http\Controllers\API\HealthController;
use App\Http\Controllers\Web\AuthController as WebAuthController;
use App\Http\Controllers\Web\LegalController;
use Illuminate\Support\Facades\Route;

// ── Health Check ──────────────────────────────────────────────────────────
Route::get('/health', [HealthController::class, 'check'])->name('health');

// ── Public Homepage ───────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ── Public Programs / Courses (Ambition Guru style) ───────────────────────
Route::get('/programs', [\App\Http\Controllers\Web\ProgramController::class, 'index'])->name('programs.index');
Route::get('/programs/{slug}', [\App\Http\Controllers\Web\ProgramController::class, 'show'])->name('programs.show');

Route::get('/locale/{lang}', [\App\Http\Controllers\Web\LocaleController::class, 'switch'])->name('locale.switch');

// ── Legal Pages ───────────────────────────────────────────────────────────
Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])->name('privacy-policy')->withoutMiddleware([\App\Http\Middleware\MaintenanceMode::class]);
Route::get('/delete-account', [LegalController::class, 'deleteAccount'])->name('delete-account')->withoutMiddleware([\App\Http\Middleware\MaintenanceMode::class]);
Route::post('/delete-account', [LegalController::class, 'submitDeleteAccount'])->name('delete-account.submit')->withoutMiddleware([\App\Http\Middleware\MaintenanceMode::class]);

// ── User Portal ───────────────────────────────────────────────────────────
Route::prefix('')->name('user.')->group(function () {
    Route::get('login',   [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('login',  [WebAuthController::class, 'login'])->name('login.post');
    Route::get('register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('register', [WebAuthController::class, 'register'])->name('register.post');
    Route::post('logout', [WebAuthController::class, 'logout'])->name('logout');

    Route::get('/pdf-tools', function () {
        return view('user.pdf-tools');
    })->name('pdf-tools');

    Route::get('/transfer', \App\Livewire\Public\FileTransfer::class)->name('file-transfer');

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

        Route::get('/verification', [\App\Http\Controllers\Web\VerificationWebController::class, 'index'])
            ->name('verification');

        Route::get('/nid-download', function () {
            return view('user.nid-download');
        })->name('nid-download');
    });
});

// ── Admin Portal ──────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login',   [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login',  [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('users',            AdminUserController::class)->except(['edit', 'update']);
        Route::post('users/{user}/ban',    [AdminUserController::class, 'ban'])->name('users.ban');
        Route::post('users/{user}/kyc-review', [AdminUserController::class, 'kycReview'])->name('users.kyc_review');
        Route::resource('documents',        AdminDocumentController::class)->only(['index', 'show']);
        Route::resource('document-templates', AdminDocumentTemplateController::class);
        Route::resource('form-submissions', AdminFormSubmissionController::class)->except(['create', 'store', 'edit']);
        Route::resource('news',             AdminNewsController::class);
        Route::post('news/{news}/approve',  [AdminNewsController::class, 'approve'])->name('news.approve');
        Route::post('news/{news}/reject',   [AdminNewsController::class, 'reject'])->name('news.reject');
        Route::post('news/{news}/send-back',[AdminNewsController::class, 'sendBack'])->name('news.send-back');
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

        // Hello Sarkar Grievances
        Route::resource('grievances',       \App\Http\Controllers\Admin\AdminGrievanceController::class)->only(['index', 'show', 'update']);

        // Payments / Transactions
        Route::resource('transactions',     \App\Http\Controllers\Admin\AdminTransactionController::class)->only(['index', 'show']);

        // Audit Logs
        Route::resource('audit-logs',       \App\Http\Controllers\Admin\AdminAuditLogController::class)->only(['index', 'show']);

        Route::get('notifications',          [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/broadcast', [AdminNotificationController::class, 'broadcast'])->name('notifications.broadcast');

        Route::resource('reminders',        AdminReminderController::class)->only(['index', 'destroy']);

        Route::get('ai/logs',        [AdminAiController::class, 'logs'])->name('ai.logs');
        Route::get('ai/suggestions', [AdminAiController::class, 'suggestions'])->name('ai.suggestions');
        Route::resource('shorts',    AdminShortsController::class);
        Route::resource('ar-filters',AdminArFilterController::class);

        Route::get('settings',  [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [AdminSettingsController::class, 'update'])->name('settings.update');

        // ── Learning Center ───────────────────────────────────────────────
        // Categories
        Route::resource('learning/categories', AdminLearningCategoryController::class)
            ->names('learning.categories')
            ->parameters(['categories' => 'category']);

        // Chapters
        Route::resource('learning/chapters', AdminLearningChapterController::class)
            ->names('learning.chapters')
            ->parameters(['chapters' => 'chapter']);

        // Mock Tests
        Route::resource('learning/mock-tests', AdminMockTestController::class)
            ->names('learning.mock-tests')
            ->parameters(['mock-tests' => 'mockTest']);

        // Competitions
        Route::resource('learning/competitions', AdminCompetitionController::class)
            ->names('learning.competitions')
            ->parameters(['competitions' => 'competition']);

        // Competition extra actions
        Route::get('learning/competitions/{competition}/registrations',
            [AdminCompetitionController::class, 'registrations'])
            ->name('learning.competitions.registrations');

        Route::get('learning/competitions/{competition}/leaderboard',
            [AdminCompetitionController::class, 'leaderboard'])
            ->name('learning.competitions.leaderboard');

        Route::post('learning/competitions/{competition}/compute-leaderboard',
            [AdminCompetitionController::class, 'computeLeaderboard'])
            ->name('learning.competitions.compute-leaderboard');

        Route::post('learning/competitions/{competition}/announce-winners',
            [AdminCompetitionController::class, 'announceWinners'])
            ->name('learning.competitions.announce-winners');

        Route::get('learning/competitions/{competition}/export',
            [AdminCompetitionController::class, 'exportResults'])
            ->name('learning.competitions.export');

        // ── Flashcard Sets ────────────────────────────────────────────────
        Route::resource('learning/flashcards', AdminFlashcardController::class)
            ->names('learning.flashcards')
            ->parameters(['flashcards' => 'flashcard']);

        // Individual card management (nested under a set)
        Route::post('learning/flashcards/{flashcard}/cards',
            [AdminFlashcardController::class, 'storeCard'])
            ->name('learning.flashcards.cards.store');

        Route::put('learning/flashcards/cards/{card}',
            [AdminFlashcardController::class, 'updateCard'])
            ->name('learning.flashcards.cards.update');

        Route::delete('learning/flashcards/cards/{card}',
            [AdminFlashcardController::class, 'destroyCard'])
            ->name('learning.flashcards.cards.destroy');

        // ── Daily Quiz ────────────────────────────────────────────────────
        Route::resource('learning/daily-quiz', AdminDailyQuizController::class)
            ->names('learning.daily-quiz')
            ->parameters(['daily-quiz' => 'dailyQuiz']);

        Route::get('learning/daily-quiz/{dailyQuiz}/stats',
            [AdminDailyQuizController::class, 'stats'])
            ->name('learning.daily-quiz.stats');

        // ── Achievements / Badges ─────────────────────────────────────────
        Route::resource('learning/achievements', AdminAchievementController::class)
            ->names('learning.achievements')
            ->parameters(['achievements' => 'achievement']);

        // ── Programs → Courses → Subjects ─────────────────────────────────
        Route::resource('learning/programs', AdminProgramController::class)
            ->names('learning.programs')
            ->parameters(['programs' => 'program']);

        // Course CRUD (nested under program)
        Route::get('learning/programs/{program}/courses/create',
            [AdminProgramController::class, 'createCourse'])
            ->name('learning.courses.create');
        Route::post('learning/programs/{program}/courses',
            [AdminProgramController::class, 'storeCourse'])
            ->name('learning.courses.store');
        Route::get('learning/courses/{course}/edit',
            [AdminProgramController::class, 'editCourse'])
            ->name('learning.courses.edit');
        Route::put('learning/courses/{course}',
            [AdminProgramController::class, 'updateCourse'])
            ->name('learning.courses.update');
        Route::delete('learning/courses/{course}',
            [AdminProgramController::class, 'destroyCourse'])
            ->name('learning.courses.destroy');

        // Subject CRUD
        Route::post('learning/courses/{course}/subjects',
            [AdminProgramController::class, 'storeSubject'])
            ->name('learning.subjects.store');
        Route::delete('learning/subjects/{subject}',
            [AdminProgramController::class, 'destroySubject'])
            ->name('learning.subjects.destroy');

        // ── Live Sessions ──────────────────────────────────────────────────
        Route::get('learning/live-sessions',
            [AdminProgramController::class, 'liveSessions'])
            ->name('learning.live-sessions.index');
        Route::get('learning/live-sessions/create',
            [AdminProgramController::class, 'createLiveSession'])
            ->name('learning.live-sessions.create');
        Route::post('learning/live-sessions',
            [AdminProgramController::class, 'storeLiveSession'])
            ->name('learning.live-sessions.store');
        Route::get('learning/live-sessions/{liveSession}/edit',
            [AdminProgramController::class, 'editLiveSession'])
            ->name('learning.live-sessions.edit');
        Route::put('learning/live-sessions/{liveSession}',
            [AdminProgramController::class, 'updateLiveSession'])
            ->name('learning.live-sessions.update');
        Route::delete('learning/live-sessions/{liveSession}',
            [AdminProgramController::class, 'destroyLiveSession'])
            ->name('learning.live-sessions.destroy');

        // ── Video Classes ──────────────────────────────────────────────────
        Route::resource('learning/video-classes', AdminVideoClassController::class)
            ->names('learning.video-classes')
            ->parameters(['video-classes' => 'videoClass']);

        // ── Subscription Packages ──────────────────────────────────────────
        Route::resource('learning/subscriptions', AdminSubscriptionController::class)
            ->names('learning.subscriptions')
            ->parameters(['subscriptions' => 'subscription']);

        // ── Parental Monitoring ────────────────────────────────────────────
        Route::get('learning/parental',
            [AdminParentalController::class, 'index'])
            ->name('learning.parental.index');
        Route::get('learning/parental/students/{studentId}',
            [AdminParentalController::class, 'studentProgress'])
            ->name('learning.parental.student');
        Route::patch('learning/parental/{link}/approve',
            [AdminParentalController::class, 'approve'])
            ->name('learning.parental.approve');
        Route::delete('learning/parental/{link}',
            [AdminParentalController::class, 'destroy'])
            ->name('learning.parental.destroy');

        // ── Doubts / Questions ─────────────────────────────────────────────
        Route::get('learning/doubts',
            [AdminDoubtController::class, 'index'])
            ->name('learning.doubts.index');
        Route::get('learning/doubts/{doubt}',
            [AdminDoubtController::class, 'show'])
            ->name('learning.doubts.show');
        Route::post('learning/doubts/{doubt}/answer',
            [AdminDoubtController::class, 'answer'])
            ->name('learning.doubts.answer');
        Route::patch('learning/doubts/{doubt}/pin',
            [AdminDoubtController::class, 'togglePin'])
            ->name('learning.doubts.pin');
        Route::patch('learning/doubts/{doubt}/close',
            [AdminDoubtController::class, 'close'])
            ->name('learning.doubts.close');
        Route::patch('learning/doubts/{doubt}/reopen',
            [AdminDoubtController::class, 'reopen'])
            ->name('learning.doubts.reopen');
        Route::delete('learning/doubts/{doubt}',
            [AdminDoubtController::class, 'destroy'])
            ->name('learning.doubts.destroy');
        Route::delete('learning/doubts/answers/{answer}',
            [AdminDoubtController::class, 'destroyAnswer'])
            ->name('learning.doubts.answer.destroy');

        // ── Chapter Ratings ────────────────────────────────────────────────
        Route::get('learning/chapter-ratings',
            [AdminChapterRatingController::class, 'index'])
            ->name('learning.chapter-ratings.index');
        Route::get('learning/chapter-ratings/summary',
            [AdminChapterRatingController::class, 'chapterSummary'])
            ->name('learning.chapter-ratings.summary');
        Route::delete('learning/chapter-ratings/{rating}',
            [AdminChapterRatingController::class, 'destroy'])
            ->name('learning.chapter-ratings.destroy');

        // ── Mock Test — Question Assignment ───────────────────────────────
        Route::get('learning/mock-tests/{mockTest}/questions',
            [AdminMockTestController::class, 'questions'])
            ->name('learning.mock-tests.questions');
        Route::put('learning/mock-tests/{mockTest}/questions',
            [AdminMockTestController::class, 'syncQuestions'])
            ->name('learning.mock-tests.questions.sync');

        // ── Document Verification Audit Logs ──────────────────────────────
        Route::get('verification/audit-logs',
            [\App\Http\Controllers\Admin\AdminVerificationController::class, 'auditLogs'])
            ->name('verification.audit-logs');
    });
});
