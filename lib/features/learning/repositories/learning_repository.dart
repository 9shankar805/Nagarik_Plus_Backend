import '../models/road_sign.dart';
import '../models/learning_models.dart';
import '../services/learning_api_service.dart';

// ─────────────────────────────────────────────────────────────────────────────
// LearningRepository — typed wrappers over LearningApiService
// ─────────────────────────────────────────────────────────────────────────────
class LearningRepository {
  final LearningApiService _api;

  LearningRepository({LearningApiService? apiService})
      : _api = apiService ?? LearningApiService();

  // ── 1. Programs ─────────────────────────────────────────────────────────────
  Future<List<Program>> getPrograms() async =>
      (await _api.getPrograms()).data ?? [];

  Future<Program?> getProgram(String slug) async =>
      (await _api.getProgram(slug)).data;

  Future<List<Program>> getMyCourses() async =>
      (await _api.getMyCourses()).data ?? [];

  Future<Map<String, dynamic>> enrollCourse(String id) async =>
      (await _api.enrollCourse(id)).data ?? {};

  // ── 2. Categories ────────────────────────────────────────────────────────────
  Future<List<LearningCategory>> getCategories() async =>
      (await _api.getCategories()).data ?? [];

  Future<LearningCategory?> getCategory(String slug) async =>
      (await _api.getCategory(slug)).data;

  // ── 3. Chapters ──────────────────────────────────────────────────────────────
  Future<List<LearningChapter>> getChapters({String? categoryId}) async =>
      (await _api.getChapters(categoryId: categoryId)).data ?? [];

  Future<LearningChapter?> getChapter(String id) async =>
      (await _api.getChapter(id)).data;

  Future<Map<String, dynamic>> markChapterAsRead(
    String id, {
    int timeSpentSeconds = 0,
  }) async =>
      (await _api.markChapterAsRead(id, timeSpentSeconds: timeSpentSeconds))
          .data ?? {};

  Future<Map<String, dynamic>> rateChapter(
    String id, {
    required int rating,
    String? comment,
  }) async =>
      (await _api.rateChapter(id, rating: rating, comment: comment)).data ?? {};

  // ── 4. Mock Tests ────────────────────────────────────────────────────────────
  Future<List<MockTest>> getMockTests({
    String? categoryId,
    int? featured,
  }) async =>
      (await _api.getMockTests(categoryId: categoryId, featured: featured))
          .data ?? [];

  Future<MockTest?> getMockTest(String id) async =>
      (await _api.getMockTest(id)).data;

  Future<Map<String, dynamic>> startMockTest(String id) async =>
      (await _api.startMockTest(id)).data ?? {};

  Future<Map<String, dynamic>> getRetryWrong(
    String id, {
    String? attemptId,
  }) async =>
      (await _api.getRetryWrong(id, attemptId: attemptId)).data ?? {};

  // ── 5. Submit & History ──────────────────────────────────────────────────────
  Future<Map<String, dynamic>> submitAnswers(
    Map<String, dynamic> payload,
  ) async =>
      (await _api.submitAnswers(payload)).data ?? {};

  Future<List<Map<String, dynamic>>> getHistory({
    String? category,
    String? type,
  }) async =>
      (await _api.getHistory(category: category, type: type)).data ?? [];

  Future<LearningStat?> getStats() async => (await _api.getStats()).data;

  Future<Map<String, dynamic>> getProgress() async =>
      (await _api.getProgress()).data ?? {};

  // ── 6. Bookmarks ─────────────────────────────────────────────────────────────
  Future<Map<String, dynamic>> toggleBookmark(String type, String id) async =>
      (await _api.toggleBookmark(type, id)).data ?? {};

  Future<List<Map<String, dynamic>>> getBookmarks() async =>
      (await _api.getBookmarks()).data ?? [];

  // ── 7. Daily Quiz ────────────────────────────────────────────────────────────
  Future<DailyQuiz?> getDailyQuiz() async =>
      (await _api.getDailyQuiz()).data;

  Future<Map<String, dynamic>> answerDailyQuiz({
    required String dailyQuizId,
    required int selected,
  }) async =>
      (await _api.answerDailyQuiz(
              dailyQuizId: dailyQuizId, selected: selected))
          .data ?? {};

