import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../providers/learning_provider.dart';
import 'study_screen.dart';
import 'test_dashboard_screen.dart';
import 'practice_screen.dart';
import 'progress_screen.dart';
import 'discuss_screen.dart';
import 'daily_quiz_screen.dart';
import 'subscription_screen.dart';
import 'video_lectures_screen.dart';

// ─────────────────────────────────────────────────────────────────────────────
// LearningScreen — 5-tab shell (Home / Study / Practice / Tests / Progress)
// ─────────────────────────────────────────────────────────────────────────────
class LearningScreen extends StatefulWidget {
  const LearningScreen({super.key});

  @override
  State<LearningScreen> createState() => _LearningScreenState();
}

class _LearningScreenState extends State<LearningScreen> {
  int _currentIndex = 0;

  static const _tabs = [
    _LearningDashboard(),
    StudyScreen(),
    PracticeScreen(),
    TestDashboardScreen(),
    ProgressScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(index: _currentIndex, children: _tabs),
      bottomNavigationBar: Container(
        decoration: const BoxDecoration(
          color: Colors.white,
          boxShadow: [BoxShadow(color: Color(0x1A000000), blurRadius: 16, offset: Offset(0, -4))],
        ),
        child: SafeArea(
          child: BottomNavigationBar(
            currentIndex: _currentIndex,
            onTap: (i) => setState(() => _currentIndex = i),
            type: BottomNavigationBarType.fixed,
            backgroundColor: Colors.white,
            selectedItemColor: const Color(0xFF302B63),
            unselectedItemColor: AppColors.textLight,
            selectedLabelStyle: const TextStyle(fontWeight: FontWeight.w700, fontSize: 11),
            unselectedLabelStyle: const TextStyle(fontWeight: FontWeight.w500, fontSize: 10),
            elevation: 0,
            items: const [
              BottomNavigationBarItem(icon: Icon(Icons.home_rounded), label: 'Home'),
              BottomNavigationBarItem(icon: Icon(Icons.menu_book_rounded), label: 'Study'),
              BottomNavigationBarItem(icon: Icon(Icons.fitness_center_rounded), label: 'Practice'),
              BottomNavigationBarItem(icon: Icon(Icons.assignment_rounded), label: 'Tests'),
              BottomNavigationBarItem(icon: Icon(Icons.bar_chart_rounded), label: 'Progress'),
            ],
          ),
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Home Dashboard Tab
// ─────────────────────────────────────────────────────────────────────────────
class _LearningDashboard extends StatefulWidget {
  const _LearningDashboard();

  @override
  State<_LearningDashboard> createState() => _LearningDashboardState();
}

class _LearningDashboardState extends State<_LearningDashboard> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => _loadData());
  }

  Future<void> _loadData() async {
    final p = context.read<LearningProvider>();
    await Future.wait([
      p.loadDailyQuiz(),
      p.loadCategories(),
      p.initStreak(),
      p.loadAchievements(),
    ]);
    if (p.categories.isNotEmpty) {
      p.loadAnalytics(categorySlug: p.categories.first.slug);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (context, p, _) {
      return Scaffold(
        backgroundColor: const Color(0xFFF5F7FA),
        body: RefreshIndicator(
          onRefresh: _loadData,
          child: CustomScrollView(
            physics: const BouncingScrollPhysics(),
            slivers: [
              // ── Pinned Gradient Header ────────────────────────────────────
              SliverAppBar(
                expandedHeight: 180,
                pinned: true,
                elevation: 0,
                automaticallyImplyLeading: false,
                backgroundColor: const Color(0xFF302B63),
                flexibleSpace: FlexibleSpaceBar(
                  background: Container(
                    decoration: const BoxDecoration(
                      gradient: LinearGradient(
                        colors: [Color(0xFF302B63), Color(0xFF24243E)],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                    ),
                    child: SafeArea(
                      child: Padding(
                        padding: const EdgeInsets.fromLTRB(20, 16, 20, 0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Row(children: [
                                  Container(
                                    padding: const EdgeInsets.all(2),
                                    decoration: const BoxDecoration(color: Colors.white, shape: BoxShape.circle),
                                    child: const CircleAvatar(
                                      radius: 22,
                                      backgroundColor: Color(0xFF302B63),
                                      child: Icon(Icons.person, color: Colors.white, size: 22),
                                    ),
                                  ),
                                  const SizedBox(width: 12),
                                  const Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                                    Text('Hello, Student! 👋', style: TextStyle(color: Colors.white, fontSize: 17, fontWeight: FontWeight.bold)),
                                    Text('Keep learning every day', style: TextStyle(color: Colors.white60, fontSize: 12)),
                                  ]),
                                ]),
                                // Streak pill
                                GestureDetector(
                                  onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ProgressScreen())),
                                  child: Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                    decoration: BoxDecoration(
                                      color: Colors.white.withOpacity(0.15),
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    child: Row(children: [
                                      const Icon(Icons.local_fire_department_rounded, color: Colors.orange, size: 20),
                                      const SizedBox(width: 4),
                                      Text('${p.streakDays}', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 15)),
                                    ]),
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 16),
                            // Quick-stat row
                            Row(children: [
                              _HeaderStat(label: 'Tests', value: '${p.stats?.testsTaken ?? 0}'),
                              const SizedBox(width: 20),
                              _HeaderStat(label: 'Avg Score', value: '${p.stats?.avgScore.toStringAsFixed(0) ?? '0'}%'),
                              const SizedBox(width: 20),
                              _HeaderStat(label: 'Badges', value: '${p.myAchievements.length}'),
                            ]),
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
              ),

              SliverPadding(
                padding: const EdgeInsets.fromLTRB(20, 20, 20, 100),
                sliver: SliverList(delegate: SliverChildListDelegate([

                  // ── Daily Quiz Card ────────────────────────────────────────
                  _DailyQuizBanner(quiz: p.dailyQuiz, isLoading: p.isLoadingDailyQuiz),
                  const SizedBox(height: 24),

                  // ── Quick Access ───────────────────────────────────────────
                  _SectionHeader(title: 'Quick Access', onSeeAll: null),
                  const SizedBox(height: 12),
                  SizedBox(
                    height: 100,
                    child: ListView(
                      scrollDirection: Axis.horizontal,
                      physics: const BouncingScrollPhysics(),
                      children: [
                        _QuickCard(icon: Icons.play_circle_rounded, label: 'Practice', color: const Color(0xFF4A148C),
                          onTap: () {}),
                        _QuickCard(icon: Icons.flash_on_rounded, label: 'Flashcards', color: const Color(0xFF00695C),
                          onTap: () {}),
                        _QuickCard(icon: Icons.ondemand_video_rounded, label: 'Videos', color: const Color(0xFF1565C0),
                          onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const VideoLecturesScreen()))),
                        _QuickCard(icon: Icons.forum_rounded, label: 'Discuss', color: const Color(0xFFB71C1C),
                          onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const DiscussScreen()))),
                        _QuickCard(icon: Icons.workspace_premium_rounded, label: 'Premium', color: const Color(0xFFF57F17),
                          onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const SubscriptionScreen()))),
                      ],
                    ),
                  ),
                  const SizedBox(height: 28),

                  // ── Continue Learning (recommendations) ────────────────────
                  _SectionHeader(title: 'Continue Learning', onSeeAll: () {}),
                  const SizedBox(height: 12),
                  if (p.isLoadingAnalytics)
                    const Center(child: CircularProgressIndicator())
                  else if ((p.recommendations['recommendations'] as List? ?? []).isEmpty)
                    _EmptyCard(message: 'Start reading chapters to get recommendations!')
                  else
                    ...(p.recommendations['recommendations'] as List? ?? [])
                        .take(3)
                        .map((r) => _RecommendationCard(item: r as Map<String, dynamic>))
                        .expand((w) => [w, const SizedBox(height: 10)]),
                  const SizedBox(height: 28),

                  // ── Syllabus Tracker ───────────────────────────────────────
                  _SectionHeader(title: 'My Syllabus', onSeeAll: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ProgressScreen()))),
                  const SizedBox(height: 12),
                  if (p.isLoadingCategories)
                    const SizedBox(height: 80, child: Center(child: CircularProgressIndicator()))
                  else
                    SizedBox(
                      height: 130,
                      child: ListView(
                        scrollDirection: Axis.horizontal,
                        physics: const BouncingScrollPhysics(),
                        children: p.categories.take(5).map((cat) {
                          final chapters = p.syllabus['chapters'] as List? ?? [];
                          final done = chapters.where((c) => c['completed'] == true).length;
                          final total = (p.syllabus['total_chapters'] as int?) ?? (cat.chapterCount > 0 ? cat.chapterCount : 1);
                          final pct = total > 0 ? done / total : 0.0;
                          return _SyllabusChip(
                            title: cat.title,
                            pct: pct.toDouble(),
                            color: _catColor(cat.colorCode),
                          );
                        }).expand((w) => [w, const SizedBox(width: 12)]).toList(),
                      ),
                    ),
                  const SizedBox(height: 28),

                  // ── Weak Areas Alert ───────────────────────────────────────
                  if ((p.weakAreas['weak_areas'] as List? ?? []).isNotEmpty) ...[
                    _SectionHeader(title: 'Weak Areas', onSeeAll: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const ProgressScreen()))),
                    const SizedBox(height: 12),
                    ...(p.weakAreas['weak_areas'] as List).take(3).map((w) {
                      final area = w as Map<String, dynamic>;
                      return Padding(
                        padding: const EdgeInsets.only(bottom: 8),
                        child: _WeakAreaTile(area: area),
                      );
                    }),
                    const SizedBox(height: 16),
                  ],
                ])),
              ),
            ],
          ),
        ),
      );
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Reusable sub-widgets
// ─────────────────────────────────────────────────────────────────────────────

