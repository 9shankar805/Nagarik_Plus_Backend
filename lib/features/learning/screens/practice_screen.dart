import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../providers/learning_provider.dart';
import '../models/learning_models.dart';
import '../models/road_sign.dart';

// ─────────────────────────────────────────────────────────────────────────────
// PracticeScreen — mode selector + question runner
// ─────────────────────────────────────────────────────────────────────────────
class PracticeScreen extends StatelessWidget {
  const PracticeScreen({super.key});

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
              bottomRight: Radius.circular(28),
            ),
          ),
          child: SafeArea(
            bottom: false,
            child: Padding(
              padding: const EdgeInsets.fromLTRB(20, 16, 20, 24),
              child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Row(children: [
                      Icon(Icons.fitness_center_rounded,
                          color: Colors.white70, size: 22),
                      SizedBox(width: 10),
                      Text('Practice Mode',
                          style: TextStyle(
                              color: Colors.white,
                              fontSize: 20,
                              fontWeight: FontWeight.bold)),
                    ]),
                    const SizedBox(height: 8),
                    const Text(
                      'Choose a mode to drill questions your way',
                      style: TextStyle(color: Colors.white60, fontSize: 13),
                    ),
                  ]),
            ),
          ),
        ),

        Expanded(
          child: Consumer<LearningProvider>(builder: (context, p, _) {
            return ListView(
              physics: const BouncingScrollPhysics(),
              padding: const EdgeInsets.fromLTRB(16, 20, 16, 100),
              children: [
                // Modes
                _ModeCard(
                  icon: Icons.shuffle_rounded,
                  title: 'All Questions',
                  subtitle: 'Random mix from all categories',
                  color: const Color(0xFF4A148C),
                  tag: null,
                  onTap: () => _startPractice(context, p, 'all'),
                ),
                const SizedBox(height: 12),
                _ModeCard(
                  icon: Icons.replay_rounded,
                  title: 'Wrong Only',
                  subtitle: 'Retry questions you got wrong',
                  color: const Color(0xFFB71C1C),
                  tag: null,
                  onTap: () => _startPractice(context, p, 'wrong_only'),
                ),
                const SizedBox(height: 12),
                _ModeCard(
                  icon: Icons.bookmark_rounded,
                  title: 'Bookmarked',
                  subtitle: 'Questions you saved for later',
                  color: const Color(0xFF1565C0),
                  tag: null,
                  onTap: () => _startPractice(context, p, 'bookmarked'),
                ),
                const SizedBox(height: 12),
                _ModeCard(
                  icon: Icons.speed_rounded,
                  title: 'By Difficulty',
                  subtitle: 'Focus on easy, medium, or hard',
                  color: const Color(0xFF00695C),
                  tag: 'CHOOSE',
                  onTap: () => _showDifficultySheet(context, p),
                ),

                const SizedBox(height: 28),

                // Practice history stats
                if (p.practiceHistory.isNotEmpty) ...[
                  const Text('Recent Sessions',
                      style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w800,
                          color: AppColors.textDark)),
                  const SizedBox(height: 12),
                  ...p.practiceHistory.take(5).map(
                        (h) => _HistoryTile(session: h),
                      ),
                ],
              ],
            );
          }),
        ),
      ]),
    );
  }

  void _startPractice(
      BuildContext context, LearningProvider p, String mode) async {
    final category =
        p.activeCategorySlug ?? (p.categories.isNotEmpty ? p.categories.first.slug : 'general');

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => const Center(
        child: CircularProgressIndicator(),
      ),
    );

    final session =
        await p.startPractice(category: category, mode: mode);
    if (context.mounted) Navigator.pop(context); // close loading

    if (session == null || session.questions.isEmpty) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
              content: Text(
                  mode == 'wrong_only'
                      ? 'No wrong answers found. Great job!'
                      : mode == 'bookmarked'
                          ? 'No bookmarks found.'
                          : 'No questions available')),
        );
      }
      return;
    }

    if (context.mounted) {
      await Navigator.push(
        context,
        MaterialPageRoute(
          builder: (_) =>
              _PracticeRunner(session: session, mode: mode),
        ),
      );
      p.loadPracticeHistory();
    }
  }

  void _showDifficultySheet(
      BuildContext context, LearningProvider p) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
          borderRadius:
              BorderRadius.vertical(top: Radius.circular(20))),
      builder: (_) => Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Choose Difficulty',
                  style: TextStyle(
                      fontSize: 18, fontWeight: FontWeight.bold)),
              const SizedBox(height: 20),
              _DiffTile(
                  label: '😊 Easy',
                  color: AppColors.success,
                  onTap: () {
                    Navigator.pop(context);
                    _startDifficulty(context, p, 'easy');
                  }),
              const SizedBox(height: 10),
              _DiffTile(
                  label: '🤔 Medium',
                  color: Colors.orange,
                  onTap: () {
                    Navigator.pop(context);
                    _startDifficulty(context, p, 'medium');
                  }),
              const SizedBox(height: 10),
              _DiffTile(
                  label: '🔥 Hard',
                  color: AppColors.danger,
                  onTap: () {
                    Navigator.pop(context);
                    _startDifficulty(context, p, 'hard');
                  }),
              const SizedBox(height: 16),
            ]),
      ),
    );
  }

  void _startDifficulty(
      BuildContext context, LearningProvider p, String diff) async {
    final category =
        p.activeCategorySlug ?? (p.categories.isNotEmpty ? p.categories.first.slug : 'general');

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) =>
          const Center(child: CircularProgressIndicator()),
    );

    final session = await p.startPractice(
        category: category, mode: 'difficulty', difficulty: diff);
    if (context.mounted) Navigator.pop(context);

    if (session == null || session.questions.isEmpty) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('No questions for this difficulty')),
        );
      }
      return;
    }

    if (context.mounted) {
      await Navigator.push(
        context,
        MaterialPageRoute(
          builder: (_) =>
              _PracticeRunner(session: session, mode: 'difficulty'),
        ),
      );
      p.loadPracticeHistory();
    }
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Practice Question Runner
// ─────────────────────────────────────────────────────────────────────────────
class _PracticeRunner extends StatefulWidget {
  final PracticeSession session;
  final String mode;
  const _PracticeRunner({required this.session, required this.mode});

