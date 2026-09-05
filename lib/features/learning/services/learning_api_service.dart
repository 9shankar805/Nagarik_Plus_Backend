import '../../../core/network/api_client.dart';
import '../../../core/network/api_response.dart';
import '../models/road_sign.dart';
import '../models/learning_models.dart';

// ─────────────────────────────────────────────────────────────────────────────
// LearningApiService — all 17 endpoint groups
// Base: https://yourdomain.com/api/v1
// ─────────────────────────────────────────────────────────────────────────────
class LearningApiService {
  final ApiClient _api = ApiClient();

  // ── 1. Programs ─────────────────────────────────────────────────────────────

  Future<ApiResponse<List<Program>>> getPrograms() => _api.get(
        '/programs',
        fromJsonT: (j) => _list(j, Program.fromJson),
      );

  Future<ApiResponse<Program>> getProgram(String slug) => _api.get(
        '/programs/$slug',
        fromJsonT: (j) => Program.fromJson(_map(j)),
      );

  Future<ApiResponse<List<Program>>> getMyCourses() => _api.get(
        '/courses/my',
        fromJsonT: (j) => _list(j, Program.fromJson),
      );

  Future<ApiResponse<Map<String, dynamic>>> enrollCourse(String id) =>
      _api.post('/courses/$id/enroll', fromJsonT: _map);

  // ── 2. Learning Categories ───────────────────────────────────────────────────

  Future<ApiResponse<List<LearningCategory>>> getCategories() => _api.get(
        '/learning/categories',
        fromJsonT: (j) => _list(j, LearningCategory.fromJson),
      );

  Future<ApiResponse<LearningCategory>> getCategory(String slug) => _api.get(
        '/learning/categories/$slug',
        fromJsonT: (j) => LearningCategory.fromJson(_map(j)),
      );

  // ── 3. Chapters ──────────────────────────────────────────────────────────────

  Future<ApiResponse<List<LearningChapter>>> getChapters({
    String? categoryId,
  }) =>
      _api.get(
        '/learning/chapters',
        queryParameters: {if (categoryId != null) 'category_id': categoryId},
        fromJsonT: (j) => _list(j, LearningChapter.fromJson),
      );

  Future<ApiResponse<LearningChapter>> getChapter(String id) => _api.get(
        '/learning/chapters/$id',
        fromJsonT: (j) => LearningChapter.fromJson(_map(j)),
      );

  Future<ApiResponse<Map<String, dynamic>>> markChapterAsRead(
    String id, {
    int timeSpentSeconds = 0,
  }) =>
      _api.post(
        '/learning/chapters/$id/read',
        data: {'time_spent_seconds': timeSpentSeconds},
        fromJsonT: _map,
      );

  Future<ApiResponse<Map<String, dynamic>>> rateChapter(
    String id, {
    required int rating,
    String? comment,
  }) =>
      _api.post(
        '/learning/chapters/$id/rate',
        data: {'rating': rating, if (comment != null) 'comment': comment},
        fromJsonT: _map,
      );

  // ── 4. Mock Tests ────────────────────────────────────────────────────────────

  Future<ApiResponse<List<MockTest>>> getMockTests({
    String? categoryId,
    int? featured,
  }) =>
      _api.get(
        '/learning/mock-tests',
        queryParameters: {
          if (categoryId != null) 'category_id': categoryId,
          if (featured != null) 'featured': featured,
        },
        fromJsonT: (j) => _list(j, MockTest.fromJson),
      );

  Future<ApiResponse<MockTest>> getMockTest(String id) => _api.get(
        '/learning/mock-tests/$id',
        fromJsonT: (j) => MockTest.fromJson(_map(j)),
      );

  Future<ApiResponse<Map<String, dynamic>>> startMockTest(String id) =>
      _api.post('/learning/mock-tests/$id/start', fromJsonT: _map);

  /// GET /learning/mock-tests/{id}/retry-wrong?attempt_id=X
  Future<ApiResponse<Map<String, dynamic>>> getRetryWrong(
    String id, {
    String? attemptId,
  }) =>
      _api.get(
        '/learning/mock-tests/$id/retry-wrong',
        queryParameters: {if (attemptId != null) 'attempt_id': attemptId},
        fromJsonT: _map,
      );

