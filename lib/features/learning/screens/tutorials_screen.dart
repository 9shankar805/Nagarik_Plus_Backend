import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/l10n/l10n_extension.dart';
import '../providers/learning_provider.dart';
import '../models/learning_models.dart';
import 'chapter_detail_screen.dart';
import 'course_selection_screen.dart';
import 'mock_test_screen.dart';
import 'practice_screen.dart';
import 'flashcards_screen.dart';
import 'video_lectures_screen.dart';

class TutorialsScreen extends StatefulWidget {
  const TutorialsScreen({super.key});

  @override
  State<TutorialsScreen> createState() => _TutorialsScreenState();
}

class _TutorialsScreenState extends State<TutorialsScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tab;
  String _searchQuery = '';

  @override
  void initState() {
    super.initState();
    _tab = TabController(
      length: 4,
      vsync: this,
    );

    SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.light,
    ));

    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = context.read<LearningProvider>();
      provider.loadCategories();
      provider.loadChapters();
      provider.loadMockTests();
      provider.loadStats();
      provider.loadProgress();
      provider.loadQuizHistory();
      provider.loadCompetitions();
      provider.loadBookmarks();
      provider.initStreak();
    });
  }

  @override
  void dispose() {
    _tab.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(
      builder: (context, provider, _) {
        final showGate = provider.activeCategorySlug == null;

        return Scaffold(
          backgroundColor: const Color(0xFFF2F5FA),
          body: AnimatedSwitcher(
            duration: const Duration(milliseconds: 350),
            switchInCurve: Curves.easeOut,
            switchOutCurve: Curves.easeIn,
            transitionBuilder: (child, anim) => FadeTransition(
              opacity: anim,
              child: SlideTransition(
                position: Tween<Offset>(
                  begin: const Offset(0, 0.04),
                  end: Offset.zero,
                ).animate(anim),
                child: child,
              ),
            ),
            child: showGate
                ? const CourseSelectionScreen(key: ValueKey('gate'))
                : NestedScrollView(
                    key: ValueKey(provider.activeCategorySlug ?? 'active'),
                    headerSliverBuilder: (_, innerBoxScrolled) => [
                      _buildSliverHeader(context, provider),
                      SliverToBoxAdapter(
                        child: Column(
                          children: [
                            _buildStatsCard(context, provider),
                            _buildCategoryFilter(provider),
                          ],
                        ),
                      ),
                      SliverPersistentHeader(
                        pinned: true,
                        delegate: _TabBarDelegate(
                          TabBar(
                            controller: _tab,
                            isScrollable: true,
                            labelColor: AppColors.primary,
                            unselectedLabelColor: Colors.grey,
                            indicatorColor: AppColors.primary,
                            indicatorSize: TabBarIndicatorSize.label,
                            labelStyle: const TextStyle(fontWeight: FontWeight.w800, fontSize: 13),
                            unselectedLabelStyle: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13),
                            tabs: [
                              Tab(text: Localizations.localeOf(context).languageCode == 'ne' ? 'ट्यूटोरियल' : 'Tutorials'),
                              Tab(text: Localizations.localeOf(context).languageCode == 'ne' ? 'नमूना परीक्षा' : 'Mock Tests'),
                              Tab(text: Localizations.localeOf(context).languageCode == 'ne' ? 'प्रगति' : 'Progress'),
                              Tab(text: Localizations.localeOf(context).languageCode == 'ne' ? 'बचत' : 'Saved'),
                            ],
                          ),
                        ),
                      ),
                    ],
                    body: TabBarView(
                      controller: _tab,
                      children: [
                        _buildCoursesTab(context, provider),
                        _buildQuizzesTab(context, provider),
                        _buildProgressView(context, provider),
                        _buildBookmarksView(context),
                      ],
                    ),
                  ),
          ),
        );
      },
    );
  }

  // ── Progress view ─────────────────────────────────────────────────────────
  Widget _buildProgressView(BuildContext context, LearningProvider provider) {
    final isNepali = Localizations.localeOf(context).languageCode == 'ne';
    final progress = provider.progress;
    final testsCompleted = progress['tests_completed'] ?? 0;
    final totalTests = progress['total_tests'] ?? 20;
    final chaptersRead = provider.chapters.where((c) => c.isRead).length;
    final totalChapters = provider.chapters.length;
    final progressVal =
        totalTests > 0 ? (testsCompleted / totalTests).clamp(0.0, 1.0) : 0.0;
    final chaptersVal =
        totalChapters > 0 ? (chaptersRead / totalChapters).clamp(0.0, 1.0) : 0.0;

    return ListView(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
      children: [
        // Overall progress card
        Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            gradient: const LinearGradient(
              colors: [Color(0xFF1565C0), Color(0xFF1976D2)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                color: const Color(0xFF1565C0).withValues(alpha: 0.35),
                blurRadius: 16,
                offset: const Offset(0, 6),
              ),
            ],
          ),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            // Streak chip
            if (provider.streakDays > 0) ...[
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.18),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Row(mainAxisSize: MainAxisSize.min, children: [
                  const Text('🔥', style: TextStyle(fontSize: 14)),
                  const SizedBox(width: 5),
                  Text(
                    isNepali
                        ? '${provider.streakDays} दिनको streak'
                        : '${provider.streakDays}-day streak',
                    style: const TextStyle(
                        color: Colors.white,
                        fontSize: 12,
                        fontWeight: FontWeight.w700),
                  ),
                ]),
              ),
              const SizedBox(height: 10),
            ],
            Text(
              isNepali ? 'समग्र प्रगति' : 'Overall Progress',
              style: const TextStyle(
                  color: Colors.white70, fontSize: 12, fontWeight: FontWeight.w600),
            ),
            const SizedBox(height: 4),
            Text(
              isNepali ? 'राम्रो काम गर्दै हुनुहुन्छ!' : 'Great work!',
              style: const TextStyle(
                  color: Colors.white,
                  fontSize: 22,
                  fontWeight: FontWeight.w900),
            ),
            const SizedBox(height: 16),
            _ProgressRow(
              label: isNepali ? 'ट्यूटोरियलहरू' : 'Tutorials',
              value: chaptersVal,
              current: chaptersRead,
              total: totalChapters,
              color: Colors.white,
            ),
            const SizedBox(height: 10),
            _ProgressRow(
              label: isNepali ? 'नमूना परीक्षाहरू' : 'Mock Tests',
              value: progressVal,
              current: testsCompleted,
              total: totalTests,
              color: const Color(0xFFFFD54F),
            ),
          ]),
        ),
        const SizedBox(height: 20),
        // Subject-wise breakdown
        Text(
          isNepali ? 'विषयअनुसार' : 'By Subject',
          style: const TextStyle(
              fontSize: 15, fontWeight: FontWeight.w800, color: Color(0xFF1A2B4A)),
        ),
        const SizedBox(height: 12),
        if (provider.categories.isEmpty)
          const Center(child: CircularProgressIndicator())
        else
          ...provider.categories.map((cat) {
            final isNepCat = isNepali && cat.titleNp != null;
            return _SubjectProgressCard(
              title: isNepCat ? cat.titleNp! : cat.title,
              chapters: cat.chapterCount,
              color: AppColors.primary,
            );
          }),
        const SizedBox(height: 24),
        // ── Test History ────────────────────────────────────────────────────
        Row(children: [
          Container(
              width: 4, height: 16,
              decoration: BoxDecoration(
                  color: AppColors.primary,
                  borderRadius: BorderRadius.circular(2))),
          const SizedBox(width: 8),
          Text(
            isNepali ? 'परीक्षा इतिहास' : 'Test History',
            style: const TextStyle(
                fontSize: 15, fontWeight: FontWeight.w800, color: Color(0xFF1A2B4A)),
          ),
        ]),
        const SizedBox(height: 12),
        if (provider.isLoadingHistory)
          const Padding(
            padding: EdgeInsets.symmetric(vertical: 24),
            child: Center(child: CircularProgressIndicator()),
          )
        else if (provider.quizHistory.isEmpty)
          Container(
            padding: const EdgeInsets.symmetric(vertical: 32),
            child: Column(children: [
              Icon(Icons.history_edu_rounded,
                  size: 48, color: Colors.grey.shade300),
              const SizedBox(height: 12),
              Text(
                isNepali
                    ? 'अहिलेसम्म कुनै परीक्षा दिइएको छैन'
                    : 'No tests taken yet',
                style: const TextStyle(
                    color: Color(0xFF8A9BB0),
                    fontSize: 13,
                    fontWeight: FontWeight.w500),
              ),
            ]),
          )
        else
          ...provider.quizHistory.map((h) =>
              _HistoryCard(history: h, isNepali: isNepali)),
      ],
    );
  }

  // ── Bookmarks view ────────────────────────────────────────────────────────
  Widget _buildBookmarksView(BuildContext context) {
    final isNepali = Localizations.localeOf(context).languageCode == 'ne';
    final provider = context.read<LearningProvider>();
    final saved = provider.bookmarks;

    return ListView(
      padding: const EdgeInsets.fromLTRB(16, 20, 16, 100),
      children: [
        Row(children: [
          const Icon(Icons.bookmark_rounded, color: AppColors.primary, size: 20),
          const SizedBox(width: 8),
          Text(
            isNepali ? 'सेभ गरिएका ट्यूटोरियलहरू' : 'Saved Tutorials',
            style: const TextStyle(
                fontSize: 15, fontWeight: FontWeight.w800, color: Color(0xFF1A2B4A)),
          ),
        ]),
        const SizedBox(height: 14),
        if (provider.isLoadingBookmarks)
          const Center(child: Padding(padding: EdgeInsets.all(40), child: CircularProgressIndicator()))
        else if (saved.isEmpty)
          Center(
            child: Padding(
              padding: const EdgeInsets.only(top: 60),
              child: Column(children: [
                Icon(Icons.bookmark_border_rounded,
                    size: 56, color: Colors.grey.shade300),
                const SizedBox(height: 12),
                Text(
                  isNepali
                      ? 'अहिलेसम्म केही सेभ गरिएको छैन'
                      : 'Nothing saved yet',
                  style: const TextStyle(
                      color: Color(0xFF8A9BB0),
                      fontSize: 14,
                      fontWeight: FontWeight.w600),
                ),
              ]),
            ),
          )
        else
          ...saved.map((b) {
            final title = isNepali 
              ? (b['title_np'] ?? b['title'] ?? 'Unknown') 
              : (b['title'] ?? 'Unknown');
            final type = b['type']?.toString().toUpperCase() ?? 'CHAPTER';
            
            return Container(
              margin: const EdgeInsets.only(bottom: 10),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(14),
                boxShadow: const [AppColors.thinShadow],
              ),
              child: ListTile(
                leading: Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(color: AppColors.primary.withValues(alpha: 0.1), shape: BoxShape.circle),
                  child: const Icon(Icons.bookmark_rounded, color: AppColors.primary, size: 20),
                ),
                title: Text(title.toString(), style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 13.5)),
                subtitle: Text(type, style: const TextStyle(fontSize: 11, color: Colors.grey)),
                trailing: const Icon(Icons.chevron_right_rounded, color: Colors.grey),
                onTap: () {
                  final type = b['type']?.toString() ?? 'chapter';
                  if (type == 'chapter') {
                    // Build a minimal LearningChapter from bookmark data to navigate
                    final chapter = LearningChapter(
                      id: b['content_id']?.toString() ?? b['id']?.toString() ?? '',
                      title: (b['title'] ?? 'Chapter').toString(),
                      titleNp: b['title_np']?.toString(),
                      categorySlug: b['category_slug']?.toString(),
                    );
                    Navigator.push(context,
                        MaterialPageRoute(
                            builder: (_) => ChapterDetailScreen(chapter: chapter)));
                  } else {
                    Navigator.push(context,
                        MaterialPageRoute(
                            builder: (_) => const MockTestScreen()));
                  }
                },
              ),
            );
          }),
      ],
    );
  }

  // ── Sliver header ─────────────────────────────────────────────────────────
  Widget _buildSliverHeader(BuildContext context, LearningProvider provider) {
    final active = provider.activeCategory;
    final isNe = Localizations.localeOf(context).languageCode == 'ne';
    final title = isNe 
        ? (active?.titleNp ?? active?.title ?? 'ट्यूटोरियल') 
        : (active?.title ?? 'Tutorials');
        
    return SliverAppBar(
      pinned: true,
      elevation: 0,
      backgroundColor: Colors.white,
      foregroundColor: const Color(0xFF1A2B4A),
      leading: IconButton(
        icon: const Icon(Icons.arrow_back_ios_new_rounded, size: 20),
        onPressed: () => provider.setActiveCategory(null),
      ),
      title: Text(
        title,
        style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 18),
      ),
      centerTitle: true,
    );
  }

  // ── Floating stats card ───────────────────────────────────────────────────
  Widget _buildStatsCard(BuildContext context, LearningProvider provider) {
    final active = provider.activeCategory;
    final totalChapters = active != null ? active.chapterCount : provider.categories.fold<int>(0, (sum, c) => sum + c.chapterCount);
    final totalMocks = active != null ? active.mockTestCount : provider.categories.fold<int>(0, (sum, c) => sum + c.mockTestCount);
    final totalSubjects = active != null ? 1 : provider.categories.length;

    return Container(
      margin: const EdgeInsets.fromLTRB(16, 12, 16, 0),
      padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 4),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
              color: Colors.black.withValues(alpha: 0.08),
              blurRadius: 16,
              offset: const Offset(0, 4)),
        ],
      ),
      child: Row(
        children: [
          _statCell(Icons.menu_book_rounded, '500+', context.l10n.questions, AppColors.primary),
          _vDivider(),
          _statCell(Icons.play_circle_rounded, '$totalChapters', context.l10n.tutorials, const Color(0xFFF57F17)),
          _vDivider(),
          _statCell(Icons.assignment_rounded, '$totalMocks', context.l10n.mockTests, AppColors.secondary),
          _vDivider(),
          _statCell(Icons.subject_rounded, '$totalSubjects', context.l10n.subjects, const Color(0xFF7B1FA2)),
        ],
      ),
    );
  }

  Widget _statCell(IconData icon, String val, String label, Color color) {
    return Expanded(
      child: Column(mainAxisSize: MainAxisSize.min, children: [
        Container(
          width: 34, height: 34,
          decoration: BoxDecoration(
            color: color.withValues(alpha: 0.10),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, color: color, size: 18),
        ),
        const SizedBox(height: 5),
        Text(val,
            style: TextStyle(
                color: color, fontSize: 15, fontWeight: FontWeight.w800)),
        Text(label,
            style: const TextStyle(
                color: Color(0xFF8A96A3), fontSize: 10, fontWeight: FontWeight.w500)),
      ]),
    );
  }

  Widget _vDivider() => Container(
        width: 1, height: 38, color: const Color(0xFFEEF2F8));

  // ── Category filter ───────────────────────────────────────────────────────
  Widget _buildCategoryFilter(LearningProvider provider) {
    if (provider.isLoadingCategories) {
      return const SizedBox(height: 50, child: Center(child: CircularProgressIndicator()));
    }

    final isNepali = Localizations.localeOf(context).languageCode == 'ne';
    final cats = [
      _Cat(isNepali ? 'सबै' : 'All', 'all', Icons.grid_view_rounded),
      ...provider.categories.map((c) => _Cat(
          isNepali ? (c.titleNp ?? c.title) : c.title,
          c.slug,
          Icons.category_rounded))
    ];

    return Container(
      height: 50,
      color: Colors.transparent,
      margin: const EdgeInsets.only(top: 12),
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
        itemCount: cats.length,
        itemBuilder: (_, i) {
          final cat = cats[i];
          final active = (provider.activeCategorySlug == null && cat.slug == 'all') || 
                         (provider.activeCategorySlug == cat.slug);
          return GestureDetector(
            onTap: () {
              final slug = cat.slug == 'all' ? null : cat.slug;
              provider.setActiveCategory(slug);
            },
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              margin: const EdgeInsets.only(right: 8),
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 5),
              decoration: BoxDecoration(
                color: active ? AppColors.primary : const Color(0xFFF0F4FA),
                borderRadius: BorderRadius.circular(20),
              ),
              child: Row(mainAxisSize: MainAxisSize.min, children: [
                Icon(cat.icon,
                    color: active ? Colors.white : const Color(0xFF7A8898),
                    size: 14),
                const SizedBox(width: 5),
                Text(cat.label,
                    style: TextStyle(
                        color: active ? Colors.white : const Color(0xFF5A6A80),
                        fontSize: 12,
                        fontWeight: FontWeight.w700)),
              ]),
            ),
          );
        },
      ),
    );
  }

  // ── Courses tab ───────────────────────────────────────────────────────────
  Widget _buildCoursesTab(BuildContext context, LearningProvider provider) {
    final isNepali = Localizations.localeOf(context).languageCode == 'ne';

    if (provider.isLoadingChapters) {
      return const Center(child: CircularProgressIndicator());
    }

    if (provider.chapters.isEmpty) {
      return const Center(child: Text('No tutorials found for this category.'));
    }

    var allChapters = provider.chapters;
    if (_searchQuery.isNotEmpty) {
      final query = _searchQuery.toLowerCase();
      allChapters = allChapters.where((c) {
        final titleEn = c.title.toLowerCase();
        final titleNp = (c.titleNp ?? '').toLowerCase();
        return titleEn.contains(query) || titleNp.contains(query);
      }).toList();
    }

    final featured = allChapters.take(2).toList();

    return ListView(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
      children: [
        Padding(
          padding: const EdgeInsets.only(bottom: 16),
          child: TextField(
            onChanged: (val) => setState(() => _searchQuery = val),
            decoration: InputDecoration(
              hintText: isNepali ? 'अध्याय खोज्नुहोस्...' : 'Search tutorials...',
              prefixIcon: const Icon(Icons.search_rounded, color: AppColors.primary),
              filled: true,
              fillColor: Colors.white,
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(14),
                borderSide: BorderSide.none,
              ),
              contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
            ),
          ),
        ),
        
        // Quick Access
        _SectionHeader(isNepali ? 'द्रुत पहुँच' : 'Quick Access'),
        const SizedBox(height: 10),
        Row(
          children: [
            Expanded(child: _QuickAccessButton(icon: Icons.fitness_center_rounded, label: isNepali ? 'अभ्यास' : 'Practice', color: const Color(0xFF4A148C), onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const PracticeScreen())))),
            const SizedBox(width: 10),
            Expanded(child: _QuickAccessButton(icon: Icons.style_rounded, label: isNepali ? 'फ्ल्यासकार्ड' : 'Flashcards', color: const Color(0xFFBF360C), onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const FlashcardsScreen())))),
            const SizedBox(width: 10),
            Expanded(child: _QuickAccessButton(icon: Icons.play_circle_fill_rounded, label: isNepali ? 'भिडियो' : 'Video', color: const Color(0xFF00695C), onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const VideoLecturesScreen())))),
          ],
        ),
        const SizedBox(height: 20),

        _SectionHeader(isNepali ? 'सिकाइ जारी राख्नुहोस्' : 'Continue Learning', onTap: () {}),
        const SizedBox(height: 10),
        ...featured.map((c) => _FeaturedCourseCard(
            chapter: c,
            isNepali: isNepali,
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => ChapterDetailScreen(chapter: c))))),
        const SizedBox(height: 20),
        _SectionHeader(isNepali ? 'सबै ट्यूटोरियलहरू' : 'All Tutorials', onTap: () {}),
        const SizedBox(height: 10),
        GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: 4,
            childAspectRatio: 0.72,
            crossAxisSpacing: 10,
            mainAxisSpacing: 10,
          ),
          itemCount: allChapters.length,
          itemBuilder: (_, i) => _CourseGridCard(
            chapter: allChapters[i],
            isNepali: isNepali,
            onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => ChapterDetailScreen(chapter: allChapters[i])))
          ),
        ),
      ],
    );
  }

  // ── Quizzes / Mock Tests tab ──────────────────────────────────────────────
  Widget _buildQuizzesTab(BuildContext context, LearningProvider provider) {
    final isNepali = Localizations.localeOf(context).languageCode == 'ne';

    if (provider.isLoadingMockTests) {
      return const Center(child: CircularProgressIndicator());
    }

    final mockTests = provider.mockTests;
    final progress = provider.progress;
    final testsCompleted = progress['tests_completed'] ?? 0;
    final totalTests = progress['total_tests'] ?? 20; // fallback
    final progressVal = totalTests > 0 ? (testsCompleted / totalTests).clamp(0.0, 1.0) : 0.0;

    return ListView(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
      children: [
        // Progress card
        Container(
          padding: const EdgeInsets.all(18),
          margin: const EdgeInsets.only(bottom: 20),
          decoration: BoxDecoration(
            gradient: const LinearGradient(
              colors: [Color(0xFF1565C0), Color(0xFF1976D2)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.circular(18),
          ),
          child: Row(children: [
            Expanded(
                child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                  Text(isNepali ? 'तपाईंको प्रगति' : 'Your Progress',
                      style: const TextStyle(color: Colors.white70, fontSize: 12)),
                  const SizedBox(height: 4),
                  Text(isNepali ? 'यसरी नै अगाडि बढ्नुहोस्!' : 'Keep it up!',
                      style: const TextStyle(
                          color: Colors.white,
                          fontSize: 18,
                          fontWeight: FontWeight.w800)),
                  const SizedBox(height: 10),
                  Row(children: [
                    Expanded(
                        child: ClipRRect(
                      borderRadius: BorderRadius.circular(4),
                      child: LinearProgressIndicator(
                        value: progressVal,
                        minHeight: 6,
                        backgroundColor: Colors.white24,
                        valueColor: const AlwaysStoppedAnimation(Colors.white),
                      ),
                    )),
                    const SizedBox(width: 10),
                    Text('${(progressVal * 100).toInt()}%',
                        style: const TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.w700,
                            fontSize: 13)),
                  ]),
                  const SizedBox(height: 6),
                  Text(isNepali ? '$totalTests मध्ये $testsCompleted नमूना परीक्षा पुरा भयो' : '$testsCompleted of $totalTests mock tests completed',
                      style: const TextStyle(color: Colors.white60, fontSize: 11)),
                ])),
            const SizedBox(width: 16),
            Container(
              width: 60,
              height: 60,
              decoration: BoxDecoration(
                color: Colors.white.withValues(alpha: 0.15),
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.emoji_events_rounded,
                  color: Colors.white, size: 32),
            ),
          ]),
        ),
        
        // Competitions Section
        if (provider.isLoadingCompetitions)
          const Center(child: CircularProgressIndicator())
        else if (provider.competitions.isNotEmpty) ...[
          _SectionHeader(isNepali ? 'प्रतियोगिताहरू' : 'Live Competitions', onTap: null),
          const SizedBox(height: 10),
          ...provider.competitions.map((comp) {
            final title = isNepali ? (comp.titleNp ?? comp.title) : comp.title;
            return Container(
              margin: const EdgeInsets.only(bottom: 12),
              decoration: BoxDecoration(
                gradient: const LinearGradient(colors: [Color(0xFFF57F17), Color(0xFFF9A825)]),
                borderRadius: BorderRadius.circular(16),
                boxShadow: [BoxShadow(color: const Color(0xFFF57F17).withValues(alpha: 0.3), blurRadius: 8, offset: const Offset(0, 4))],
              ),
              child: ListTile(
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                leading: const Icon(Icons.emoji_events_rounded, color: Colors.white, size: 32),
                title: Text(title, style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 15)),
                subtitle: Text(comp.status?.toUpperCase() ?? 'UPCOMING', style: const TextStyle(color: Colors.white70, fontSize: 11, fontWeight: FontWeight.w700)),
                trailing: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
                  child: Text(isNepali ? 'सुरु' : 'Join', style: const TextStyle(color: Color(0xFFF57F17), fontWeight: FontWeight.w800, fontSize: 12)),
                ),
                onTap: () => Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (_) =>
                            const MockTestScreen(isCompetition: true))),
              ),
            );
          }),
          const SizedBox(height: 20),
        ],

        if (mockTests.isEmpty)
          const Center(child: Text('No mock tests available.'))
        else ...[
          _SectionHeader(isNepali ? 'उपलब्ध परीक्षाहरू' : 'Available Tests', onTap: null),
          const SizedBox(height: 10),
          ...mockTests.map((q) => _QuizCard(
              quiz: q,
              isNepali: isNepali,
              onTap: () => Navigator.push(
                  context,
                  MaterialPageRoute(
                      builder: (_) => MockTestScreen(test: q))))),
        ]
      ],
    );
  }
}