class _HeaderStat extends StatelessWidget {
  final String label, value;
  const _HeaderStat({required this.label, required this.value});
  @override
  Widget build(BuildContext context) => Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      Text(value, style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w800)),
      Text(label, style: const TextStyle(color: Colors.white60, fontSize: 11)),
    ],
  );
}

class _SectionHeader extends StatelessWidget {
  final String title;
  final VoidCallback? onSeeAll;
  const _SectionHeader({required this.title, this.onSeeAll});
  @override
  Widget build(BuildContext context) => Row(
    mainAxisAlignment: MainAxisAlignment.spaceBetween,
    children: [
      Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.textDark)),
      if (onSeeAll != null)
        GestureDetector(
          onTap: onSeeAll,
          child: const Text('See All', style: TextStyle(color: Color(0xFF302B63), fontWeight: FontWeight.w600, fontSize: 13)),
        ),
    ],
  );
}

class _DailyQuizBanner extends StatelessWidget {
  final dynamic quiz;
  final bool isLoading;
  const _DailyQuizBanner({this.quiz, required this.isLoading});

  @override
  Widget build(BuildContext context) {
    final answered = quiz?.isAnswered ?? false;
    return GestureDetector(
      onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const DailyQuizScreen())),
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            colors: [Color(0xFFE65100), Color(0xFFBF360C)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          borderRadius: BorderRadius.circular(20),
          boxShadow: [BoxShadow(color: const Color(0xFFE65100).withOpacity(0.3), blurRadius: 16, offset: const Offset(0, 6))],
        ),
        child: Row(children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(color: Colors.white.withOpacity(0.2), shape: BoxShape.circle),
            child: const Icon(Icons.psychology_alt_rounded, color: Colors.white, size: 30),
          ),
          const SizedBox(width: 16),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Text('Daily Challenge', style: TextStyle(color: Colors.white, fontSize: 17, fontWeight: FontWeight.bold)),
            const SizedBox(height: 4),
            Text(
              answered ? '✅ Completed today!' : 'Answer today\'s question to keep your streak!',
              style: TextStyle(color: Colors.white.withOpacity(0.85), fontSize: 12),
            ),
          ])),
          if (!answered)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
              child: const Text('Play', style: TextStyle(color: Color(0xFFBF360C), fontWeight: FontWeight.bold, fontSize: 13)),
            )
          else
            const Icon(Icons.check_circle_rounded, color: Colors.white, size: 32),
        ]),
      ),
    );
  }
}

