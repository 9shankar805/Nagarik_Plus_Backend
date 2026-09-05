import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../providers/learning_provider.dart';
import '../models/learning_models.dart';
import 'mock_test_screen.dart';

// ─────────────────────────────────────────────────────────────────────────────
// TestDashboardScreen — Mock Tests · Adaptive · Competitions
// ─────────────────────────────────────────────────────────────────────────────
class TestDashboardScreen extends StatefulWidget {
  const TestDashboardScreen({super.key});

  @override
  State<TestDashboardScreen> createState() => _TestDashboardScreenState();
}

class _TestDashboardScreenState extends State<TestDashboardScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tab;

  @override
  void initState() {
    super.initState();
    _tab = TabController(length: 3, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) => _load());
  }

  @override
  void dispose() {
    _tab.dispose();
    super.dispose();
  }

  void _load() {
    final p = context.read<LearningProvider>();
    p.loadMockTests();
    p.loadCompetitions();
    p.loadStats();
    p.loadAdaptiveHistory();
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
                  padding: const EdgeInsets.fromLTRB(20, 16, 20, 4),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        Text('Test Center',
                            style: TextStyle(
                                color: Colors.white,
                                fontSize: 20,
                                fontWeight: FontWeight.bold)),
                        Text('Challenge yourself daily',
                            style:
                                TextStyle(color: Colors.white60, fontSize: 12)),
                      ]),
                    ],
                  ),
                ),
                // Stats bar
                Padding(
                  padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 20),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                    children: [
                      _StatBadge(
                          icon: Icons.analytics_rounded,
                          label: 'Avg Score',
                          value:
                              '${(p.stats?.avgScore ?? 0).toStringAsFixed(0)}%'),
                      Container(width: 1, height: 36, color: Colors.white24),
                      _StatBadge(
                          icon: Icons.assignment_turned_in_rounded,
                          label: 'Tests Taken',
                          value: '${p.stats?.testsTaken ?? 0}'),
                      Container(width: 1, height: 36, color: Colors.white24),
                      _StatBadge(
                          icon: Icons.emoji_events_rounded,
                          label: 'Competitions',
                          value: '${p.stats?.competitionsJoined ?? 0}'),
                    ],
                  ),
                ),
                TabBar(
                  controller: _tab,
                  indicatorColor: Colors.amber,
                  indicatorWeight: 3,
                  labelColor: Colors.amber,
                  unselectedLabelColor: Colors.white60,
                  labelStyle: const TextStyle(
                      fontWeight: FontWeight.bold, fontSize: 13),
                  tabs: const [
                    Tab(text: 'Mock Tests'),
                    Tab(text: 'Adaptive'),
                    Tab(text: 'Competitions'),
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
              _MockTestsTab(),
              _AdaptiveTab(),
              _CompetitionsTab(),
            ],
          ),
        ),
      ]),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Mock Tests Tab
// ─────────────────────────────────────────────────────────────────────────────
class _MockTestsTab extends StatelessWidget {
  const _MockTestsTab();

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (_, p, __) {
      if (p.isLoadingMockTests) {
        return const Center(child: CircularProgressIndicator());
      }
      final tests = p.mockTests;

      return RefreshIndicator(
        onRefresh: () => p.loadMockTests(),
        child: ListView(
          physics: const BouncingScrollPhysics(),
          padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
          children: [
            // Practice mode shortcut
            _ActionCard(
              title: 'Quick Practice',
              subtitle: 'Unlimited questions · No timer',
              icon: Icons.flash_on_rounded,
              color: const Color(0xFF00695C),
              tag: 'PRACTICE',
              onTap: () => Navigator.push(context,
                  MaterialPageRoute(builder: (_) => const MockTestScreen(isPracticeMode: true))),
            ),
            const SizedBox(height: 12),

            if (tests.isEmpty)
              const _EmptyState(message: 'No mock tests available')
            else ...[
              const _SH(title: 'Available Tests'),
              const SizedBox(height: 10),
              ...tests.map((t) => _MockTestCard(test: t)),
            ],

            // History
            if (p.quizHistory.isNotEmpty) ...[
              const SizedBox(height: 20),
              const _SH(title: 'Recent Attempts'),
              const SizedBox(height: 10),
              ...p.quizHistory.take(5).map((h) => _HistoryRow(item: h)),
            ],
          ],
        ),
      );
    });
  }
}