  Future<List<Map<String, dynamic>>> getDailyQuizHistory() async =>
      (await _api.getDailyQuizHistory()).data ?? [];

  Future<Map<String, dynamic>> getStreak() async =>
      (await _api.getStreak()).data ?? {};

  // ── 8. Flashcards ────────────────────────────────────────────────────────────
  Future<List<FlashcardSet>> getFlashcardSets() async =>
      (await _api.getFlashcardSets()).data ?? [];

  Future<FlashcardSet?> getFlashcardSet(String id) async =>
      (await _api.getFlashcardSet(id)).data;

  Future<List<Flashcard>> getFlashcardsDue() async =>
      (await _api.getFlashcardsDue()).data ?? [];

  Future<Map<String, dynamic>> reviewFlashcard(
    String id, {
    required int quality,
  }) async =>
      (await _api.reviewFlashcard(id, quality: quality)).data ?? {};

  // ── 9. Practice Mode ─────────────────────────────────────────────────────────
  Future<PracticeSession?> startPractice({
    required String category,
    String mode = 'all',
    String difficulty = 'medium',
  }) async =>
      (await _api.startPractice(
              category: category, mode: mode, difficulty: difficulty))
          .data;

  Future<PracticeSession?> submitPractice(
    Map<String, dynamic> payload,
  ) async =>
      (await _api.submitPractice(payload)).data;

  Future<List<Map<String, dynamic>>> getPracticeHistory() async =>
      (await _api.getPracticeHistory()).data ?? [];

  // ── 10. Adaptive Test ────────────────────────────────────────────────────────
  Future<AdaptiveSession?> startAdaptiveTest({
    required String mockTestId,
  }) async =>
      (await _api.startAdaptiveTest(mockTestId: mockTestId)).data;

  Future<AdaptiveSession?> answerAdaptive({
    required String sessionToken,
    required String questionId,
    required int selected,
  }) async =>
      (await _api.answerAdaptive(
              sessionToken: sessionToken,
              questionId: questionId,
              selected: selected))
          .data;

  Future<AdaptiveSession?> finishAdaptiveTest({
    required String sessionToken,
  }) async =>
      (await _api.finishAdaptiveTest(sessionToken: sessionToken)).data;

  Future<List<Map<String, dynamic>>> getAdaptiveHistory() async =>
      (await _api.getAdaptiveHistory()).data ?? [];

  // ── 11. Competitions ─────────────────────────────────────────────────────────
  Future<List<Competition>> getCompetitions({String? status}) async =>
      (await _api.getCompetitions(status: status)).data ?? [];

  Future<Competition?> getCompetition(String id) async =>
      (await _api.getCompetition(id)).data;

  Future<List<CompetitionResult>> getCompetitionLeaderboard(String id) async =>
      (await _api.getCompetitionLeaderboard(id)).data ?? [];

  Future<Map<String, dynamic>> registerForCompetition(String id) async =>
      (await _api.registerForCompetition(id)).data ?? {};

  Future<Map<String, dynamic>> startCompetition(String id) async =>
      (await _api.startCompetition(id)).data ?? {};

  Future<Map<String, dynamic>> submitCompetition(
    String id,
    Map<String, dynamic> payload,
  ) async =>
      (await _api.submitCompetition(id, payload)).data ?? {};

  Future<CompetitionResult?> getCompetitionResult(String id) async =>
      (await _api.getCompetitionResult(id)).data;

  Future<List<Competition>> getMyCompetitions() async =>
      (await _api.getMyCompetitions()).data ?? [];

  // ── 12. Doubts ───────────────────────────────────────────────────────────────
  Future<List<Doubt>> getDoubts({String? categoryId, String? status}) async =>
      (await _api.getDoubts(categoryId: categoryId, status: status)).data ?? [];

  Future<List<Doubt>> getMyDoubts() async =>
      (await _api.getMyDoubts()).data ?? [];

  Future<Doubt?> getDoubt(String id) async =>
      (await _api.getDoubt(id)).data;

  Future<Doubt?> createDoubt(Map<String, dynamic> payload) async =>
      (await _api.createDoubt(payload)).data;