  // ── 5. Submit & History ──────────────────────────────────────────────────────

  Future<ApiResponse<Map<String, dynamic>>> submitAnswers(
    Map<String, dynamic> payload,
  ) =>
      _api.post('/learning/submit', data: payload, fromJsonT: _map);

  Future<ApiResponse<List<Map<String, dynamic>>>> getHistory({
    String? category,
    String? type,
  }) =>
      _api.get(
        '/learning/history',
        queryParameters: {
          if (category != null) 'category': category,
          if (type != null) 'type': type,
        },
        fromJsonT: _mapList,
      );

  Future<ApiResponse<LearningStat>> getStats() => _api.get(
        '/learning/stats',
        fromJsonT: (j) => LearningStat.fromJson(_map(j)),
      );

  Future<ApiResponse<Map<String, dynamic>>> getProgress() =>
      _api.get('/learning/progress', fromJsonT: _map);

  // ── 6. Bookmarks ─────────────────────────────────────────────────────────────

  /// Body: { bookmarkable_id, bookmarkable_type: "chapter" }
  Future<ApiResponse<Map<String, dynamic>>> toggleBookmark(
    String type,
    String id,
  ) =>
      _api.post(
        '/learning/bookmarks',
        data: {'bookmarkable_id': id, 'bookmarkable_type': type},
        fromJsonT: _map,
      );

  Future<ApiResponse<List<Map<String, dynamic>>>> getBookmarks() =>
      _api.get('/learning/bookmarks', fromJsonT: _mapList);

  // ── 7. Daily Quiz ────────────────────────────────────────────────────────────

  Future<ApiResponse<DailyQuiz>> getDailyQuiz() => _api.get(
        '/learning/daily-quiz',
        fromJsonT: (j) => DailyQuiz.fromJson(_map(j)),
      );

  Future<ApiResponse<Map<String, dynamic>>> answerDailyQuiz({
    required String dailyQuizId,
    required int selected,
  }) =>
      _api.post(
        '/learning/daily-quiz/answer',
        data: {'daily_quiz_id': dailyQuizId, 'selected': selected},
        fromJsonT: _map,
      );

  Future<ApiResponse<List<Map<String, dynamic>>>> getDailyQuizHistory() =>
      _api.get('/learning/daily-quiz/history', fromJsonT: _mapList);

  Future<ApiResponse<Map<String, dynamic>>> getStreak() =>
      _api.get('/learning/streak', fromJsonT: _map);

  // ── 8. Flashcards ────────────────────────────────────────────────────────────

  Future<ApiResponse<List<FlashcardSet>>> getFlashcardSets() => _api.get(
        '/learning/flashcard-sets',
        fromJsonT: (j) => _list(j, FlashcardSet.fromJson),
      );

  Future<ApiResponse<FlashcardSet>> getFlashcardSet(String id) => _api.get(
        '/learning/flashcard-sets/$id',
        fromJsonT: (j) => FlashcardSet.fromJson(_map(j)),
      );

  Future<ApiResponse<List<Flashcard>>> getFlashcardsDue() => _api.get(
        '/learning/flashcards/due',
        fromJsonT: (j) => _list(j, Flashcard.fromJson),
      );

  /// quality: 0=forgot, 1=hard, 2=ok, 3=easy
  Future<ApiResponse<Map<String, dynamic>>> reviewFlashcard(
    String id, {
    required int quality,
  }) =>
      _api.post(
        '/learning/flashcards/$id/review',
        data: {'quality': quality},
        fromJsonT: _map,
      );

  // ── 9. Practice Mode ─────────────────────────────────────────────────────────

  /// mode: "all" | "wrong_only" | "bookmarked"
  /// difficulty: "easy" | "medium" | "hard"
  Future<ApiResponse<PracticeSession>> startPractice({
    required String category,
    String mode = 'all',
    String difficulty = 'medium',
  }) =>
      _api.post(
        '/learning/practice/start',
        data: {'category': category, 'mode': mode, 'difficulty': difficulty},
        fromJsonT: (j) => PracticeSession.fromJson(_map(j)),
      );