  @override
  State<_PracticeRunner> createState() => _PracticeRunnerState();
}

class _PracticeRunnerState extends State<_PracticeRunner> {
  int _current = 0;
  int? _selected;
  bool _revealed = false;
  final List<Map<String, dynamic>> _answers = [];
  final _stopwatch = Stopwatch();

  @override
  void initState() {
    super.initState();
    _stopwatch.start();
  }

  void _selectOption(int idx) {
    if (_revealed) return;
    setState(() {
      _selected = idx;
      _revealed = true;
      final q = widget.session.questions[_current];
      _answers.add({
        'question_id': int.tryParse(q.id) ?? 0,
        'selected': idx,
      });
    });
  }

  void _nextQuestion() {
    if (_current < widget.session.questions.length - 1) {
      setState(() {
        _current++;
        _selected = null;
        _revealed = false;
      });
    } else {
      _submitAll();
    }
  }

  Future<void> _submitAll() async {
    _stopwatch.stop();
    final p = context.read<LearningProvider>();
    final cat = p.activeCategorySlug ?? 'general';

    showDialog(
        context: context,
        barrierDismissible: false,
        builder: (_) => const Center(child: CircularProgressIndicator()));

    try {
      final result = await p.submitPractice({
        'category': cat,
        'mode': widget.mode,
        'answers': _answers,
        'time_taken_seconds': _stopwatch.elapsed.inSeconds,
      });

      if (mounted) {
        Navigator.pop(context); // loading
        Navigator.pop(context); // runner
        _showResultSheet(context, result);
      }
    } catch (_) {
      if (mounted) Navigator.pop(context);
    }
  }