class _QuickCard extends StatelessWidget {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;
  const _QuickCard({required this.icon, required this.label, required this.color, required this.onTap});

  @override
  Widget build(BuildContext context) => GestureDetector(
    onTap: onTap,
    child: Container(
      width: 80,
      margin: const EdgeInsets.only(right: 12),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), boxShadow: const [AppColors.thinShadow]),
      child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(color: color.withOpacity(0.1), shape: BoxShape.circle),
          child: Icon(icon, color: color, size: 26),
        ),
        const SizedBox(height: 8),
        Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold, color: AppColors.textDark), textAlign: TextAlign.center),
      ]),
    ),
  );
}

class _RecommendationCard extends StatelessWidget {
  final Map<String, dynamic> item;
  const _RecommendationCard({required this.item});

  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.all(14),
    decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(14), boxShadow: const [AppColors.thinShadow]),
    child: Row(children: [
      Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(color: const Color(0xFF302B63).withOpacity(0.08), borderRadius: BorderRadius.circular(10)),
        child: const Icon(Icons.menu_book_rounded, color: Color(0xFF302B63), size: 22),
      ),
      const SizedBox(width: 14),
      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Text(item['title_en']?.toString() ?? '', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14)),
        const SizedBox(height: 3),
        Text(
          '${item['category']?['name_en'] ?? ''} · ${item['read_time_minutes'] ?? 0} min read',
          style: const TextStyle(color: AppColors.textLight, fontSize: 11),
        ),
      ])),
      const Icon(Icons.chevron_right_rounded, color: AppColors.textLight),
    ]),
  );
}

