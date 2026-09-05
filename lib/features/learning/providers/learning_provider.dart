import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/road_sign.dart';
import '../models/learning_models.dart';
import '../repositories/learning_repository.dart';

// ─────────────────────────────────────────────────────────────────────────────
// LearningProvider — state management for all 17 endpoint groups
// ─────────────────────────────────────────────────────────────────────────────
class LearningProvider extends ChangeNotifier {
  final LearningRepository _repo;

  LearningProvider({LearningRepository? repository})
      : _repo = repository ?? LearningRepository();

  // ── Error ──────────────────────────────────────────────────────────────────
  String? _errorMessage;
  String? get errorMessage => _errorMessage;
  void _setError(Object e) =>
      _errorMessage = e.toString().replaceFirst('Exception: ', '');
  void clearError() { _errorMessage = null; notifyListeners(); }

  // ══════════════════════════════════════════════════════════════════════════
  // 1. Programs
  // ══════════════════════════════════════════════════════════════════════════
  List<Program> _programs = [];
  List<Program> _myCourses = [];
  bool _isLoadingPrograms = false;

  List<Program> get programs => _programs;
  List<Program> get myCourses => _myCourses;
  bool get isLoadingPrograms => _isLoadingPrograms;

  Future<void> loadPrograms() async {
    _isLoadingPrograms = true; notifyListeners();
    try { _programs = await _repo.getPrograms(); }
    catch (e) { _setError(e); }
    finally { _isLoadingPrograms = false; notifyListeners(); }
  }

