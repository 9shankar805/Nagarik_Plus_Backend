<?php

use App\Http\Controllers\API\AdminContentController;
use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\API\NidDownloadController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AiController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CitizenServiceController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\EmergencyController;
use App\Http\Controllers\API\HospitalController;
use App\Http\Controllers\API\LearningController;
use App\Http\Controllers\API\LearningFeatureController;
use App\Http\Controllers\API\VideoClassController;
use App\Http\Controllers\API\SubscriptionController;
use App\Http\Controllers\API\ParentalMonitoringController;
use App\Http\Controllers\API\AdvancedLearningController;
use App\Http\Controllers\API\AdaptiveTestController;
use App\Http\Controllers\API\DoubtController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\OtpController;
use App\Http\Controllers\API\ReminderController;
use App\Http\Controllers\API\SyncController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\AdvisorController;
use App\Http\Controllers\API\FormSubmissionController;
use App\Http\Controllers\API\KycController;
use App\Http\Controllers\API\StaticContentController;
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
        Route::post('google',      [AuthController::class, 'google']);
        Route::post('apple',       [AuthController::class, 'apple']);
        // OTP / PIN reset (public)
        Route::post('send-otp',         [OtpController::class, 'send']);
        Route::post('verify-otp',       [OtpController::class, 'verify']);
        Route::post('forgot-pin',       [OtpController::class, 'forgotPin']);
        Route::post('reset-pin',        [OtpController::class, 'resetPin']);
        // Password reset (public)
        Route::post('forgot-password',  [OtpController::class, 'forgotPassword']);
        Route::post('verify-reset-otp', [OtpController::class, 'verifyResetPasswordOtp']);
        Route::post('reset-password',   [OtpController::class, 'resetPassword']);
    });

    // Public: Citizen services (guides)
    Route::prefix('services')->group(function () {
        Route::get('/',               [CitizenServiceController::class, 'index']);
        Route::get('/{slug}',         [CitizenServiceController::class, 'show']);
        Route::get('/{slug}/offices', [CitizenServiceController::class, 'offices']);
    });

    // Public: Offices
    Route::get('offices', [CitizenServiceController::class, 'allOffices']);

    // Public: Static Content
    Route::get('static-content', [StaticContentController::class, 'index']);

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

    // Public: Programs → Courses → Subjects hierarchy
    Route::prefix('programs')->group(function () {
        Route::get('/',             [CourseController::class, 'programs']);
        Route::get('/{slug}',       [CourseController::class, 'program']);
    });

    Route::prefix('courses')->group(function () {
        Route::get('/{id}',         [CourseController::class, 'course'])->where('id','[0-9]+');
    });

    // Public: Live sessions
    Route::prefix('live-sessions')->group(function () {
        Route::get('/',             [CourseController::class, 'liveSessions']);
        Route::get('/{id}',         [CourseController::class, 'liveSession'])->where('id','[0-9]+');
    });

    // Public: Doubts/Q&A
    Route::prefix('doubts')->group(function () {
        Route::get('/',             [CourseController::class, 'doubts']);
        Route::get('/{id}',         [CourseController::class, 'doubt'])->where('id','[0-9]+');
    });

    // Public: Hospitals
    Route::prefix('hospitals')->group(function () {
        Route::get('nearby',  [HospitalController::class, 'nearby']);
        Route::get('/',       [HospitalController::class, 'index']);
        Route::get('/{id}',   [HospitalController::class, 'show'])->where('id', '[0-9]+');
    });

    // Public: Banners
    Route::get('banners', [BannerController::class, 'index']);

    // Public: Home screen content
    Route::get('social-services', [HomeController::class, 'socialServices']);
    Route::get('vital-events', [HomeController::class, 'vitalEvents']);

    // Public: AI suggestions (no auth needed)
    Route::get('ai/suggestions', [AiController::class, 'suggestions']);

    // ── Public: Learning Center ───────────────────────────────────────────
    Route::prefix('learning')->group(function () {
        // Categories
        Route::get('categories',              [LearningController::class, 'categories']);
        Route::get('categories/{slug}',       [LearningController::class, 'category']);

        // Chapters / Study material
        Route::get('chapters',                [LearningController::class, 'chapters']);
        Route::get('chapters/{id}',           [LearningController::class, 'chapter'])->where('id', '[0-9]+');

        // Mock tests (listing — no auth needed)
        Route::get('mock-tests',              [LearningController::class, 'mockTests']);
        Route::get('mock-tests/{id}',         [LearningController::class, 'mockTest'])->where('id', '[0-9]+');

        // Competitions (listing / detail / leaderboard — public)
        Route::get('competitions',            [LearningController::class, 'competitions']);
        Route::get('competitions/{id}',       [LearningController::class, 'competition'])->where('id', '[0-9]+');
        Route::get('competitions/{id}/leaderboard', [LearningController::class, 'leaderboard'])->where('id', '[0-9]+');

        // Legacy: road signs, shorts, questions (backward compatible)
        Route::get('road-signs',              [LearningController::class, 'roadSigns']);
        Route::get('shorts',                  [LearningController::class, 'shorts']);
        Route::get('tutorials',               [LearningController::class, 'shorts']);
        Route::get('questions',               [LearningController::class, 'questions']);

        // ── Ambition Guru features (public / guest) ────────────────────────
        Route::get('daily-quiz',              [LearningFeatureController::class, 'dailyQuiz']);
        Route::get('flashcard-sets',          [LearningFeatureController::class, 'flashcardSets']);
        Route::get('flashcard-sets/{id}',     [LearningFeatureController::class, 'flashcardSet'])->where('id', '[0-9]+');
        Route::get('achievements',            [LearningFeatureController::class, 'achievements']);
    });

    // Public: Advisors
    Route::prefix('advisors')->group(function () {
        Route::get('/categories', [AdvisorController::class, 'categories']);
        Route::get('/', [AdvisorController::class, 'index']);
        Route::get('/{id}', [AdvisorController::class, 'show']);
    });


    // ── Government Document Verification ──────────────────────────────────
    // Public: status check (no auth — Flutter app checks on startup)
    Route::get('verification/status', [VerificationController::class, 'status']);

    // ── DONIDCR eNID Download (3-step: captcha → OTP → download) ──────────
    // Step 1: public captcha proxy (no auth needed to show the image)
    Route::get('nid/captcha', [NidDownloadController::class, 'captcha']);
    // Steps 2 & 3: require login
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('nid/request-otp', [NidDownloadController::class, 'requestOtp']);
        Route::post('nid/download',    [NidDownloadController::class, 'download']);
    });

    // Public: CAPTCHA image proxy (Phase 6 — disabled until DONIDCR grants API access)
    Route::get('verification/captcha/nid', [VerificationController::class, 'nidCaptcha']);

    // Protected: actual verification endpoints (require login)
    Route::middleware('auth:sanctum')->prefix('verification')->group(function () {
        Route::post('nid',         [VerificationController::class, 'verifyNid']);
        Route::post('licence',     [VerificationController::class, 'verifyLicence']);
        Route::post('pan',         [VerificationController::class, 'verifyPan']);
        Route::post('citizenship', [VerificationController::class, 'verifyCitizenship']);
    });

    // ── Authenticated routes ───────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::get('profile',  [AuthController::class, 'profile']);
            Route::put('profile',  [AuthController::class, 'updateProfile']);
            Route::post('pin',     [AuthController::class, 'setPin']);
            Route::post('change-password', [AuthController::class, 'changePassword']);
            Route::post('logout',  [AuthController::class, 'logout']);
        });

        // KYC
        Route::prefix('user/kyc')->group(function () {
            Route::post('submit', [\App\Http\Controllers\API\KycController::class, 'submit']);
            Route::get('status',  [\App\Http\Controllers\API\KycController::class, 'status']);
        });

        // Digital ID (Requires KYC)
        Route::get('user/digital-id', [\App\Http\Controllers\API\DigitalIdController::class, 'generate'])->middleware('kyc.verified');

        // Digital Forms
        Route::prefix('forms')->middleware('kyc.verified')->group(function () {
            Route::post('submit', [FormSubmissionController::class, 'submit']);
            Route::get('/',       [FormSubmissionController::class, 'index']);
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
            // Timed mock test session
            Route::post('mock-tests/{id}/start',          [LearningController::class, 'startMockTest'])->where('id', '[0-9]+');

            // Answer submission (practice + timed)
            Route::post('submit',                         [LearningController::class, 'submitAnswers']);

            // Competition actions (auth required)
            Route::post('competitions/{id}/register',     [LearningController::class, 'registerForCompetition'])->where('id', '[0-9]+');
            Route::post('competitions/{id}/start',        [LearningController::class, 'startCompetition'])->where('id', '[0-9]+');
            Route::post('competitions/{id}/submit',       [LearningController::class, 'submitCompetition'])->where('id', '[0-9]+');
            Route::get('competitions/{id}/result',        [LearningController::class, 'myCompetitionResult'])->where('id', '[0-9]+');
            Route::get('competitions/my',                 [LearningController::class, 'myCompetitions']);

            // Progress & bookmarks
            Route::post('chapters/{id}/read',             [LearningController::class, 'markChapterRead'])->where('id', '[0-9]+');
            Route::get('progress',                        [LearningController::class, 'progress']);
            Route::post('bookmarks',                      [LearningController::class, 'toggleBookmark']);
            Route::get('bookmarks',                       [LearningController::class, 'bookmarks']);

            // History & stats
            Route::get('history',                         [LearningController::class, 'history']);
            Route::get('stats',                           [LearningController::class, 'stats']);

            // ── Ambition Guru features (auth required) ─────────────────────
            // Daily Quiz
            Route::post('daily-quiz/answer',              [LearningFeatureController::class, 'answerDailyQuiz']);
            Route::get('daily-quiz/history',              [LearningFeatureController::class, 'dailyQuizHistory']);

            // Streak & activity
            Route::get('streak',                          [LearningFeatureController::class, 'streak']);

            // Flashcard spaced repetition
            Route::post('flashcards/{id}/review',         [LearningFeatureController::class, 'reviewFlashcard'])->where('id', '[0-9]+');
            Route::get('flashcards/due',                  [LearningFeatureController::class, 'dueFlashcards']);

            // Practice mode (un-timed drilling)
            Route::post('practice/start',                 [LearningFeatureController::class, 'startPractice']);
            Route::post('practice/submit',                [LearningFeatureController::class, 'submitPractice']);
            Route::get('practice/history',                [LearningFeatureController::class, 'practiceHistory']);

            // Chapter rating
            Route::post('chapters/{id}/rate',             [LearningFeatureController::class, 'rateChapter'])->where('id', '[0-9]+');

            // Achievements / Badges
            Route::get('achievements/my',                 [LearningFeatureController::class, 'myAchievements']);

            // Wrong-only re-test (retry incorrect questions from a past attempt)
            Route::get('mock-tests/{id}/retry-wrong',     [LearningController::class, 'retryWrong'])->where('id', '[0-9]+');
        });

        // Programs / Courses (auth — enroll, my courses)
        Route::prefix('programs')->group(function () {
            Route::get('/',         [CourseController::class, 'programs']);
            Route::get('/{slug}',   [CourseController::class, 'program']);
        });

        Route::prefix('courses')->group(function () {
            Route::get('/my',       [CourseController::class, 'myCourses']);
            Route::get('/{id}',     [CourseController::class, 'course'])->where('id','[0-9]+');
            Route::post('/{id}/enroll', [CourseController::class, 'enroll'])->where('id','[0-9]+');
        });

        // Doubts / Q&A (auth — ask, answer, accept)
        Route::prefix('doubts')->group(function () {
            Route::get('/my',                               [CourseController::class, 'myDoubts']);
            Route::post('/',                                [CourseController::class, 'storeDoubt']);
            Route::post('/{id}/answers',                    [CourseController::class, 'answerDoubt'])->where('id','[0-9]+');
            Route::post('/{doubtId}/answers/{answerId}/accept', [CourseController::class, 'acceptAnswer'])->where(['doubtId','[0-9]+','answerId','[0-9]+']);
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

        // Grievances (Hello Sarkar)
        Route::prefix('grievances')->group(function () {
            Route::get('categories', [\App\Http\Controllers\API\GrievanceController::class, 'categories']);
            Route::get('/',          [\App\Http\Controllers\API\GrievanceController::class, 'index']);
            Route::post('/',         [\App\Http\Controllers\API\GrievanceController::class, 'store']);
        });

        // Payments
        Route::prefix('payments')->group(function () {
            Route::post('initiate', [\App\Http\Controllers\API\PaymentController::class, 'initiate']);
            Route::post('verify',   [\App\Http\Controllers\API\PaymentController::class, 'verify']);
        });
    });

    // Mock Gateway UI (Public)
    Route::get('payments/mock-gateway', [\App\Http\Controllers\API\PaymentController::class, 'mockGatewayView']);

    // ── Video Classes (public listing, auth for watch tracking) ───────────
    Route::prefix('video-classes')->group(function () {
        Route::get('/',          [VideoClassController::class, 'index']);
        Route::get('/{id}',      [VideoClassController::class, 'show'])->where('id', '[0-9]+');
        Route::get('/live',      [VideoClassController::class, 'live']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('video-classes')->group(function () {
            Route::post('/{id}/watch',   [VideoClassController::class, 'markWatched'])->where('id', '[0-9]+');
            Route::get('/my-history',    [VideoClassController::class, 'watchHistory']);
        });

        // ── Subscriptions ──────────────────────────────────────────────────
        Route::prefix('subscriptions')->group(function () {
            Route::get('/packages',      [SubscriptionController::class, 'packages']);
            Route::get('/my',            [SubscriptionController::class, 'mySubscription']);
            Route::post('/subscribe',    [SubscriptionController::class, 'subscribe']);
            Route::post('/cancel',       [SubscriptionController::class, 'cancel']);
        });

        // ── Parental Monitoring ────────────────────────────────────────────
        Route::prefix('parental')->group(function () {
            Route::get('/my-children',   [ParentalMonitoringController::class, 'myChildren']);
            Route::get('/child/{id}/progress', [ParentalMonitoringController::class, 'childProgress'])->where('id', '[0-9]+');
            Route::post('/link',         [ParentalMonitoringController::class, 'requestLink']);
            Route::post('/link/{id}/accept', [ParentalMonitoringController::class, 'acceptLink'])->where('id', '[0-9]+');
            Route::delete('/link/{id}',  [ParentalMonitoringController::class, 'removeLink'])->where('id', '[0-9]+');
            // Student side
            Route::get('/my-parents',    [ParentalMonitoringController::class, 'myParents']);
        });

        // ── Advanced Learning (syllabus map, topic mastery) ───────────────
        Route::prefix('advanced-learning')->group(function () {
            Route::get('/syllabus/{categorySlug}', [AdvancedLearningController::class, 'syllabus']);
            Route::get('/mastery',                 [AdvancedLearningController::class, 'masteryMap']);
            Route::get('/recommendations',         [AdvancedLearningController::class, 'recommendations']);
            Route::get('/weak-areas',              [AdvancedLearningController::class, 'weakAreas']);
        });

        // ── Adaptive Test ─────────────────────────────────────────────────
        Route::prefix('adaptive-test')->group(function () {
            Route::post('/start',        [AdaptiveTestController::class, 'start']);
            Route::post('/answer',       [AdaptiveTestController::class, 'submitAnswer']);
            Route::post('/finish',       [AdaptiveTestController::class, 'finish']);
            Route::get('/history',       [AdaptiveTestController::class, 'history']);
        });

        // ── Doubt / Q&A (dedicated controller) ───────────────────────────
        Route::prefix('doubts')->group(function () {
            Route::get('/',              [DoubtController::class, 'index']);
            Route::post('/',             [DoubtController::class, 'store']);
            Route::get('/{id}',          [DoubtController::class, 'show'])->where('id', '[0-9]+');
            Route::delete('/{id}',       [DoubtController::class, 'destroy'])->where('id', '[0-9]+');
            Route::post('/{id}/answers', [DoubtController::class, 'answer'])->where('id', '[0-9]+');
            Route::post('/{id}/upvote',  [DoubtController::class, 'upvote'])->where('id', '[0-9]+');
            Route::patch('/{doubtId}/answers/{answerId}/accept',
                         [DoubtController::class, 'acceptAnswer'])
                ->where(['doubtId' => '[0-9]+', 'answerId' => '[0-9]+']);
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
