import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../providers/learning_provider.dart';

// ─────────────────────────────────────────────────────────────────────────────
// ProgressScreen — Streak · Syllabus · Mastery · Weak Areas · Badges
// ─────────────────────────────────────────────────────────────────────────────
class ProgressScreen extends StatefulWidget {
  const ProgressScreen({super.key});

  @override
  State<ProgressScreen> createState() => _ProgressScreenState();
}

class _ProgressScreenState extends State<ProgressScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tab;

  @override
  void initState() {
    super.initState();
    _tab = TabController(length: 4, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) => _load());
  }

  @override
  void dispose() {
    _tab.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    final p = context.read<LearningProvider>();
    await Future.wait([
      p.initStreak(),
      p.loadStats(),
      p.loadAchievements(),
    ]);
    if (p.categories.isNotEmpty) {
      p.loadAnalytics(categorySlug: p.categories.first.slug);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FA),
      body: Column(children: [
        // Header
        Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [Color(0xFF302B63), Color(0xFF24243E)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(28),
                bottomRight: Radius.circular(28)),
          ),
          child: SafeArea(
            bottom: false,
            child: Consumer<LearningProvider>(builder: (_, p, __) {
              return Column(children: [
                Padding(
                  padding: const EdgeInsets.fromLTRB(20, 16, 20, 0),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        Text('My Progress',
                            style: TextStyle(
                                color: Colors.white,
                                fontSize: 20,
                                fontWeight: FontWeight.bold)),
                        Text('Track your learning journey',
                            style: TextStyle(color: Colors.white60, fontSize: 12)),
                      ]),
                      // Streak pill
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                        decoration: BoxDecoration(
                            color: Colors.orange.withOpacity(0.2),
                            borderRadius: BorderRadius.circular(20)),
                        child: Row(children: [
                          const Text('🔥', style: TextStyle(fontSize: 18)),
                          const SizedBox(width: 6),
                          Text(
                            '${p.streakDays} day streak',
                            style: const TextStyle(
                                color: Colors.orange,
                                fontWeight: FontWeight.bold,
                                fontSize: 13),
                          ),
                        ]),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 12),
                // Top stats
                Padding(
                  padding: const EdgeInsets.fromLTRB(20, 0, 20, 0),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      _HStat(label: 'Tests', value: '${p.stats?.testsTaken ?? 0}'),
                      _divider(),
                      _HStat(
                          label: 'Avg Score',
                          value: '${(p.stats?.avgScore ?? 0).toStringAsFixed(0)}%'),
                      _divider(),
                      _HStat(label: 'Badges', value: '${p.myAchievements.length}'),
                      _divider(),
                      _HStat(
                          label: 'Best Streak',
                          value: '${p.streakDays}d'),
                    ],
                  ),
                ),
                const SizedBox(height: 14),
                TabBar(
                  controller: _tab,
                  indicatorColor: Colors.amber,
                  indicatorWeight: 3,
                  labelColor: Colors.amber,
                  unselectedLabelColor: Colors.white60,
                  labelStyle:
                      const TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
                  tabs: const [
                    Tab(text: 'Streak'),
                    Tab(text: 'Syllabus'),
                    Tab(text: 'Mastery'),
                    Tab(text: 'Badges'),
                  ],
                ),
              ]);
            }),
          ),
        ),

        Expanded(
          child: TabBarView(
            controller: _tab,
            children: const [
              _StreakTab(),
              _SyllabusTab(),
              _MasteryTab(),
              _BadgesTab(),
            ],
          ),
        ),
      ]),
    );
  }

  Widget _divider() =>
      Container(width: 1, height: 32, color: Colors.white24);
}