// ─── Section header with View all ────────────────────────────────────────────
class _SectionHeader extends StatelessWidget {
  final String title;
  final VoidCallback? onTap;
  const _SectionHeader(this.title, {this.onTap});

  @override
  Widget build(BuildContext context) {
    return Row(children: [
      Container(
          width: 4,
          height: 16,
          decoration: BoxDecoration(
              color: AppColors.primary,
              borderRadius: BorderRadius.circular(2))),
      const SizedBox(width: 8),
      Text(title,
          style: const TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.w800,
              color: Color(0xFF1A2B4A))),
      const Spacer(),
      if (onTap != null)
        GestureDetector(
          onTap: onTap,
          child: const Row(children: [
            Text('View all',
                style: TextStyle(
                    color: AppColors.primary,
                    fontSize: 12,
                    fontWeight: FontWeight.w600)),
            Icon(Icons.chevron_right_rounded,
                color: AppColors.primary, size: 16),
          ]),
        ),
    ]);
  }
}

// ─── Featured course card ─────────────────────────────────────────────────────
class _FeaturedCourseCard extends StatelessWidget {
  final LearningChapter chapter;
  final bool isNepali;
  final VoidCallback onTap;
  const _FeaturedCourseCard({required this.chapter, required this.isNepali, required this.onTap});