class _MockTestCard extends StatelessWidget {
  final MockTest test;
  const _MockTestCard({required this.test});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () async {
        final p = context.read<LearningProvider>();
        showDialog(
            context: context,
            barrierDismissible: false,
            builder: (_) => const Center(child: CircularProgressIndicator()));
        try {
          await p.startMockTest(test.id);
          if (context.mounted) {
            Navigator.pop(context);
            Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (_) => const MockTestScreen(isPracticeMode: false)));
          }
        } catch (_) {
          if (context.mounted) Navigator.pop(context);
        }
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: const [AppColors.thinShadow],
          border: const Border(
              left: BorderSide(color: Color(0xFF302B63), width: 5)),
        ),
        child: Row(children: [
          Expanded(
            child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (test.isFeatured)
                    Container(
                      margin: const EdgeInsets.only(bottom: 6),
                      padding: const EdgeInsets.symmetric(
                          horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(
                          color: Colors.amber.withOpacity(0.15),
                          borderRadius: BorderRadius.circular(6)),
                      child: const Text('⭐ FEATURED',
                          style: TextStyle(
                              color: Colors.amber,
                              fontSize: 9,
                              fontWeight: FontWeight.bold)),
                    ),
                  Text(test.title,
                      style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 15,
                          color: AppColors.textDark)),
                  const SizedBox(height: 6),
                  Row(children: [
                    _Chip(
                        label:
                            '${test.totalQuestions} Qs',
                        icon: Icons.help_outline_rounded),
                    const SizedBox(width: 8),
                    _Chip(
                        label: '${test.durationMinutes} min',
                        icon: Icons.timer_rounded),
                  ]),
                ]),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
            decoration: BoxDecoration(
                color: const Color(0xFF302B63),
                borderRadius: BorderRadius.circular(10)),
            child: const Text('Start',
                style: TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                    fontSize: 13)),
          ),
        ]),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Adaptive Test Tab
// ─────────────────────────────────────────────────────────────────────────────
class _AdaptiveTab extends StatelessWidget {
  const _AdaptiveTab();

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (_, p, __) {
      return ListView(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
        children: [
          // Explanation card
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                  colors: [Color(0xFF4A148C), Color(0xFF6A1B9A)]),
              borderRadius: BorderRadius.circular(18),
            ),
            child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Row(children: [
                    Icon(Icons.auto_awesome_rounded,
                        color: Colors.amber, size: 22),
                    SizedBox(width: 8),
                    Text('Adaptive AI Test',
                        style: TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.bold,
                            fontSize: 16)),
                  ]),
                  const SizedBox(height: 10),
                  const Text(
                    'Questions adapt to your skill level in real-time using ELO rating. Hard questions earn more points.',
                    style:
                        TextStyle(color: Colors.white70, fontSize: 13, height: 1.5),
                  ),
                  const SizedBox(height: 16),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: () => _startAdaptive(context, p),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.amber,
                        foregroundColor: const Color(0xFF4A148C),
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12)),
                      ),
                      child: const Text('Start Adaptive Test',
                          style: TextStyle(fontWeight: FontWeight.bold)),
                    ),
                  ),
                ]),
          ),
          const SizedBox(height: 24),

          // How it works
          const _SH(title: 'How It Works'),
          const SizedBox(height: 12),
          const _HowItWorksStep(
              step: '1',
              text: 'Start with a medium difficulty question'),
          const _HowItWorksStep(
              step: '2',
              text: 'Answer correctly → harder question (more points)'),
          const _HowItWorksStep(
              step: '3',
              text: 'Answer wrong → easier question (calibration)'),
          const _HowItWorksStep(
              step: '4',
              text: 'Get ELO score + percentile rank at the end'),

          const SizedBox(height: 24),

          // Past sessions
          if (p.adaptiveHistory.isNotEmpty) ...[
            const _SH(title: 'Past Sessions'),
            const SizedBox(height: 10),
            ...p.adaptiveHistory.take(5).map((h) => _AdaptiveHistoryRow(item: h)),
          ],
        ],
      );
    });
  }

  void _startAdaptive(BuildContext context, LearningProvider p) async {
    final tests = p.mockTests;
    if (tests.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Load mock tests first')));
      return;
    }
    showDialog(
        context: context,
        barrierDismissible: false,
        builder: (_) => const Center(child: CircularProgressIndicator()));
    final session =
        await p.startAdaptiveTest(mockTestId: tests.first.id);
    if (context.mounted) Navigator.pop(context);
    if (session == null && context.mounted) {
      ScaffoldMessenger.of(context)
          .showSnackBar(const SnackBar(content: Text('Could not start test')));
    }
    // Navigate to adaptive runner — reuses MockTestScreen with adaptive flag
    if (session != null && context.mounted) {
      Navigator.push(context,
          MaterialPageRoute(builder: (_) => const MockTestScreen(isPracticeMode: false)));
    }
  }
}