  void _showResultSheet(BuildContext context, dynamic result) {
    final correct = result?.correct ?? 0;
    final total = widget.session.questions.length;
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(28))),
      builder: (_) => Padding(
        padding: const EdgeInsets.all(28),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Text(
            correct >= total ~/ 2 ? '🎉 Good Job!' : '💪 Keep Practising!',
            style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 20),
          Row(mainAxisAlignment: MainAxisAlignment.spaceEvenly, children: [
            _ResultStat(
                label: 'Correct',
                value: '$correct',
                color: AppColors.success),
            _ResultStat(
                label: 'Wrong',
                value: '${total - correct}',
                color: AppColors.danger),
            _ResultStat(
                label: 'Accuracy',
                value: '${total > 0 ? (correct / total * 100).round() : 0}%',
                color: AppColors.primary),
          ]),
          const SizedBox(height: 24),
        ]),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final questions = widget.session.questions;
    if (questions.isEmpty) {
      return const Scaffold(
          body: Center(child: Text('No questions available')));
    }
    final q = questions[_current];
    final correctIdx = q.correctOptionIndex;

    return Scaffold(
      backgroundColor: const Color(0xFF1A1A2E),
      appBar: AppBar(
        backgroundColor: const Color(0xFF1A1A2E),
        foregroundColor: Colors.white,
        elevation: 0,
        title: Text(
          'Q${_current + 1} / ${questions.length}',
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: 16),
            child: Center(
              child: Text(
                '${(((_current + 1) / questions.length) * 100).round()}%',
                style: const TextStyle(
                    color: Colors.amber, fontWeight: FontWeight.bold),
              ),
            ),
          ),
        ],
      ),
      body: Column(children: [
        // Progress
        LinearProgressIndicator(
          value: (_current + 1) / questions.length,
          backgroundColor: Colors.white12,
          color: Colors.amber,
          minHeight: 4,
        ),

        Expanded(
          child: SingleChildScrollView(
            physics: const BouncingScrollPhysics(),
            padding: const EdgeInsets.all(20),
            child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Difficulty chip
                  if (q.difficulty.isNotEmpty)
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                          color: _diffColor(q.difficulty).withOpacity(0.15),
                          borderRadius: BorderRadius.circular(10)),
                      child: Text(
                        q.difficulty.toUpperCase(),
                        style: TextStyle(
                            color: _diffColor(q.difficulty),
                            fontSize: 10,
                            fontWeight: FontWeight.bold),
                      ),
                    ),
                  const SizedBox(height: 16),

                  // Question text
                  Text(
                    q.question,
                    style: const TextStyle(
                        color: Colors.white,
                        fontSize: 18,
                        fontWeight: FontWeight.w600,
                        height: 1.5),
                  ),
                  const SizedBox(height: 24),

                  // Options
                  ...List.generate(q.options.length, (i) {
                    Color bg = const Color(0xFF16213E);
                    Color border = Colors.white12;
                    Color text = Colors.white;
                    Icon? trailIcon;

                    if (_revealed) {
                      if (i == correctIdx) {
                        bg = AppColors.success.withOpacity(0.15);
                        border = AppColors.success;
                        text = AppColors.success;
                        trailIcon = const Icon(Icons.check_circle_rounded,
                            color: AppColors.success, size: 20);
                      } else if (i == _selected) {
                        bg = AppColors.danger.withOpacity(0.15);
                        border = AppColors.danger;
                        text = AppColors.danger;
                        trailIcon = const Icon(Icons.cancel_rounded,
                            color: AppColors.danger, size: 20);
                      }
                    } else if (_selected == i) {
                      bg = Colors.amber.withOpacity(0.1);
                      border = Colors.amber;
                      text = Colors.amber;
                    }

                    return GestureDetector(
                      onTap: () => _selectOption(i),
                      child: AnimatedContainer(
                        duration: const Duration(milliseconds: 200),
                        margin: const EdgeInsets.only(bottom: 12),
                        padding: const EdgeInsets.symmetric(
                            horizontal: 16, vertical: 14),
                        decoration: BoxDecoration(
                          color: bg,
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(color: border, width: 1.5),
                        ),
                        child: Row(children: [
                          Container(
                            width: 28,
                            height: 28,
                            decoration: BoxDecoration(
                              color: border.withOpacity(0.15),
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Center(
                              child: Text(String.fromCharCode(65 + i),
                                  style: TextStyle(
                                      color: text,
                                      fontWeight: FontWeight.bold,
                                      fontSize: 12)),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                              child: Text(q.options[i],
                                  style: TextStyle(
                                      color: text,
                                      fontSize: 14,
                                      fontWeight: FontWeight.w500))),
                          if (trailIcon != null) trailIcon,
                        ]),
                      ),
                    );
                  }),

                  // Explanation
                  if (_revealed && q.explanation.isNotEmpty) ...[
                    const SizedBox(height: 12),
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.blue.withOpacity(0.08),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                            color: Colors.blue.withOpacity(0.3)),
                      ),
                      child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Row(children: [
                              Icon(Icons.lightbulb_rounded,
                                  color: Colors.amber, size: 16),
                              SizedBox(width: 6),
                              Text('Explanation',
                                  style: TextStyle(
                                      color: Colors.amber,
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold)),
                            ]),
                            const SizedBox(height: 8),
                            Text(q.explanation,
                                style: const TextStyle(
                                    color: Colors.white70,
                                    fontSize: 13,
                                    height: 1.4)),
                          ]),
                    ),
                  ],
                ]),
          ),
        ),

        // Next / Finish button
        if (_revealed)
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 0, 20, 24),
            child: SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: _nextQuestion,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.amber,
                  foregroundColor: const Color(0xFF1A1A2E),
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14)),
                ),
                child: Text(
                  _current < questions.length - 1
                      ? 'Next Question →'
                      : 'Finish Practice',
                  style: const TextStyle(
                      fontSize: 16, fontWeight: FontWeight.bold),
                ),
              ),
            ),
          ),
      ]),
    );
  }

  Color _diffColor(String d) {
    switch (d.toLowerCase()) {
      case 'easy':
        return AppColors.success;
      case 'hard':
        return AppColors.danger;
      default:
        return Colors.orange;
    }
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Helper widgets
// ─────────────────────────────────────────────────────────────────────────────
class _ModeCard extends StatelessWidget {
  final IconData icon;
  final String title;
  final String subtitle;
  final Color color;
  final String? tag;
  final VoidCallback onTap;

  const _ModeCard(
      {required this.icon,
      required this.title,
      required this.subtitle,
      required this.color,
      this.tag,
      required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(18),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(18),
          boxShadow: const [AppColors.thinShadow],
          border: Border(left: BorderSide(color: color, width: 5)),
        ),
        child: Row(children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
                color: color.withOpacity(0.1), shape: BoxShape.circle),
            child: Icon(icon, color: color, size: 26),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(children: [
                    Text(title,
                        style: const TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 15,
                            color: AppColors.textDark)),
                    if (tag != null) ...[
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 6, vertical: 2),
                        decoration: BoxDecoration(
                          color: color.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(tag!,
                            style: TextStyle(
                                color: color,
                                fontSize: 9,
                                fontWeight: FontWeight.bold)),
                      ),
                    ],
                  ]),
                  const SizedBox(height: 4),
                  Text(subtitle,
                      style: const TextStyle(
                          color: AppColors.textMedium, fontSize: 12)),
                ]),
          ),
          Icon(Icons.arrow_forward_ios_rounded,
              size: 16, color: AppColors.textLight),
        ]),
      ),
    );
  }
}