  @override
  Widget build(BuildContext context) {
    final title = isNepali ? (chapter.titleNp ?? chapter.title) : chapter.title;
    final color = const Color(0xFF1565C0);
    final progress = chapter.isRead ? 1.0 : 0.0;
    
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(18),
          boxShadow: [
            BoxShadow(
                color: Colors.black.withValues(alpha: 0.06),
                blurRadius: 12,
                offset: const Offset(0, 4)),
          ],
        ),
        child: Row(children: [
          // Icon
          Container(
            width: 52,
            height: 52,
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.12),
              borderRadius: BorderRadius.circular(14),
            ),
            child: Icon(Icons.menu_book_rounded, color: color, size: 26),
          ),
          const SizedBox(width: 14),
          Expanded(
              child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                Row(children: [
                  Expanded(
                    child: Text(title,
                        style: const TextStyle(
                            fontSize: 13.5,
                            fontWeight: FontWeight.w800,
                            color: Color(0xFF1A2B4A)),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis),
                  ),
                  const Icon(Icons.more_vert_rounded,
                      color: Color(0xFFCCD5E0), size: 18),
                ]),
                const SizedBox(height: 3),
                Text('${chapter.readTimeMinutes} mins',
                    style: const TextStyle(
                        color: Color(0xFF8A9BB0), fontSize: 11.5),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis),
                const SizedBox(height: 8),
                Row(children: [
                  Expanded(
                      child: ClipRRect(
                    borderRadius: BorderRadius.circular(4),
                    child: LinearProgressIndicator(
                      value: progress,
                      minHeight: 5,
                      backgroundColor: color.withValues(alpha: 0.12),
                      valueColor: AlwaysStoppedAnimation(color),
                    ),
                  )),
                  const SizedBox(width: 8),
                  Text('${(progress * 100).toInt()}%',
                      style: TextStyle(
                          color: color,
                          fontSize: 11,
                          fontWeight: FontWeight.w700)),
                ]),
              ])),
          const SizedBox(width: 10),
          Container(
            width: 36,
            height: 36,
            decoration:
                BoxDecoration(color: color, shape: BoxShape.circle),
            child: const Icon(Icons.play_arrow_rounded,
                color: Colors.white, size: 20),
          ),
        ]),
      ),
    );
  }
}