  Future<ApiResponse<PracticeSession>> submitPractice(
    Map<String, dynamic> payload,
  ) =>
      _api.post(
        '/learning/practice/submit',
        data: payload,
        fromJsonT: (j) => PracticeSession.fromJson(_map(j)),
      );

  Future<ApiResponse<List<Map<String, dynamic>>>> getPracticeHistory() =>
      _api.get('/learning/practice/history', fromJsonT: _mapList);

  // ── 10. Adaptive Test ────────────────────────────────────────────────────────

  Future<ApiResponse<AdaptiveSession>> startAdaptiveTest({
    required String mockTestId,
  }) =>
      _api.post(
        '/adaptive-test/start',
        data: {'mock_test_id': mockTestId},
        fromJsonT: (j) => AdaptiveSession.fromJson(_map(j)),
      );

  Future<ApiResponse<AdaptiveSession>> answerAdaptive({
    required String sessionToken,
    required String questionId,
    required int selected,
  }) =>
      _api.post(
        '/adaptive-test/answer',
        data: {
          'session_token': sessionToken,
          'question_id': questionId,
          'selected': selected,
        },
        fromJsonT: (j) => AdaptiveSession.fromJson(_map(j)),
      );

  Future<ApiResponse<AdaptiveSession>> finishAdaptiveTest({
    required String sessionToken,
  }) =>
      _api.post(
        '/adaptive-test/finish',
        data: {'session_token': sessionToken},
        fromJsonT: (j) => AdaptiveSession.fromJson(_map(j)),
      );

  Future<ApiResponse<List<Map<String, dynamic>>>> getAdaptiveHistory() =>
      _api.get('/adaptive-test/history', fromJsonT: _mapList);

  // ── 11. Competitions ─────────────────────────────────────────────────────────

  Future<ApiResponse<List<Competition>>> getCompetitions({String? status}) =>
      _api.get(
        '/learning/competitions',
        queryParameters: {if (status != null) 'status': status},
        fromJsonT: (j) => _list(j, Competition.fromJson),
      );

  Future<ApiResponse<Competition>> getCompetition(String id) => _api.get(
        '/learning/competitions/$id',
        fromJsonT: (j) => Competition.fromJson(_map(j)),
      );

  Future<ApiResponse<List<CompetitionResult>>> getCompetitionLeaderboard(
    String id,
  ) =>
      _api.get(
        '/learning/competitions/$id/leaderboard',
        fromJsonT: (j) => _list(j, CompetitionResult.fromJson),
      );

  Future<ApiResponse<Map<String, dynamic>>> registerForCompetition(
    String id,
  ) =>
      _api.post('/learning/competitions/$id/register', fromJsonT: _map);

  Future<ApiResponse<Map<String, dynamic>>> startCompetition(String id) =>
      _api.post('/learning/competitions/$id/start', fromJsonT: _map);

  Future<ApiResponse<Map<String, dynamic>>> submitCompetition(
    String id,
    Map<String, dynamic> payload,
  ) =>
      _api.post(
        '/learning/competitions/$id/submit',
        data: payload,
        fromJsonT: _map,
      );

  Future<ApiResponse<CompetitionResult>> getCompetitionResult(String id) =>
      _api.get(
        '/learning/competitions/$id/result',
        fromJsonT: (j) => CompetitionResult.fromJson(_map(j)),
      );

  Future<ApiResponse<List<Competition>>> getMyCompetitions() => _api.get(
        '/learning/competitions/my',
        fromJsonT: (j) => _list(j, Competition.fromJson),
      );

  // ── 12. Doubts ───────────────────────────────────────────────────────────────

  Future<ApiResponse<List<Doubt>>> getDoubts({
    String? categoryId,
    String? status,
  }) =>
      _api.get(
        '/doubts',
        queryParameters: {
          if (categoryId != null) 'category_id': categoryId,
          if (status != null) 'status': status,
        },
        fromJsonT: (j) => _list(j, Doubt.fromJson),
      );

  Future<ApiResponse<List<Doubt>>> getMyDoubts() => _api.get(
        '/doubts/my',
        fromJsonT: (j) => _list(j, Doubt.fromJson),
      );