// ─────────────────────────────────────────────────────────────────────────────
// Streak Tab
// ─────────────────────────────────────────────────────────────────────────────
class _StreakTab extends StatelessWidget {
  const _StreakTab();

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (_, p, __) {
      return RefreshIndicator(
        onRefresh: () => p.initStreak(),
        child: ListView(
          physics: const BouncingScrollPhysics(),
          padding: const EdgeInsets.all(20),
          children: [
            // Big streak card
            Container(
              padding: const EdgeInsets.all(24),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                    colors: [Color(0xFFFF8F00), Color(0xFFFF6F00)]),
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                      color: const Color(0xFFFF6F00).withOpacity(0.3),
                      blurRadius: 20,
                      offset: const Offset(0, 8))
                ],
              ),
              child: Column(children: [
                const Text('🔥', style: TextStyle(fontSize: 48)),
                const SizedBox(height: 8),
                Text(
                  '${p.streakDays}',
                  style: const TextStyle(
                      color: Colors.white,
                      fontSize: 56,
                      fontWeight: FontWeight.w900),
                ),
                const Text('Day Streak',
                    style: TextStyle(
                        color: Colors.white70,
                        fontSize: 16,
                        fontWeight: FontWeight.w600)),
              ]),
            ),
            const SizedBox(height: 20),

            // 30-day calendar heatmap
            _StreakCalendar(),
            const SizedBox(height: 20),

            // Daily quiz streak
            Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16),
                  boxShadow: const [AppColors.thinShadow]),
              child: Row(children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                      color: Colors.purple.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(12)),
                  child: const Icon(Icons.psychology_alt_rounded,
                      color: Colors.purple, size: 24),
                ),
                const SizedBox(width: 14),
                const Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Daily Quiz Streak',
                          style: TextStyle(
                              fontWeight: FontWeight.bold, fontSize: 14)),
                      Text('Consecutive quiz days',
                          style: TextStyle(
                              color: AppColors.textLight, fontSize: 11)),
                    ]),
                const Spacer(),
                const Text('–',
                    style: TextStyle(
                        fontSize: 22,
                        fontWeight: FontWeight.w900,
                        color: Colors.purple)),
              ]),
            ),
          ],
        ),
      );
    });
  }
}

class _StreakCalendar extends StatelessWidget {
  _StreakCalendar();

  @override
  Widget build(BuildContext context) {
    // Build a 30-day grid. Active dates would come from the backend.
    // For now use a placeholder grid.
    final now = DateTime.now();
    final days = List.generate(
        30, (i) => now.subtract(Duration(days: 29 - i)));

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: const [AppColors.thinShadow]),
      child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Last 30 Days',
                style: TextStyle(
                    fontWeight: FontWeight.bold, fontSize: 14)),
            const SizedBox(height: 14),
            GridView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              gridDelegate:
                  const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 10,
                crossAxisSpacing: 4,
                mainAxisSpacing: 4,
                childAspectRatio: 1,
              ),
              itemCount: 30,
              itemBuilder: (_, i) {
                final isToday = days[i].day == now.day &&
                    days[i].month == now.month;
                // Random active for demo — replace with real active_dates
                final active = (i % 3 != 0);
                return Container(
                  decoration: BoxDecoration(
                    color: isToday
                        ? Colors.orange
                        : active
                            ? const Color(0xFF302B63).withOpacity(0.7)
                            : const Color(0xFFF0F0F0),
                    borderRadius: BorderRadius.circular(4),
                  ),
                  child: Center(
                    child: Text(
                      '${days[i].day}',
                      style: TextStyle(
                          fontSize: 9,
                          fontWeight: FontWeight.bold,
                          color: (active || isToday)
                              ? Colors.white
                              : AppColors.textLight),
                    ),
                  ),
                );
              },
            ),
            const SizedBox(height: 12),
            Row(children: [
              _Legend(color: const Color(0xFF302B63), label: 'Active'),
              const SizedBox(width: 16),
              _Legend(color: Colors.orange, label: 'Today'),
              const SizedBox(width: 16),
              _Legend(color: const Color(0xFFF0F0F0), label: 'Missed'),
            ]),
          ]),
    );
  }
}