class _HowItWorksStep extends StatelessWidget {
  final String step, text;
  const _HowItWorksStep({required this.step, required this.text});
  @override
  Widget build(BuildContext context) => Padding(
        padding: const EdgeInsets.only(bottom: 10),
        child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Container(
            width: 26,
            height: 26,
            decoration: const BoxDecoration(
                color: Color(0xFF4A148C), shape: BoxShape.circle),
            child: Center(
                child: Text(step,
                    style: const TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                        fontSize: 12))),
          ),
          const SizedBox(width: 12),
          Expanded(
              child: Text(text,
                  style:
                      const TextStyle(color: AppColors.textMedium, fontSize: 13))),
        ]),
      );
}

class _AdaptiveHistoryRow extends StatelessWidget {
  final Map<String, dynamic> item;
  const _AdaptiveHistoryRow({required this.item});
  @override
  Widget build(BuildContext context) {
    final elo = item['elo_score'] ?? item['final_elo'] ?? '–';
    final pct = item['percentile_score'] ?? '–';
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: const [AppColors.thinShadow]),
      child: Row(children: [
        const Icon(Icons.auto_awesome_rounded,
            color: Color(0xFF4A148C), size: 20),
        const SizedBox(width: 10),
        Expanded(
          child: Text('ELO: $elo · Percentile: $pct',
              style: const TextStyle(
                  fontWeight: FontWeight.w600, fontSize: 13)),
        ),
        Text(item['created_at']?.toString().substring(0, 10) ?? '',
            style: const TextStyle(
                color: AppColors.textLight, fontSize: 11)),
      ]),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Competitions Tab
// ─────────────────────────────────────────────────────────────────────────────
class _CompetitionsTab extends StatelessWidget {
  const _CompetitionsTab();

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (_, p, __) {
      if (p.isLoadingCompetitions) {
        return const Center(child: CircularProgressIndicator());
      }
      final open = p.competitions
          .where((c) => c.status == 'open' || c.status == 'active')
          .toList();
      final upcoming = p.competitions
          .where((c) => c.status == 'upcoming')
          .toList();

      return RefreshIndicator(
        onRefresh: () => p.loadCompetitions(),
        child: ListView(
          physics: const BouncingScrollPhysics(),
          padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
          children: [
            if (open.isNotEmpty) ...[
              const _SH(title: '🔴 Live Now'),
              const SizedBox(height: 10),
              ...open.map((c) => _CompetitionCard(competition: c, isLive: true)),
              const SizedBox(height: 20),
            ],
            if (upcoming.isNotEmpty) ...[
              const _SH(title: '📅 Upcoming'),
              const SizedBox(height: 10),
              ...upcoming.map((c) => _CompetitionCard(competition: c)),
              const SizedBox(height: 20),
            ],
            if (open.isEmpty && upcoming.isEmpty)
              const _EmptyState(message: 'No competitions available right now'),
          ],
        ),
      );
    });
  }
}

class _CompetitionCard extends StatelessWidget {
  final Competition competition;
  final bool isLive;
  const _CompetitionCard({required this.competition, this.isLive = false});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: const [AppColors.thinShadow],
        border: isLive
            ? Border.all(color: Colors.red.withOpacity(0.4))
            : null,
      ),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          if (isLive) ...[
            Container(
              padding:
                  const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
              decoration: BoxDecoration(
                  color: Colors.red, borderRadius: BorderRadius.circular(6)),
              child: const Text('LIVE',
                  style: TextStyle(
                      color: Colors.white,
                      fontSize: 9,
                      fontWeight: FontWeight.bold)),
            ),
            const SizedBox(width: 8),
          ],
          Expanded(
            child: Text(competition.title,
                style: const TextStyle(
                    fontWeight: FontWeight.bold, fontSize: 15)),
          ),
        ]),
        const SizedBox(height: 8),
        Row(children: [
          const Icon(Icons.people_rounded,
              size: 14, color: AppColors.textLight),
          const SizedBox(width: 4),
          Text('${competition.participantsCount} participants',
              style: const TextStyle(
                  color: AppColors.textLight, fontSize: 12)),
          if (competition.startTime != null) ...[
            const SizedBox(width: 12),
            const Icon(Icons.schedule_rounded,
                size: 14, color: AppColors.textLight),
            const SizedBox(width: 4),
            Text(
              competition.startTime!.substring(0, 10),
              style: const TextStyle(
                  color: AppColors.textLight, fontSize: 12),
            ),
          ],
        ]),
        const SizedBox(height: 14),
        SizedBox(
          width: double.infinity,
          child: ElevatedButton(
            onPressed: () => _register(context, competition),
            style: ElevatedButton.styleFrom(
              backgroundColor:
                  isLive ? Colors.red : const Color(0xFF302B63),
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(vertical: 12),
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10)),
            ),
            child: Text(isLive ? 'Join Now' : 'Register',
                style: const TextStyle(fontWeight: FontWeight.bold)),
          ),
        ),
      ]),
    );
  }

  void _register(BuildContext context, Competition c) async {
    final p = context.read<LearningProvider>();
    try {
      await p.startCompetition(c.id);
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Competition started!')));
      }
    } catch (_) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Could not join competition')));
      }
    }
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Shared helper widgets
// ─────────────────────────────────────────────────────────────────────────────
class _StatBadge extends StatelessWidget {
  final IconData icon;
  final String label, value;
  const _StatBadge(
      {required this.icon, required this.label, required this.value});
  @override
  Widget build(BuildContext context) => Column(children: [
        Icon(icon, color: Colors.amber, size: 22),
        const SizedBox(height: 4),
        Text(value,
            style: const TextStyle(
                color: Colors.white,
                fontSize: 16,
                fontWeight: FontWeight.bold)),
        Text(label,
            style: const TextStyle(color: Colors.white60, fontSize: 10)),
      ]);
}