  Future<ApiResponse<Doubt>> getDoubt(String id) => _api.get(
        '/doubts/$id',
        fromJsonT: (j) => Doubt.fromJson(_map(j)),
      );

  Future<ApiResponse<Doubt>> createDoubt(Map<String, dynamic> payload) =>
      _api.post(
        '/doubts',
        data: payload,
        fromJsonT: (j) => Doubt.fromJson(_map(j)),
      );

  Future<ApiResponse<DoubtAnswer>> answerDoubt(
    String doubtId,
    Map<String, dynamic> payload,
  ) =>
      _api.post(
        '/doubts/$doubtId/answers',
        data: payload,
        fromJsonT: (j) => DoubtAnswer.fromJson(_map(j)),
      );

  Future<ApiResponse<Map<String, dynamic>>> upvoteDoubt(String id) =>
      _api.post('/doubts/$id/upvote', fromJsonT: _map);

  Future<ApiResponse<Map<String, dynamic>>> acceptAnswer(
    String doubtId,
    String answerId,
  ) =>
      _api.put(
        '/doubts/$doubtId/answers/$answerId/accept',
        fromJsonT: _map,
      );

  Future<ApiResponse<Map<String, dynamic>>> deleteDoubt(String id) =>
      _api.post('/doubts/$id/delete', fromJsonT: _map);

  // ── 13. Video Classes ────────────────────────────────────────────────────────

  Future<ApiResponse<List<VideoClass>>> getVideoClasses({
    String? categoryId,
    String? status,
  }) =>
      _api.get(
        '/video-classes',
        queryParameters: {
          if (categoryId != null) 'category_id': categoryId,
          if (status != null) 'status': status,
        },
        fromJsonT: (j) => _list(j, VideoClass.fromJson),
      );

  Future<ApiResponse<List<VideoClass>>> getLiveVideoClasses() => _api.get(
        '/video-classes/live',
        fromJsonT: (j) => _list(j, VideoClass.fromJson),
      );

  Future<ApiResponse<VideoClass>> getVideoClass(String id) => _api.get(
        '/video-classes/$id',
        fromJsonT: (j) => VideoClass.fromJson(_map(j)),
      );

  Future<ApiResponse<Map<String, dynamic>>> markVideoWatched(String id) =>
      _api.post('/video-classes/$id/watch', fromJsonT: _map);

  Future<ApiResponse<List<Map<String, dynamic>>>> getVideoHistory() =>
      _api.get('/video-classes/my-history', fromJsonT: _mapList);

  // ── 14. Advanced Analytics ───────────────────────────────────────────────────

  Future<ApiResponse<Map<String, dynamic>>> getSyllabus(
    String categorySlug,
  ) =>
      _api.get(
        '/advanced-learning/syllabus/$categorySlug',
        fromJsonT: _map,
      );

  Future<ApiResponse<Map<String, dynamic>>> getMastery() =>
      _api.get('/advanced-learning/mastery', fromJsonT: _map);

  Future<ApiResponse<Map<String, dynamic>>> getWeakAreas() =>
      _api.get('/advanced-learning/weak-areas', fromJsonT: _map);

  Future<ApiResponse<Map<String, dynamic>>> getRecommendations() =>
      _api.get('/advanced-learning/recommendations', fromJsonT: _map);

  // ── 15. Achievements ─────────────────────────────────────────────────────────

  Future<ApiResponse<List<Achievement>>> getAchievements() => _api.get(
        '/learning/achievements',
        fromJsonT: (j) => _list(j, Achievement.fromJson),
      );

  Future<ApiResponse<List<Achievement>>> getMyAchievements() => _api.get(
        '/learning/achievements/my',
        fromJsonT: (j) => _list(j, Achievement.fromJson),
      );

  // ── 16. Subscriptions ────────────────────────────────────────────────────────

  Future<ApiResponse<List<SubscriptionPackage>>> getSubscriptionPackages() =>
      _api.get(
        '/subscriptions/packages',
        fromJsonT: (j) => _list(j, SubscriptionPackage.fromJson),
      );