class _Legend extends StatelessWidget {
  final Color color;
  final String label;
  const _Legend({required this.color, required this.label});
  @override
  Widget build(BuildContext context) => Row(children: [
        Container(
            width: 12, height: 12, decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(3))),
        const SizedBox(width: 4),
        Text(label, style: const TextStyle(fontSize: 11, color: AppColors.textLight)),
      ]);
}

// ─────────────────────────────────────────────────────────────────────────────
// Syllabus Tab
// ─────────────────────────────────────────────────────────────────────────────
class _SyllabusTab extends StatelessWidget {
  const _SyllabusTab();

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (_, p, __) {
      final chapters = p.syllabus['chapters'] as List? ?? [];
      final total = p.syllabus['total_chapters'] as int? ?? 0;
      final completed = p.syllabus['completed'] as int? ?? 0;
      final pct = p.syllabus['completion_pct'] as int? ?? 0;

      return p.isLoadingAnalytics
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () => p.loadAnalytics(
                  categorySlug: p.activeCategorySlug ??
                      (p.categories.isNotEmpty
                          ? p.categories.first.slug
                          : '')),
              child: ListView(
                physics: const BouncingScrollPhysics(),
                padding: const EdgeInsets.all(20),
                children: [
                  // Overall bar
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        boxShadow: const [AppColors.thinShadow]),
                    child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                              mainAxisAlignment:
                                  MainAxisAlignment.spaceBetween,
                              children: [
                                const Text('Overall Progress',
                                    style: TextStyle(
                                        fontWeight: FontWeight.bold,
                                        fontSize: 15)),
                                Text('$completed / $total chapters',
                                    style: const TextStyle(
                                        color: AppColors.textLight,
                                        fontSize: 12)),
                              ]),
                          const SizedBox(height: 12),
                          ClipRRect(
                            borderRadius: BorderRadius.circular(6),
                            child: LinearProgressIndicator(
                              value: pct / 100,
                              backgroundColor:
                                  const Color(0xFF302B63).withOpacity(0.1),
                              color: const Color(0xFF302B63),
                              minHeight: 12,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text('$pct% complete',
                              style: const TextStyle(
                                  fontWeight: FontWeight.bold,
                                  color: Color(0xFF302B63))),
                        ]),
                  ),
                  const SizedBox(height: 16),