// ─── Course grid card (4-column, icon + title + lessons + color bar) ──────────
class _CourseGridCard extends StatelessWidget {
  final LearningChapter chapter;
  final bool isNepali;
  final VoidCallback onTap;
  const _CourseGridCard({required this.chapter, required this.isNepali, required this.onTap});

  @override
  Widget build(BuildContext context) {
    final title = isNepali ? (chapter.titleNp ?? chapter.title) : chapter.title;
    final color = const Color(0xFF4A148C);
    final progress = chapter.isRead ? 1.0 : 0.0;
    
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(14),
          boxShadow: const [AppColors.cardShadow],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 38,
              height: 38,
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.12),
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(Icons.play_circle_fill_rounded, color: color, size: 20),
            ),
            const Spacer(),
            Text(title,
                style: const TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                    color: Color(0xFF1A2B4A)),
                maxLines: 2,
                overflow: TextOverflow.ellipsis),
            const SizedBox(height: 3),
            Text('${chapter.readTimeMinutes} min',
                style: TextStyle(
                    fontSize: 9.5,
                    color: color.withValues(alpha: 0.8),
                    fontWeight: FontWeight.w600)),
            const SizedBox(height: 6),
            ClipRRect(
              borderRadius: BorderRadius.circular(3),
              child: LinearProgressIndicator(
                value: progress,
                minHeight: 3,
                backgroundColor: color.withValues(alpha: 0.12),
                valueColor: AlwaysStoppedAnimation(color),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ─── Quiz card ────────────────────────────────────────────────────────────────
class _QuizCard extends StatelessWidget {
  final MockTest quiz;
  final bool isNepali;
  final VoidCallback onTap;
  const _QuizCard({required this.quiz, required this.isNepali, required this.onTap});

  @override
  Widget build(BuildContext context) {
    final title = isNepali ? (quiz.titleNp ?? quiz.title) : quiz.title;
    final color = quiz.isFeatured ? AppColors.secondary : AppColors.primary;
    final subtitle = '${quiz.totalQuestions} questions · ${quiz.durationMinutes} min';
    final action = isNepali ? 'सुरु गर्नुहोस्' : 'Start';
    
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: const [AppColors.cardShadow],
        ),
        child: Row(children: [
          Container(
            width: 46,
            height: 46,
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.12),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(Icons.assignment_rounded, color: color, size: 22),
          ),
          const SizedBox(width: 12),
          Expanded(
              child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                Text(title,
                    style: const TextStyle(
                        fontSize: 13.5,
                        fontWeight: FontWeight.w700,
                        color: Color(0xFF1A2B4A))),
                const SizedBox(height: 3),
                Text(subtitle,
                    style: const TextStyle(
                        color: Color(0xFF8A9BB0), fontSize: 11.5)),
              ])),
          Container(
            padding:
                const EdgeInsets.symmetric(horizontal: 14, vertical: 7),
            decoration: BoxDecoration(
              color: AppColors.primary,
              borderRadius: BorderRadius.circular(20),
            ),
            child: Text(action,
                style: const TextStyle(
                    color: Colors.white,
                    fontSize: 12,
                    fontWeight: FontWeight.w700)),
          ),
        ]),
      ),
    );
  }
}