  Future<DoubtAnswer?> answerDoubt(
    String doubtId,
    Map<String, dynamic> payload,
  ) async =>
      (await _api.answerDoubt(doubtId, payload)).data;

  Future<Map<String, dynamic>> upvoteDoubt(String id) async =>
      (await _api.upvoteDoubt(id)).data ?? {};

  Future<Map<String, dynamic>> acceptAnswer(
    String doubtId,
    String answerId,
  ) async =>
      (await _api.acceptAnswer(doubtId, answerId)).data ?? {};

  Future<Map<String, dynamic>> deleteDoubt(String id) async =>
      (await _api.deleteDoubt(id)).data ?? {};

  // ── 13. Video Classes ────────────────────────────────────────────────────────
  Future<List<VideoClass>> getVideoClasses({
    String? categoryId,
    String? status,
  }) async =>
      (await _api.getVideoClasses(categoryId: categoryId, status: status))
          .data ?? [];

  Future<List<VideoClass>> getLiveVideoClasses() async =>
      (await _api.getLiveVideoClasses()).data ?? [];

  Future<VideoClass?> getVideoClass(String id) async =>
      (await _api.getVideoClass(id)).data;

  Future<Map<String, dynamic>> markVideoWatched(String id) async =>
      (await _api.markVideoWatched(id)).data ?? {};

  Future<List<Map<String, dynamic>>> getVideoHistory() async =>
      (await _api.getVideoHistory()).data ?? [];

  // ── 14. Advanced Analytics ───────────────────────────────────────────────────
  Future<Map<String, dynamic>> getSyllabus(String categorySlug) async =>
      (await _api.getSyllabus(categorySlug)).data ?? {};

  Future<Map<String, dynamic>> getMastery() async =>
      (await _api.getMastery()).data ?? {};

  Future<Map<String, dynamic>> getWeakAreas() async =>
      (await _api.getWeakAreas()).data ?? {};

  Future<Map<String, dynamic>> getRecommendations() async =>
      (await _api.getRecommendations()).data ?? {};

  // ── 15. Achievements ─────────────────────────────────────────────────────────
  Future<List<Achievement>> getAchievements() async =>
      (await _api.getAchievements()).data ?? [];

  Future<List<Achievement>> getMyAchievements() async =>
      (await _api.getMyAchievements()).data ?? [];

  // ── 16. Subscriptions ────────────────────────────────────────────────────────
  Future<List<SubscriptionPackage>> getSubscriptionPackages() async =>
      (await _api.getSubscriptionPackages()).data ?? [];

  Future<Map<String, dynamic>> subscribe(String packageId) async =>
      (await _api.subscribe(packageId)).data ?? {};

  Future<Subscription?> getMySubscription() async =>
      (await _api.getMySubscription()).data;

  Future<Map<String, dynamic>> cancelSubscription() async =>
      (await _api.cancelSubscription()).data ?? {};

  // ── 17. Parental Monitoring ──────────────────────────────────────────────────
  Future<Map<String, dynamic>> linkChild(String identifier) async =>
      (await _api.linkChild(identifier)).data ?? {};

  Future<Map<String, dynamic>> acceptParentalLink(String id) async =>
      (await _api.acceptParentalLink(id)).data ?? {};

  Future<Map<String, dynamic>> removeParentalLink(String id) async =>
      (await _api.removeParentalLink(id)).data ?? {};

  Future<List<Map<String, dynamic>>> getMyChildren() async =>
      (await _api.getMyChildren()).data ?? [];

  Future<Map<String, dynamic>> getChildProgress(String id) async =>
      (await _api.getChildProgress(id)).data ?? {};

  Future<List<Map<String, dynamic>>> getMyParents() async =>
      (await _api.getMyParents()).data ?? [];

  // ── Legacy (road signs, shorts, questions) ───────────────────────────────────
  Future<List<RoadSign>> getRoadSigns() async =>
      (await _api.getRoadSigns()).data ?? [];

  Future<List<LearningShort>> getShorts() async =>
      (await _api.getShorts()).data ?? [];

  Future<List<QuizQuestion>> getQuestions({
    String? category,
    String? difficulty,
    int? limit,
  }) async =>
      (await _api.getQuestions(
              category: category, difficulty: difficulty, limit: limit))
          .data ?? [];
}