class _ActionCard extends StatelessWidget {
  final String title, subtitle, tag;
  final IconData icon;
  final Color color;
  final VoidCallback onTap;
  const _ActionCard(
      {required this.title,
      required this.subtitle,
      required this.icon,
      required this.color,
      required this.tag,
      required this.onTap});

  @override
  Widget build(BuildContext context) => GestureDetector(
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.all(18),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            boxShadow: const [AppColors.thinShadow],
            border:
                Border(left: BorderSide(color: color, width: 5)),
          ),
          child: Row(children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                  color: color.withOpacity(0.1), shape: BoxShape.circle),
              child: Icon(icon, color: color, size: 24),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(title,
                        style: const TextStyle(
                            fontWeight: FontWeight.bold, fontSize: 14)),
                    const SizedBox(height: 3),
                    Text(subtitle,
                        style: const TextStyle(
                            color: AppColors.textMedium, fontSize: 12)),
                  ]),
            ),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
              decoration: BoxDecoration(
                  color: color.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(8)),
              child: Text(tag,
                  style: TextStyle(
                      color: color,
                      fontSize: 9,
                      fontWeight: FontWeight.bold)),
            ),
          ]),
        ),
      );
}

class _HistoryRow extends StatelessWidget {
  final Map<String, dynamic> item;
  const _HistoryRow({required this.item});
  @override
  Widget build(BuildContext context) {
    final score = item['score'] ?? item['percentage'] ?? 0;
    final total = item['total_questions'] ?? '–';
    final passed = (score is num && score >= 60) ||
        (score is String && double.tryParse(score) != null &&
            double.parse(score) >= 60);
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: const [AppColors.thinShadow]),
      child: Row(children: [
        Icon(
          passed
              ? Icons.check_circle_rounded
              : Icons.cancel_rounded,
          color: passed ? AppColors.success : AppColors.danger,
          size: 20,
        ),
        const SizedBox(width: 10),
        Expanded(
          child: Text(
            '${item['category'] ?? 'Test'} · $total questions',
            style: const TextStyle(
                fontWeight: FontWeight.w600, fontSize: 13),
          ),
        ),
        Text('$score%',
            style: TextStyle(
                color: passed ? AppColors.success : AppColors.danger,
                fontWeight: FontWeight.bold,
                fontSize: 13)),
      ]),
    );
  }
}

class _Chip extends StatelessWidget {
  final String label;
  final IconData icon;
  const _Chip({required this.label, required this.icon});
  @override
  Widget build(BuildContext context) => Row(children: [
        Icon(icon, size: 12, color: AppColors.textLight),
        const SizedBox(width: 3),
        Text(label,
            style: const TextStyle(
                color: AppColors.textLight, fontSize: 11)),
      ]);
}

class _SH extends StatelessWidget {
  final String title;
  const _SH({required this.title});
  @override
  Widget build(BuildContext context) => Text(title,
      style: const TextStyle(
          fontSize: 15,
          fontWeight: FontWeight.w800,
          color: AppColors.textDark));
}

class _EmptyState extends StatelessWidget {
  final String message;
  const _EmptyState({required this.message});
  @override
  Widget build(BuildContext context) => Center(
        child: Padding(
          padding: const EdgeInsets.all(40),
          child: Column(children: [
            const Icon(Icons.assignment_outlined,
                size: 56, color: AppColors.textLight),
            const SizedBox(height: 12),
            Text(message,
                style: const TextStyle(color: AppColors.textMedium),
                textAlign: TextAlign.center),
          ]),
        ),
      );
}