                  // Chapter list
                  ...chapters.asMap().entries.map((e) {
                    final i = e.key;
                    final ch = e.value as Map<String, dynamic>;
                    final done = ch['completed'] as bool? ?? false;
                    return Container(
                      margin: const EdgeInsets.only(bottom: 8),
                      padding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 12),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                        boxShadow: const [AppColors.thinShadow],
                        border: done
                            ? Border.all(
                                color:
                                    AppColors.success.withOpacity(0.3))
                            : null,
                      ),
                      child: Row(children: [
                        Container(
                          width: 32,
                          height: 32,
                          decoration: BoxDecoration(
                            color: done
                                ? AppColors.success.withOpacity(0.1)
                                : const Color(0xFF302B63).withOpacity(0.06),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Center(
                            child: done
                                ? const Icon(Icons.check_rounded,
                                    color: AppColors.success, size: 18)
                                : Text('${i + 1}',
                                    style: const TextStyle(
                                        fontWeight: FontWeight.bold,
                                        fontSize: 12,
                                        color: Color(0xFF302B63))),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: Text(
                            ch['title_en']?.toString() ?? '',
                            style: TextStyle(
                                fontWeight: FontWeight.w600,
                                fontSize: 13,
                                color: done
                                    ? AppColors.textLight
                                    : AppColors.textDark),
                          ),
                        ),
                        if (done)
                          const Icon(Icons.check_circle_rounded,
                              color: AppColors.success, size: 18),
                      ]),
                    );
                  }),
                ],
              ),
            );
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Mastery Tab — topic breakdown + weak areas
// ─────────────────────────────────────────────────────────────────────────────
class _MasteryTab extends StatelessWidget {
  const _MasteryTab();

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (_, p, __) {
      final topics = p.mastery['topics'] as List? ?? [];
      final weakAreas = p.weakAreas['weak_areas'] as List? ?? [];

      return p.isLoadingAnalytics
          ? const Center(child: CircularProgressIndicator())
          : ListView(
              physics: const BouncingScrollPhysics(),
              padding: const EdgeInsets.all(20),
              children: [
                if (weakAreas.isNotEmpty) ...[
                  const _SH(title: '⚠️ Weak Areas'),
                  const SizedBox(height: 10),
                  ...weakAreas.map((w) {
                    final area = w as Map<String, dynamic>;
                    final pct =
                        (area['accuracy_pct'] as num?)?.toInt() ?? 0;
                    return Container(
                      margin: const EdgeInsets.only(bottom: 8),
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12),
                        border: Border(
                            left: BorderSide(
                                color: Colors.red.shade300, width: 4)),
                        boxShadow: const [AppColors.thinShadow],
                      ),
                      child: Row(children: [
                        Expanded(
                          child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  area['category']?.toString() ?? '',
                                  style: const TextStyle(
                                      fontWeight: FontWeight.bold,
                                      fontSize: 13),
                                ),
                                const SizedBox(height: 6),
                                ClipRRect(
                                  borderRadius: BorderRadius.circular(4),
                                  child: LinearProgressIndicator(
                                    value: pct / 100,
                                    backgroundColor: Colors.red.shade50,
                                    color: Colors.red.shade400,
                                    minHeight: 6,
                                  ),
                                ),
                              ]),
                        ),
                        const SizedBox(width: 12),
                        Text('$pct%',
                            style: TextStyle(
                                color: Colors.red.shade600,
                                fontWeight: FontWeight.bold,
                                fontSize: 14)),
                      ]),
                    );
                  }),
                  const SizedBox(height: 20),
                ],

                const _SH(title: 'Topic Mastery'),
                const SizedBox(height: 10),

                if (topics.isEmpty)
                  const _Empty(message: 'Take some mock tests to see your mastery map')
                else
                  ...topics.map((t) {
                    final topic = t as Map<String, dynamic>;
                    final pct =
                        (topic['mastery_pct'] as num?)?.toInt() ?? 0;
                    final level =
                        topic['level']?.toString() ?? 'moderate';
                    final color = level == 'strong'
                        ? AppColors.success
                        : level == 'weak'
                            ? AppColors.danger
                            : Colors.orange;

                    return Container(
                      margin: const EdgeInsets.only(bottom: 8),
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(12),
                          boxShadow: const [AppColors.thinShadow]),
                      child: Column(children: [
                        Row(children: [
                          Expanded(
                            child: Text(
                              topic['category']?.toString() ?? '',
                              style: const TextStyle(
                                  fontWeight: FontWeight.w600,
                                  fontSize: 13),
                            ),
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 8, vertical: 3),
                            decoration: BoxDecoration(
                              color: color.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Text(
                              level.toUpperCase(),
                              style: TextStyle(
                                  color: color,
                                  fontSize: 9,
                                  fontWeight: FontWeight.bold),
                            ),
                          ),
                          const SizedBox(width: 8),
                          Text('$pct%',
                              style: TextStyle(
                                  color: color,
                                  fontWeight: FontWeight.bold,
                                  fontSize: 13)),
                        ]),
                        const SizedBox(height: 8),
                        ClipRRect(
                          borderRadius: BorderRadius.circular(4),
                          child: LinearProgressIndicator(
                            value: pct / 100,
                            backgroundColor: color.withOpacity(0.1),
                            color: color,
                            minHeight: 6,
                          ),
                        ),
                      ]),
                    );
                  }),
              ],
            );
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Badges Tab
// ─────────────────────────────────────────────────────────────────────────────
class _BadgesTab extends StatelessWidget {
  const _BadgesTab();

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (_, p, __) {
      final all = p.achievements;
      final earned = p.myAchievements;
      final earnedIds = earned.map((a) => a.id).toSet();

      return p.isLoadingAchievements
          ? const Center(child: CircularProgressIndicator())
          : ListView(
              physics: const BouncingScrollPhysics(),
              padding: const EdgeInsets.all(20),
              children: [
                // Earned count
                Container(
                  padding: const EdgeInsets.all(18),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                        colors: [Color(0xFF302B63), Color(0xFF24243E)]),
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Row(children: [
                    const Text('🏆',
                        style: TextStyle(fontSize: 36)),
                    const SizedBox(width: 16),
                    Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('${earned.length} / ${all.length} Badges',
                              style: const TextStyle(
                                  color: Colors.white,
                                  fontWeight: FontWeight.bold,
                                  fontSize: 16)),
                          Text(
                            '${all.length - earned.length} more to unlock',
                            style: const TextStyle(
                                color: Colors.white60, fontSize: 12),
                          ),
                        ]),
                  ]),
                ),
                const SizedBox(height: 20),