// ─── Tutorials SliverPersistentHeader delegate ────────────────────────────────

// ─── Test history card ────────────────────────────────────────────────────────
class _HistoryCard extends StatelessWidget {
  final Map<String, dynamic> history;
  final bool isNepali;
  const _HistoryCard({required this.history, required this.isNepali});

  @override
  Widget build(BuildContext context) {
    final score = (history['score'] as num?)?.toInt() ?? 0;
    final total = (history['total_questions'] as num?)?.toInt() ?? 10;
    final pct   = total > 0 ? (score / total * 100).round() : 0;
    final passed = pct >= 60;
    final category = history['category_slug']?.toString() ?? '';
    final timeTaken = (history['time_taken_seconds'] as num?)?.toInt() ?? 0;
    final rawDate = history['created_at']?.toString() ?? '';
    final dateLabel = _formatDate(rawDate, isNepali);
    final timeLabel = timeTaken > 0
        ? '${timeTaken ~/ 60}m ${timeTaken % 60}s'
        : '';

    final barColor = passed ? AppColors.success : AppColors.danger;

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: const [AppColors.cardShadow],
      ),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        // Top row: category + date
        Row(children: [
          Expanded(
            child: Text(
              category.isNotEmpty
                  ? _slugToLabel(category)
                  : (isNepali ? 'नमूना परीक्षा' : 'Mock Test'),
              style: const TextStyle(
                  fontSize: 13.5,
                  fontWeight: FontWeight.w800,
                  color: Color(0xFF1A2B4A)),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ),
          // Pass / Fail badge
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
            decoration: BoxDecoration(
              color: barColor.withValues(alpha: 0.12),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Text(
              passed
                  ? (isNepali ? 'उत्तीर्ण' : 'PASS')
                  : (isNepali ? 'अनुत्तीर्ण' : 'FAIL'),
              style: TextStyle(
                  color: barColor,
                  fontSize: 11,
                  fontWeight: FontWeight.w800),
            ),
          ),
        ]),
        const SizedBox(height: 10),
        // Score progress bar
        Row(children: [
          Expanded(
            child: ClipRRect(
              borderRadius: BorderRadius.circular(4),
              child: LinearProgressIndicator(
                value: pct / 100,
                minHeight: 6,
                backgroundColor: barColor.withValues(alpha: 0.12),
                valueColor: AlwaysStoppedAnimation(barColor),
              ),
            ),
          ),
          const SizedBox(width: 10),
          Text(
            '$pct%',
            style: TextStyle(
                color: barColor,
                fontSize: 13,
                fontWeight: FontWeight.w800),
          ),
        ]),
        const SizedBox(height: 8),
        // Bottom row: score fraction + time + date
        Row(children: [
          _HistMeta(Icons.quiz_rounded, '$score/$total',
              isNepali ? 'अङ्क' : 'Score', AppColors.primary),
          if (timeLabel.isNotEmpty) ...[
            const SizedBox(width: 14),
            _HistMeta(Icons.timer_outlined, timeLabel,
                isNepali ? 'समय' : 'Time', const Color(0xFFF57F17)),
          ],
          const Spacer(),
          Text(
            dateLabel,
            style: const TextStyle(
                color: Color(0xFFADB9C9),
                fontSize: 11,
                fontWeight: FontWeight.w500),
          ),
        ]),
      ]),
    );
  }

  String _formatDate(String raw, bool isNe) {
    if (raw.isEmpty) return isNe ? 'हालसालै' : 'Recently';
    try {
      final dt = DateTime.parse(raw).toLocal();
      final months = isNe
          ? ['जन', 'फेब', 'मार्च', 'अप्र', 'मई', 'जुन',
             'जुल', 'अग', 'सेप', 'अक्ट', 'नोभ', 'डिस']
          : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
             'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      return '${dt.day} ${months[dt.month - 1]} ${dt.year}';
    } catch (_) {
      return raw.length > 10 ? raw.substring(0, 10) : raw;
    }
  }

  String _slugToLabel(String slug) {
    return slug
        .replaceAll('-', ' ')
        .replaceAll('_', ' ')
        .split(' ')
        .map((w) => w.isEmpty ? '' : '${w[0].toUpperCase()}${w.substring(1)}')
        .join(' ');
  }
}