class _SyllabusChip extends StatelessWidget {
  final String title;
  final double pct;
  final Color color;
  const _SyllabusChip({required this.title, required this.pct, required this.color});

  @override
  Widget build(BuildContext context) => Container(
    width: 130,
    padding: const EdgeInsets.all(14),
    decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), boxShadow: const [AppColors.thinShadow]),
    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Container(
        padding: const EdgeInsets.all(8),
        decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(10)),
        child: Icon(Icons.book_rounded, color: color, size: 20),
      ),
      const SizedBox(height: 8),
      Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 12, color: AppColors.textDark), maxLines: 2, overflow: TextOverflow.ellipsis),
      const Spacer(),
      LinearProgressIndicator(value: pct, color: color, backgroundColor: color.withOpacity(0.15), minHeight: 5, borderRadius: BorderRadius.circular(3)),
      const SizedBox(height: 4),
      Text('${(pct * 100).toInt()}%', style: TextStyle(fontSize: 11, color: color, fontWeight: FontWeight.bold)),
    ]),
  );
}

class _WeakAreaTile extends StatelessWidget {
  final Map<String, dynamic> area;
  const _WeakAreaTile({required this.area});

  @override
  Widget build(BuildContext context) {
    final pct = (area['accuracy_pct'] as num?)?.toInt() ?? 0;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border(left: BorderSide(color: Colors.red.shade300, width: 4)),
        boxShadow: const [AppColors.thinShadow],
      ),
      child: Row(children: [
        const Icon(Icons.warning_amber_rounded, color: Colors.orange, size: 22),
        const SizedBox(width: 10),
        Expanded(child: Text(area['category']?.toString() ?? 'Topic', style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13))),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
          decoration: BoxDecoration(color: Colors.red.shade50, borderRadius: BorderRadius.circular(8)),
          child: Text('$pct%', style: TextStyle(color: Colors.red.shade700, fontWeight: FontWeight.bold, fontSize: 12)),
        ),
      ]),
    );
  }
}

class _EmptyCard extends StatelessWidget {
  final String message;
  const _EmptyCard({required this.message});
  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.all(20),
    decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16), boxShadow: const [AppColors.thinShadow]),
    child: Row(children: [
      const Icon(Icons.info_outline_rounded, color: AppColors.textLight),
      const SizedBox(width: 12),
      Expanded(child: Text(message, style: const TextStyle(color: AppColors.textMedium, fontSize: 13))),
    ]),
  );
}

Color _catColor(String? code) {
  if (code == null) return const Color(0xFF302B63);
  try { return Color(int.parse('FF${code.replaceAll('#', '')}', radix: 16)); }
  catch (_) { return const Color(0xFF302B63); }
}