                if (all.isEmpty)
                  const _Empty(message: 'No achievements defined yet')
                else
                  GridView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    gridDelegate:
                        const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 3,
                      crossAxisSpacing: 12,
                      mainAxisSpacing: 12,
                      childAspectRatio: 0.9,
                    ),
                    itemCount: all.length,
                    itemBuilder: (_, i) {
                      final a = all[i];
                      final isEarned = earnedIds.contains(a.id);
                      return Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          border: isEarned
                              ? Border.all(color: Colors.amber, width: 2)
                              : null,
                          boxShadow: const [AppColors.thinShadow],
                        ),
                        child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              ColorFiltered(
                                colorFilter: isEarned
                                    ? const ColorFilter.mode(
                                        Colors.transparent,
                                        BlendMode.multiply)
                                    : const ColorFilter.matrix([
                                        0.2126, 0.7152, 0.0722, 0, 0,
                                        0.2126, 0.7152, 0.0722, 0, 0,
                                        0.2126, 0.7152, 0.0722, 0, 0,
                                        0, 0, 0, 1, 0,
                                      ]),
                                child: Text(
                                  a.iconUrl ?? '🏅',
                                  style: const TextStyle(fontSize: 32),
                                ),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                a.title,
                                textAlign: TextAlign.center,
                                style: TextStyle(
                                    fontWeight: FontWeight.bold,
                                    fontSize: 11,
                                    color: isEarned
                                        ? AppColors.textDark
                                        : AppColors.textLight),
                              ),
                              if (isEarned) ...[
                                const SizedBox(height: 4),
                                const Icon(Icons.verified_rounded,
                                    color: Colors.amber, size: 14),
                              ],
                            ]),
                      );
                    },
                  ),
              ],
            );
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Shared helper widgets
// ─────────────────────────────────────────────────────────────────────────────
class _HStat extends StatelessWidget {
  final String label, value;
  const _HStat({required this.label, required this.value});
  @override
  Widget build(BuildContext context) => Column(children: [
        Text(value,
            style: const TextStyle(
                color: Colors.white,
                fontSize: 17,
                fontWeight: FontWeight.w800)),
        Text(label,
            style: const TextStyle(color: Colors.white60, fontSize: 10)),
      ]);
}

class _SH extends StatelessWidget {
  final String title;
  const _SH({required this.title});
  @override
  Widget build(BuildContext context) => Text(title,
      style: const TextStyle(
          fontSize: 15, fontWeight: FontWeight.w800, color: AppColors.textDark));
}

class _Empty extends StatelessWidget {
  final String message;
  const _Empty({required this.message});
  @override
  Widget build(BuildContext context) => Container(
        padding: const EdgeInsets.all(32),
        decoration: BoxDecoration(
            color: Colors.white, borderRadius: BorderRadius.circular(16)),
        child: Column(children: [
          const Icon(Icons.info_outline_rounded,
              size: 40, color: AppColors.textLight),
          const SizedBox(height: 12),
          Text(message,
              style: const TextStyle(color: AppColors.textMedium),
              textAlign: TextAlign.center),
        ]),
      );
}