class _HistMeta extends StatelessWidget {
  final IconData icon;
  final String value;
  final String label;
  final Color color;
  const _HistMeta(this.icon, this.value, this.label, this.color);

  @override
  Widget build(BuildContext context) {
    return Row(mainAxisSize: MainAxisSize.min, children: [
      Icon(icon, size: 13, color: color),
      const SizedBox(width: 3),
      Text(
        '$value $label',
        style: TextStyle(
            color: color, fontSize: 11.5, fontWeight: FontWeight.w600),
      ),
    ]);
  }
}

// ─── Data models ──────────────────────────────────────────────────────────────
class _Cat {
  final String label;
  final String slug;
  final IconData icon;
  const _Cat(this.label, this.slug, this.icon);
}

// ─── Progress row (used in the Progress view) ─────────────────────────────────
class _ProgressRow extends StatelessWidget {
  final String label;
  final double value;
  final int current;
  final int total;
  final Color color;

  const _ProgressRow({
    required this.label,
    required this.value,
    required this.current,
    required this.total,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Row(children: [
        Expanded(
          child: Text(label,
              style: TextStyle(
                  color: color.withValues(alpha: 0.85),
                  fontSize: 12,
                  fontWeight: FontWeight.w600)),
        ),
        Text('$current / $total',
            style: TextStyle(
                color: color,
                fontSize: 12,
                fontWeight: FontWeight.w700)),
      ]),
      const SizedBox(height: 6),
      ClipRRect(
        borderRadius: BorderRadius.circular(4),
        child: LinearProgressIndicator(
          value: value,
          minHeight: 6,
          backgroundColor: color.withValues(alpha: 0.2),
          valueColor: AlwaysStoppedAnimation(color),
        ),
      ),
    ]);
  }
}