  Future<ApiResponse<Map<String, dynamic>>> subscribe(String packageId) =>
      _api.post(
        '/subscriptions/subscribe',
        data: {'package_id': packageId},
        fromJsonT: _map,
      );

  Future<ApiResponse<Subscription>> getMySubscription() => _api.get(
        '/subscriptions/my',
        fromJsonT: (j) => Subscription.fromJson(_map(j)),
      );

  Future<ApiResponse<Map<String, dynamic>>> cancelSubscription() =>
      _api.post('/subscriptions/cancel', fromJsonT: _map);

  // ── 17. Parental Monitoring ──────────────────────────────────────────────────

  Future<ApiResponse<Map<String, dynamic>>> linkChild(
    String studentIdentifier,
  ) =>
      _api.post(
        '/parental/link',
        data: {'student_phone_or_email': studentIdentifier},
        fromJsonT: _map,
      );

  Future<ApiResponse<Map<String, dynamic>>> acceptParentalLink(String id) =>
      _api.post('/parental/link/$id/accept', fromJsonT: _map);

  Future<ApiResponse<Map<String, dynamic>>> removeParentalLink(String id) =>
      _api.post('/parental/link/$id/remove', fromJsonT: _map);

  Future<ApiResponse<List<Map<String, dynamic>>>> getMyChildren() =>
      _api.get('/parental/my-children', fromJsonT: _mapList);

  Future<ApiResponse<Map<String, dynamic>>> getChildProgress(String id) =>
      _api.get('/parental/child/$id/progress', fromJsonT: _map);

  Future<ApiResponse<List<Map<String, dynamic>>>> getMyParents() =>
      _api.get('/parental/my-parents', fromJsonT: _mapList);

  // ── Legacy road-signs / shorts (kept for existing UI) ───────────────────────

  Future<ApiResponse<List<RoadSign>>> getRoadSigns() => _api.get(
        '/learning/road-signs',
        fromJsonT: (j) {
          final raw = j is Map && j['data'] is List
              ? j['data'] as List
              : (j is List ? j : []);
          return raw
              .map((e) => RoadSign.fromJson(e as Map<String, dynamic>))
              .toList();
        },
      );

  Future<ApiResponse<List<LearningShort>>> getShorts() => _api.get(
        '/learning/shorts',
        fromJsonT: (j) => _list(j, LearningShort.fromJson),
      );

  Future<ApiResponse<List<QuizQuestion>>> getQuestions({
    String? category,
    String? difficulty,
    int? limit,
  }) =>
      _api.get(
        '/learning/questions',
        queryParameters: {
          if (category != null) 'category': category,
          if (difficulty != null) 'difficulty': difficulty,
          if (limit != null) 'limit': limit,
        },
        fromJsonT: (j) {
          final raw = j is Map && j['data'] is List
              ? j['data'] as List
              : (j is List ? j : []);
          return raw
              .map((e) => QuizQuestion.fromJson(e as Map<String, dynamic>))
              .toList();
        },
      );

  // ── Helpers ──────────────────────────────────────────────────────────────────

  List<T> _list<T>(dynamic j, T Function(Map<String, dynamic>) fromJson) {
    List raw;
    if (j is Map) {
      if (j['data'] is List) {
        raw = j['data'] as List;
      } else if (j['data'] is Map && (j['data'] as Map)['data'] is List) {
        raw = (j['data'] as Map)['data'] as List;
      } else {
        raw = [];
      }
    } else if (j is List) {
      raw = j;
    } else {
      raw = [];
    }
    return raw.map((e) => fromJson(e as Map<String, dynamic>)).toList();
  }

  Map<String, dynamic> _map(dynamic j) {
    if (j is Map && j['data'] is Map) return j['data'] as Map<String, dynamic>;
    if (j is Map) return j as Map<String, dynamic>;
    return {};
  }

  List<Map<String, dynamic>> _mapList(dynamic j) {
    if (j is Map && j['data'] is List) {
      return (j['data'] as List).map((e) => e as Map<String, dynamic>).toList();
    }
    if (j is List) return j.map((e) => e as Map<String, dynamic>).toList();
    return [];
  }
}