class _DiffTile extends StatelessWidget {
  final String label;
  final Color color;
  final VoidCallback onTap;
  const _DiffTile(
      {required this.label, required this.color, required this.onTap});

  @override
  Widget build(BuildContext context) => GestureDetector(
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          decoration: BoxDecoration(
            color: color.withOpacity(0.08),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: color.withOpacity(0.3)),
          ),
          child: Row(children: [
            Text(label,
                style: TextStyle(
                    color: color, fontWeight: FontWeight.bold, fontSize: 14)),
            const Spacer(),
            Icon(Icons.chevron_right_rounded, color: color),
          ]),
        ),
      );
}

class _HistoryTile extends StatelessWidget {
  final Map<String, dynamic> session;
  const _HistoryTile({required this.session});

  @override
  Widget build(BuildContext context) {
    final correct = session['correct_answers'] as int? ?? 0;
    final total = session['questions_answered'] as int? ?? 0;
    final pct = total > 0 ? (correct / total * 100).round() : 0;
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: const [AppColors.thinShadow]),
      child: Row(children: [
        Expanded(
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(
              '${session['mode']?.toString().replaceAll('_', ' ') ?? 'Practice'} · ${session['category'] ?? ''}',
              style: const TextStyle(
                  fontWeight: FontWeight.w600,
                  fontSize: 13,
                  color: AppColors.textDark),
            ),
            const SizedBox(height: 3),
            Text(
              '$correct/$total correct',
              style: const TextStyle(color: AppColors.textLight, fontSize: 12),
            ),
          ]),
        ),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
          decoration: BoxDecoration(
            color: pct >= 60
                ? AppColors.success.withOpacity(0.1)
                : AppColors.danger.withOpacity(0.1),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Text(
            '$pct%',
            style: TextStyle(
                color: pct >= 60 ? AppColors.success : AppColors.danger,
                fontWeight: FontWeight.bold,
                fontSize: 13),
          ),
        ),
      ]),
    );
  }
}

class _ResultStat extends StatelessWidget {
  final String label, value;
  final Color color;
  const _ResultStat(
      {required this.label, required this.value, required this.color});

  @override
  Widget build(BuildContext context) => Column(children: [
        Text(value,
            style: TextStyle(
                fontSize: 28, fontWeight: FontWeight.w800, color: color)),
        Text(label,
            style:
                const TextStyle(color: AppColors.textMedium, fontSize: 13)),
      ]);
}