// ─── Subject progress card ────────────────────────────────────────────────────
class _SubjectProgressCard extends StatelessWidget {
  final String title;
  final int chapters;
  final Color color;

  const _SubjectProgressCard({
    required this.title,
    required this.chapters,
    required this.color,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        boxShadow: const [AppColors.cardShadow],
      ),
      child: Row(children: [
        Container(
          width: 40,
          height: 40,
          decoration: BoxDecoration(
            color: color.withValues(alpha: 0.10),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(Icons.subject_rounded, color: color, size: 20),
        ),
        const SizedBox(width: 12),
        Expanded(
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(title,
                style: const TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                    color: Color(0xFF1A2B4A))),
            const SizedBox(height: 2),
            Text('$chapters chapters',
                style: const TextStyle(
                    fontSize: 11, color: Color(0xFF8A9BB0))),
          ]),
        ),
        ClipRRect(
          borderRadius: BorderRadius.circular(4),
          child: SizedBox(
            width: 60,
            child: LinearProgressIndicator(
              value: 0.0,
              minHeight: 5,
              backgroundColor: color.withValues(alpha: 0.12),
              valueColor: AlwaysStoppedAnimation(color),
            ),
          ),
        ),
      ]),
    );
  }
}

class _TabBarDelegate extends SliverPersistentHeaderDelegate {
  final TabBar tabBar;
  _TabBarDelegate(this.tabBar);

  @override
  double get minExtent => tabBar.preferredSize.height;
  @override
  double get maxExtent => tabBar.preferredSize.height;

  @override
  Widget build(BuildContext context, double shrinkOffset, bool overlapsContent) {
    return Container(
      color: const Color(0xFFF2F5FA), // Match scaffold bg
      child: tabBar,
    );
  }

  @override
  bool shouldRebuild(_TabBarDelegate oldDelegate) => false;
}

class _QuickAccessButton extends StatelessWidget {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;

  const _QuickAccessButton({required this.icon, required this.label, required this.color, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14),
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.08),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: color.withValues(alpha: 0.2)),
        ),
        child: Column(
          children: [
            Icon(icon, color: color, size: 28),
            const SizedBox(height: 6),
            Text(label, style: TextStyle(color: color, fontSize: 12, fontWeight: FontWeight.w700)),
          ],
        ),
      ),
    );
  }
}