  Future<void> loadMyCourses() async {
    try { _myCourses = await _repo.getMyCourses(); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  Future<Map<String, dynamic>> enrollCourse(String id) async {
    _isSubmitting = true; notifyListeners();
    try {
      final r = await _repo.enrollCourse(id);
      await loadMyCourses();
      return r;
    } catch (e) { _setError(e); rethrow; }
    finally { _isSubmitting = false; notifyListeners(); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 2. Categories
  // ══════════════════════════════════════════════════════════════════════════
  List<LearningCategory> _categories = [];
  bool _isLoadingCategories = false;
  String? _activeCategorySlug;

  List<LearningCategory> get categories => _categories;
  bool get isLoadingCategories => _isLoadingCategories;
  String? get activeCategorySlug => _activeCategorySlug;

  LearningCategory? get activeCategory {
    if (_activeCategorySlug == null) return null;
    try { return _categories.firstWhere((c) => c.slug == _activeCategorySlug); }
    catch (_) { return null; }
  }

  void setActiveCategory(String? slug) {
    if (_activeCategorySlug == slug) return;
    _activeCategorySlug = slug; notifyListeners();
    loadChapters(categoryId: slug);
    loadMockTests(categoryId: slug);
  }

  Future<void> loadCategories() async {
    _isLoadingCategories = true; notifyListeners();
    try { _categories = await _repo.getCategories(); }
    catch (e) { _setError(e); }
    finally { _isLoadingCategories = false; notifyListeners(); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 3. Chapters
  // ══════════════════════════════════════════════════════════════════════════
  List<LearningChapter> _chapters = [];
  bool _isLoadingChapters = false;

  List<LearningChapter> get chapters => _chapters;
  bool get isLoadingChapters => _isLoadingChapters;

  Future<void> loadChapters({String? categoryId}) async {
    _isLoadingChapters = true; notifyListeners();
    try { _chapters = await _repo.getChapters(categoryId: categoryId); }
    catch (e) { _setError(e); }
    finally { _isLoadingChapters = false; notifyListeners(); }
  }

  Future<void> markChapterAsRead(String id, {int timeSpentSeconds = 0}) async {
    try {
      await _repo.markChapterAsRead(id, timeSpentSeconds: timeSpentSeconds);
      final idx = _chapters.indexWhere((c) => c.id == id);
      if (idx != -1) {
        final c = _chapters[idx];
        _chapters[idx] = LearningChapter(
          id: c.id, categorySlug: c.categorySlug,
          title: c.title, titleNp: c.titleNp,
          content: c.content, contentNp: c.contentNp,
          isRead: true, readTimeMinutes: c.readTimeMinutes,
        );
        notifyListeners();
      }
      await incrementStreak();
    } catch (e) { _setError(e); }
  }

  Future<Map<String, dynamic>> rateChapter(
    String id, { required int rating, String? comment }) async {
    try { return await _repo.rateChapter(id, rating: rating, comment: comment); }
    catch (e) { _setError(e); rethrow; }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 4. Mock Tests
  // ══════════════════════════════════════════════════════════════════════════
  List<MockTest> _mockTests = [];
  bool _isLoadingMockTests = false;
  Map<String, dynamic> _retryWrong = {};

  List<MockTest> get mockTests => _mockTests;
  bool get isLoadingMockTests => _isLoadingMockTests;
  Map<String, dynamic> get retryWrong => _retryWrong;

  Future<void> loadMockTests({String? categoryId, int? featured}) async {
    _isLoadingMockTests = true; notifyListeners();
    try { _mockTests = await _repo.getMockTests(categoryId: categoryId, featured: featured); }
    catch (e) { _setError(e); }
    finally { _isLoadingMockTests = false; notifyListeners(); }
  }

  Future<Map<String, dynamic>> startMockTest(String id) async {
    _isSubmitting = true; notifyListeners();
    try { return await _repo.startMockTest(id); }
    catch (e) { _setError(e); rethrow; }
    finally { _isSubmitting = false; notifyListeners(); }
  }

  Future<void> loadRetryWrong(String id, {String? attemptId}) async {
    try { _retryWrong = await _repo.getRetryWrong(id, attemptId: attemptId); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 5. Submit & History
  // ══════════════════════════════════════════════════════════════════════════
  List<Map<String, dynamic>> _quizHistory = [];
  LearningStat? _stats;
  Map<String, dynamic> _progress = {};
  bool _isLoadingHistory = false;
  bool _isLoadingStats = false;
  bool _isSubmitting = false;

  List<Map<String, dynamic>> get quizHistory => _quizHistory;
  LearningStat? get stats => _stats;
  Map<String, dynamic> get progress => _progress;
  bool get isLoadingHistory => _isLoadingHistory;
  bool get isLoadingStats => _isLoadingStats;
  bool get isSubmitting => _isSubmitting;

  Future<Map<String, dynamic>> submitQuiz(Map<String, dynamic> payload) async {
    _isSubmitting = true; notifyListeners();
    try {
      final r = await _repo.submitAnswers(payload);
      await incrementStreak();
      return r;
    } catch (e) { _setError(e); rethrow; }
    finally { _isSubmitting = false; notifyListeners(); }
  }

  Future<void> loadQuizHistory({String? category, String? type}) async {
    _isLoadingHistory = true; notifyListeners();
    try { _quizHistory = await _repo.getHistory(category: category, type: type); }
    catch (e) { _setError(e); }
    finally { _isLoadingHistory = false; notifyListeners(); }
  }

  Future<void> loadStats() async {
    _isLoadingStats = true; notifyListeners();
    try { _stats = await _repo.getStats(); }
    catch (e) { _setError(e); }
    finally { _isLoadingStats = false; notifyListeners(); }
  }

  Future<void> loadProgress() async {
    try { _progress = await _repo.getProgress(); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 6. Bookmarks
  // ══════════════════════════════════════════════════════════════════════════
  List<Map<String, dynamic>> _bookmarks = [];
  bool _isLoadingBookmarks = false;

  List<Map<String, dynamic>> get bookmarks => _bookmarks;
  bool get isLoadingBookmarks => _isLoadingBookmarks;

  Future<void> loadBookmarks() async {
    _isLoadingBookmarks = true; notifyListeners();
    try { _bookmarks = await _repo.getBookmarks(); }
    catch (e) { _setError(e); }
    finally { _isLoadingBookmarks = false; notifyListeners(); }
  }

  Future<void> toggleBookmark(String type, String id) async {
    try { await _repo.toggleBookmark(type, id); await loadBookmarks(); }
    catch (e) { _setError(e); rethrow; }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 7. Daily Quiz + Streak
  // ══════════════════════════════════════════════════════════════════════════
  DailyQuiz? _dailyQuiz;
  List<Map<String, dynamic>> _dailyQuizHistory = [];
  int _streakDays = 0;
  bool _isLoadingDailyQuiz = false;

  DailyQuiz? get dailyQuiz => _dailyQuiz;
  List<Map<String, dynamic>> get dailyQuizHistory => _dailyQuizHistory;
  int get streakDays => _streakDays;
  bool get isLoadingDailyQuiz => _isLoadingDailyQuiz;

  Future<void> loadDailyQuiz() async {
    _isLoadingDailyQuiz = true; notifyListeners();
    try { _dailyQuiz = await _repo.getDailyQuiz(); }
    catch (e) { _setError(e); }
    finally { _isLoadingDailyQuiz = false; notifyListeners(); }
  }

  Future<Map<String, dynamic>> answerDailyQuiz({
    required String dailyQuizId, required int selected }) async {
    try {
      final r = await _repo.answerDailyQuiz(dailyQuizId: dailyQuizId, selected: selected);
      await incrementStreak();
      return r;
    } catch (e) { _setError(e); rethrow; }
  }

  Future<void> loadDailyQuizHistory() async {
    try { _dailyQuizHistory = await _repo.getDailyQuizHistory(); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  Future<void> initStreak() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final lastStr = prefs.getString('last_study_date');
      final saved = prefs.getInt('current_streak') ?? 0;
      if (lastStr != null) {
        final last = DateTime.parse(lastStr);
        final now = DateTime.now();
        final diff = DateTime(now.year, now.month, now.day)
            .difference(DateTime(last.year, last.month, last.day)).inDays;
        _streakDays = diff > 1 ? 0 : saved;
        if (diff > 1) await prefs.setInt('current_streak', 0);
      } else {
        _streakDays = saved;
      }
      notifyListeners();
    } catch (e) { debugPrint('streak init error: $e'); }
  }

  Future<void> incrementStreak() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final lastStr = prefs.getString('last_study_date');
      final now = DateTime.now();
      bool should = false;
      if (lastStr != null) {
        final last = DateTime.parse(lastStr);
        final diff = DateTime(now.year, now.month, now.day)
            .difference(DateTime(last.year, last.month, last.day)).inDays;
        if (diff == 1) { should = true; }
        else if (diff > 1) { _streakDays = 0; should = true; }
      } else { should = true; }
      if (should) {
        _streakDays++;
        await prefs.setInt('current_streak', _streakDays);
        await prefs.setString('last_study_date', now.toIso8601String());
        notifyListeners();
      }
    } catch (e) { debugPrint('streak increment error: $e'); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 8. Flashcards
  // ══════════════════════════════════════════════════════════════════════════
  List<FlashcardSet> _flashcardSets = [];
  List<Flashcard> _flashcardsDue = [];
  bool _isLoadingFlashcards = false;

  List<FlashcardSet> get flashcardSets => _flashcardSets;
  List<Flashcard> get flashcardsDue => _flashcardsDue;
  bool get isLoadingFlashcards => _isLoadingFlashcards;

  Future<void> loadFlashcardSets() async {
    _isLoadingFlashcards = true; notifyListeners();
    try { _flashcardSets = await _repo.getFlashcardSets(); }
    catch (e) { _setError(e); }
    finally { _isLoadingFlashcards = false; notifyListeners(); }
  }

  Future<void> loadFlashcardsDue() async {
    try { _flashcardsDue = await _repo.getFlashcardsDue(); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  Future<Map<String, dynamic>> reviewFlashcard(String id, {required int quality}) async {
    try { return await _repo.reviewFlashcard(id, quality: quality); }
    catch (e) { _setError(e); rethrow; }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 9. Practice Mode
  // ══════════════════════════════════════════════════════════════════════════
  PracticeSession? _practiceSession;
  List<Map<String, dynamic>> _practiceHistory = [];
  bool _isLoadingPractice = false;

  PracticeSession? get practiceSession => _practiceSession;
  List<Map<String, dynamic>> get practiceHistory => _practiceHistory;
  bool get isLoadingPractice => _isLoadingPractice;

  Future<PracticeSession?> startPractice({
    required String category, String mode = 'all', String difficulty = 'medium' }) async {
    _isLoadingPractice = true; notifyListeners();
    try {
      _practiceSession = await _repo.startPractice(
        category: category, mode: mode, difficulty: difficulty);
      return _practiceSession;
    } catch (e) { _setError(e); return null; }
    finally { _isLoadingPractice = false; notifyListeners(); }
  }

  Future<PracticeSession?> submitPractice(Map<String, dynamic> payload) async {
    _isSubmitting = true; notifyListeners();
    try {
      final r = await _repo.submitPractice(payload);
      await incrementStreak();
      return r;
    } catch (e) { _setError(e); rethrow; }
    finally { _isSubmitting = false; notifyListeners(); }
  }

  Future<void> loadPracticeHistory() async {
    try { _practiceHistory = await _repo.getPracticeHistory(); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 10. Adaptive Test
  // ══════════════════════════════════════════════════════════════════════════
  AdaptiveSession? _adaptiveSession;
  List<Map<String, dynamic>> _adaptiveHistory = [];
  bool _isLoadingAdaptive = false;

  AdaptiveSession? get adaptiveSession => _adaptiveSession;
  List<Map<String, dynamic>> get adaptiveHistory => _adaptiveHistory;
  bool get isLoadingAdaptive => _isLoadingAdaptive;

  Future<AdaptiveSession?> startAdaptiveTest({required String mockTestId}) async {
    _isLoadingAdaptive = true; notifyListeners();
    try {
      _adaptiveSession = await _repo.startAdaptiveTest(mockTestId: mockTestId);
      return _adaptiveSession;
    } catch (e) { _setError(e); return null; }
    finally { _isLoadingAdaptive = false; notifyListeners(); }
  }

  Future<AdaptiveSession?> answerAdaptive({
    required String sessionToken, required String questionId, required int selected }) async {
    try {
      _adaptiveSession = await _repo.answerAdaptive(
        sessionToken: sessionToken, questionId: questionId, selected: selected);
      notifyListeners();
      return _adaptiveSession;
    } catch (e) { _setError(e); rethrow; }
  }

  Future<AdaptiveSession?> finishAdaptiveTest({required String sessionToken}) async {
    _isSubmitting = true; notifyListeners();
    try {
      final r = await _repo.finishAdaptiveTest(sessionToken: sessionToken);
      await incrementStreak();
      return r;
    } catch (e) { _setError(e); rethrow; }
    finally { _isSubmitting = false; notifyListeners(); }
  }

  Future<void> loadAdaptiveHistory() async {
    try { _adaptiveHistory = await _repo.getAdaptiveHistory(); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 11. Competitions
  // ══════════════════════════════════════════════════════════════════════════
  List<Competition> _competitions = [];
  List<Competition> _myCompetitions = [];
  List<CompetitionResult> _leaderboard = [];
  bool _isLoadingCompetitions = false;

  List<Competition> get competitions => _competitions;
  List<Competition> get myCompetitions => _myCompetitions;
  List<CompetitionResult> get leaderboard => _leaderboard;
  bool get isLoadingCompetitions => _isLoadingCompetitions;

  Future<void> loadCompetitions({String? status}) async {
    _isLoadingCompetitions = true; notifyListeners();
    try { _competitions = await _repo.getCompetitions(status: status); }
    catch (e) { _setError(e); }
    finally { _isLoadingCompetitions = false; notifyListeners(); }
  }

  Future<void> loadMyCompetitions() async {
    try { _myCompetitions = await _repo.getMyCompetitions(); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  Future<void> loadLeaderboard(String id) async {
    try { _leaderboard = await _repo.getCompetitionLeaderboard(id); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  Future<Map<String, dynamic>> startCompetition(String id) async {
    _isSubmitting = true; notifyListeners();
    try { return await _repo.startCompetition(id); }
    catch (e) { _setError(e); rethrow; }
    finally { _isSubmitting = false; notifyListeners(); }
  }

  Future<Map<String, dynamic>> submitCompetition(
    String id, Map<String, dynamic> payload) async {
    _isSubmitting = true; notifyListeners();
    try {
      final r = await _repo.submitCompetition(id, payload);
      await incrementStreak();
      return r;
    } catch (e) { _setError(e); rethrow; }
    finally { _isSubmitting = false; notifyListeners(); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 12. Doubts
  // ══════════════════════════════════════════════════════════════════════════
  List<Doubt> _doubts = [];
  List<Doubt> _myDoubts = [];
  bool _isLoadingDoubts = false;

  List<Doubt> get doubts => _doubts;
  List<Doubt> get myDoubts => _myDoubts;
  bool get isLoadingDoubts => _isLoadingDoubts;

  Future<void> loadDoubts({String? categoryId, String? status}) async {
    _isLoadingDoubts = true; notifyListeners();
    try { _doubts = await _repo.getDoubts(categoryId: categoryId, status: status); }
    catch (e) { _setError(e); }
    finally { _isLoadingDoubts = false; notifyListeners(); }
  }

  Future<void> loadMyDoubts() async {
    try { _myDoubts = await _repo.getMyDoubts(); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  Future<Doubt?> createDoubt(Map<String, dynamic> payload) async {
    _isSubmitting = true; notifyListeners();
    try { final r = await _repo.createDoubt(payload); await loadMyDoubts(); return r; }
    catch (e) { _setError(e); rethrow; }
    finally { _isSubmitting = false; notifyListeners(); }
  }

  Future<DoubtAnswer?> answerDoubt(String doubtId, Map<String, dynamic> payload) async {
    _isSubmitting = true; notifyListeners();
    try { return await _repo.answerDoubt(doubtId, payload); }
    catch (e) { _setError(e); rethrow; }
    finally { _isSubmitting = false; notifyListeners(); }
  }

  Future<void> upvoteDoubt(String id) async {
    try { await _repo.upvoteDoubt(id); }
    catch (e) { _setError(e); }
  }

  Future<void> deleteDoubt(String id) async {
    try {
      await _repo.deleteDoubt(id);
      _myDoubts.removeWhere((d) => d.id == id);
      notifyListeners();
    } catch (e) { _setError(e); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 13. Video Classes
  // ══════════════════════════════════════════════════════════════════════════
  List<VideoClass> _videoClasses = [];
  List<VideoClass> _liveClasses = [];
  List<Map<String, dynamic>> _videoHistory = [];
  bool _isLoadingVideos = false;

  List<VideoClass> get videoClasses => _videoClasses;
  List<VideoClass> get liveClasses => _liveClasses;
  List<Map<String, dynamic>> get videoHistory => _videoHistory;
  bool get isLoadingVideos => _isLoadingVideos;

  Future<void> loadVideoClasses({String? categoryId, String? status}) async {
    _isLoadingVideos = true; notifyListeners();
    try { _videoClasses = await _repo.getVideoClasses(categoryId: categoryId, status: status); }
    catch (e) { _setError(e); }
    finally { _isLoadingVideos = false; notifyListeners(); }
  }

  Future<void> loadLiveClasses() async {
    try { _liveClasses = await _repo.getLiveVideoClasses(); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  Future<void> markVideoWatched(String id) async {
    try { await _repo.markVideoWatched(id); await incrementStreak(); }
    catch (e) { _setError(e); }
  }

  Future<void> loadVideoHistory() async {
    try { _videoHistory = await _repo.getVideoHistory(); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 14. Advanced Analytics
  // ══════════════════════════════════════════════════════════════════════════
  Map<String, dynamic> _syllabus = {};
  Map<String, dynamic> _mastery = {};
  Map<String, dynamic> _weakAreas = {};
  Map<String, dynamic> _recommendations = {};
  bool _isLoadingAnalytics = false;

  Map<String, dynamic> get syllabus => _syllabus;
  Map<String, dynamic> get mastery => _mastery;
  Map<String, dynamic> get weakAreas => _weakAreas;
  Map<String, dynamic> get recommendations => _recommendations;
  bool get isLoadingAnalytics => _isLoadingAnalytics;

  Future<void> loadAnalytics({required String categorySlug}) async {
    _isLoadingAnalytics = true; notifyListeners();
    try {
      final results = await Future.wait([
        _repo.getSyllabus(categorySlug),
        _repo.getMastery(),
        _repo.getWeakAreas(),
        _repo.getRecommendations(),
      ]);
      _syllabus = results[0];
      _mastery = results[1];
      _weakAreas = results[2];
      _recommendations = results[3];
    } catch (e) { _setError(e); }
    finally { _isLoadingAnalytics = false; notifyListeners(); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 15. Achievements
  // ══════════════════════════════════════════════════════════════════════════
  List<Achievement> _achievements = [];
  List<Achievement> _myAchievements = [];
  bool _isLoadingAchievements = false;

  List<Achievement> get achievements => _achievements;
  List<Achievement> get myAchievements => _myAchievements;
  bool get isLoadingAchievements => _isLoadingAchievements;

  Future<void> loadAchievements() async {
    _isLoadingAchievements = true; notifyListeners();
    try {
      final results = await Future.wait([
        _repo.getAchievements(), _repo.getMyAchievements() ]);
      _achievements = results[0];
      _myAchievements = results[1];
    } catch (e) { _setError(e); }
    finally { _isLoadingAchievements = false; notifyListeners(); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 16. Subscriptions
  // ══════════════════════════════════════════════════════════════════════════
  List<SubscriptionPackage> _subscriptionPackages = [];
  Subscription? _mySubscription;
  bool _isLoadingSubscription = false;

  List<SubscriptionPackage> get subscriptionPackages => _subscriptionPackages;
  Subscription? get mySubscription => _mySubscription;
  bool get isLoadingSubscription => _isLoadingSubscription;

  Future<void> loadSubscriptionPackages() async {
    _isLoadingSubscription = true; notifyListeners();
    try { _subscriptionPackages = await _repo.getSubscriptionPackages(); }
    catch (e) { _setError(e); }
    finally { _isLoadingSubscription = false; notifyListeners(); }
  }

  Future<void> loadMySubscription() async {
    try { _mySubscription = await _repo.getMySubscription(); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  Future<Map<String, dynamic>> subscribe(String packageId) async {
    _isSubmitting = true; notifyListeners();
    try { final r = await _repo.subscribe(packageId); await loadMySubscription(); return r; }
    catch (e) { _setError(e); rethrow; }
    finally { _isSubmitting = false; notifyListeners(); }
  }

  Future<void> cancelSubscription() async {
    _isSubmitting = true; notifyListeners();
    try { await _repo.cancelSubscription(); await loadMySubscription(); }
    catch (e) { _setError(e); }
    finally { _isSubmitting = false; notifyListeners(); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // 17. Parental Monitoring
  // ══════════════════════════════════════════════════════════════════════════
  List<Map<String, dynamic>> _myChildren = [];
  List<Map<String, dynamic>> _myParents = [];
  Map<String, dynamic> _childProgress = {};
  bool _isLoadingParental = false;

  List<Map<String, dynamic>> get myChildren => _myChildren;
  List<Map<String, dynamic>> get myParents => _myParents;
  Map<String, dynamic> get childProgress => _childProgress;
  bool get isLoadingParental => _isLoadingParental;

  Future<void> loadMyChildren() async {
    _isLoadingParental = true; notifyListeners();
    try { _myChildren = await _repo.getMyChildren(); }
    catch (e) { _setError(e); }
    finally { _isLoadingParental = false; notifyListeners(); }
  }

  Future<void> loadChildProgress(String id) async {
    try { _childProgress = await _repo.getChildProgress(id); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  Future<void> loadMyParents() async {
    try { _myParents = await _repo.getMyParents(); notifyListeners(); }
    catch (e) { _setError(e); }
  }

  Future<Map<String, dynamic>> linkChild(String identifier) async {
    _isSubmitting = true; notifyListeners();
    try { return await _repo.linkChild(identifier); }
    catch (e) { _setError(e); rethrow; }
    finally { _isSubmitting = false; notifyListeners(); }
  }

  Future<void> removeParentalLink(String id) async {
    try { await _repo.removeParentalLink(id); await loadMyChildren(); }
    catch (e) { _setError(e); }
  }

  // ══════════════════════════════════════════════════════════════════════════
  // Legacy — road signs, shorts, quiz questions (used by existing UI)
  // ══════════════════════════════════════════════════════════════════════════
  List<LearningShort> _shorts = [];
  List<RoadSign> _roadSigns = [];
  List<QuizQuestion> _quizQuestions = [];
  bool _isLoadingShorts = false;
  bool _isLoadingRoadSigns = false;
  bool _isLoadingQuizQuestions = false;

  List<LearningShort> get shorts => _shorts;
  List<RoadSign> get roadSigns => _roadSigns;
  List<QuizQuestion> get quizQuestions => _quizQuestions;
  bool get isLoadingShorts => _isLoadingShorts;
  bool get isLoadingRoadSigns => _isLoadingRoadSigns;
  bool get isLoadingQuizQuestions => _isLoadingQuizQuestions;

  Future<void> loadShorts() async {
    _isLoadingShorts = true; notifyListeners();
    try { _shorts = await _repo.getShorts(); }
    catch (e) { _setError(e); }
    finally { _isLoadingShorts = false; notifyListeners(); }
  }

  Future<void> loadRoadSigns() async {
    _isLoadingRoadSigns = true; notifyListeners();
    try { _roadSigns = await _repo.getRoadSigns(); }
    catch (e) { _setError(e); }
    finally { _isLoadingRoadSigns = false; notifyListeners(); }
  }

  Future<void> loadQuizQuestions({
    String? category, String? difficulty, int? limit }) async {
    _isLoadingQuizQuestions = true; notifyListeners();
    try {
      _quizQuestions = await _repo.getQuestions(
        category: category, difficulty: difficulty, limit: limit);
    } catch (e) { _setError(e); }
    finally { _isLoadingQuizQuestions = false; notifyListeners(); }
  }
}
