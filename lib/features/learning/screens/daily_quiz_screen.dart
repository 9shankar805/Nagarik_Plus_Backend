import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../providers/learning_provider.dart';

// ─────────────────────────────────────────────────────────────────────────────
// DailyQuizScreen — Ambition Guru style full-screen daily challenge
// ─────────────────────────────────────────────────────────────────────────────
class DailyQuizScreen extends StatefulWidget {
  const DailyQuizScreen({super.key});

  @override
  State<DailyQuizScreen> createState() => _DailyQuizScreenState();
}

class _DailyQuizScreenState extends State<DailyQuizScreen>
    with SingleTickerProviderStateMixin {
  int? _selectedIndex;
  bool _submitted = false;
  bool _isAnswering = false;
  Map<String, dynamic>? _result;
  late AnimationController _anim;
  late Animation<double> _scaleAnim;

  @override
  void initState() {
    super.initState();
    _anim = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 400));
    _scaleAnim = CurvedAnimation(parent: _anim, curve: Curves.elasticOut);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<LearningProvider>().loadDailyQuiz();
    });
  }

  @override
  void dispose() {
    _anim.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (_selectedIndex == null || _isAnswering) return;
    setState(() => _isAnswering = true);

    final p = context.read<LearningProvider>();
    final quiz = p.dailyQuiz;
    if (quiz == null) return;

    try {
      final res = await p.answerDailyQuiz(
          dailyQuizId: quiz.id, selected: _selectedIndex!);
      setState(() {
        _result = res;
        _submitted = true;
      });
      _anim.forward();
    } catch (_) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Could not submit answer')),
        );
      }
    } finally {
      setState(() => _isAnswering = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (ctx, p, _) {
      final quiz = p.dailyQuiz;

      return Scaffold(
        backgroundColor: const Color(0xFF1A1A2E),
        appBar: AppBar(
          backgroundColor: const Color(0xFF1A1A2E),
          foregroundColor: Colors.white,
          elevation: 0,
          title: Row(children: [
            const Icon(Icons.psychology_alt_rounded,
                color: Colors.amber, size: 22),
            const SizedBox(width: 8),
            const Text('Daily Challenge',
                style: TextStyle(fontWeight: FontWeight.bold)),
          ]),
          actions: [
            if (p.streakDays > 0)
              Container(
                margin: const EdgeInsets.only(right: 16),
                padding:
                    const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: Colors.orange.withOpacity(0.15),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Row(children: [
                  const Icon(Icons.local_fire_department_rounded,
                      color: Colors.orange, size: 18),
                  const SizedBox(width: 4),
                  Text('${p.streakDays}',
                      style: const TextStyle(
                          color: Colors.orange,
                          fontWeight: FontWeight.bold,
                          fontSize: 14)),
                ]),
              ),
          ],
        ),
        body: p.isLoadingDailyQuiz
            ? const Center(
                child: CircularProgressIndicator(color: Colors.amber))
            : quiz == null
                ? const Center(
                    child: Text('No quiz available today',
                        style: TextStyle(color: Colors.white70)))
                : _buildQuizBody(quiz),
      );
    });
  }

  Widget _buildQuizBody(dynamic quiz) {
    // If already answered before opening screen
    final alreadyDone = quiz.isAnswered && !_submitted;

    return SafeArea(
      child: SingleChildScrollView(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.all(20),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          // Date badge
          Container(
            padding:
                const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            decoration: BoxDecoration(
                color: Colors.amber.withOpacity(0.15),
                borderRadius: BorderRadius.circular(20)),
            child: Text(
              quiz.date.isNotEmpty ? quiz.date : 'Today',
              style: const TextStyle(
                  color: Colors.amber,
                  fontWeight: FontWeight.bold,
                  fontSize: 12),
            ),
          ),
          const SizedBox(height: 24),

          // Question card
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: const Color(0xFF16213E),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: Colors.white.withOpacity(0.08)),
            ),
            child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Row(children: [
                    Icon(Icons.help_outline_rounded,
                        color: Colors.amber, size: 18),
                    SizedBox(width: 8),
                    Text('Question',
                        style: TextStyle(
                            color: Colors.amber,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 1)),
                  ]),
                  const SizedBox(height: 16),
                  Text(
                    quiz.question,
                    style: const TextStyle(
                        color: Colors.white,
                        fontSize: 18,
                        fontWeight: FontWeight.w600,
                        height: 1.5),
                  ),
                ]),
          ),
          const SizedBox(height: 24),

          // Options
          ...List.generate(quiz.options.length, (i) {
            final isSelected = _selectedIndex == i;
            final correctIdx = _result?['correct_index'] as int? ??
                (alreadyDone ? quiz.correctOptionIndex : -1);
            final showResult = _submitted || alreadyDone;

            Color bg = const Color(0xFF16213E);
            Color border = Colors.white.withOpacity(0.1);
            Color textColor = Colors.white;
            IconData? trailingIcon;
            Color? iconColor;

            if (showResult) {
              if (i == correctIdx) {
                bg = AppColors.success.withOpacity(0.15);
                border = AppColors.success;
                textColor = AppColors.success;
                trailingIcon = Icons.check_circle_rounded;
                iconColor = AppColors.success;
              } else if (isSelected && i != correctIdx) {
                bg = AppColors.danger.withOpacity(0.15);
                border = AppColors.danger;
                textColor = AppColors.danger;
                trailingIcon = Icons.cancel_rounded;
                iconColor = AppColors.danger;
              }
            } else if (isSelected) {
              bg = Colors.amber.withOpacity(0.1);
              border = Colors.amber;
              textColor = Colors.amber;
            }

            return GestureDetector(
              onTap: showResult
                  ? null
                  : () => setState(() => _selectedIndex = i),
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 200),
                margin: const EdgeInsets.only(bottom: 12),
                padding:
                    const EdgeInsets.symmetric(horizontal: 18, vertical: 16),
                decoration: BoxDecoration(
                  color: bg,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: border, width: 1.5),
                ),
                child: Row(children: [
                  // Letter badge
                  Container(
                    width: 30,
                    height: 30,
                    decoration: BoxDecoration(
                      color: border.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Center(
                      child: Text(
                        String.fromCharCode(65 + i),
                        style: TextStyle(
                            color: textColor,
                            fontWeight: FontWeight.bold,
                            fontSize: 13),
                      ),
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Text(
                      quiz.options[i],
                      style: TextStyle(
                          color: textColor,
                          fontSize: 14,
                          fontWeight: FontWeight.w500),
                    ),
                  ),
                  if (trailingIcon != null)
                    Icon(trailingIcon, color: iconColor, size: 20),
                ]),
              ),
            );
          }),

          const SizedBox(height: 20),

          // Explanation (after submit)
          if ((_submitted || alreadyDone) && quiz.explanation != null) ...[
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.blue.withOpacity(0.1),
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: Colors.blue.withOpacity(0.3)),
              ),
              child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Row(children: [
                      Icon(Icons.lightbulb_rounded,
                          color: Colors.amber, size: 18),
                      SizedBox(width: 8),
                      Text('Explanation',
                          style: TextStyle(
                              color: Colors.amber,
                              fontWeight: FontWeight.bold,
                              fontSize: 13)),
                    ]),
                    const SizedBox(height: 10),
                    Text(
                      quiz.explanation!,
                      style: const TextStyle(
                          color: Colors.white70,
                          fontSize: 13,
                          height: 1.5),
                    ),
                  ]),
            ),
            const SizedBox(height: 20),
          ],

          // Result banner (animated)
          if (_submitted && _result != null)
            ScaleTransition(
              scale: _scaleAnim,
              child: _ResultBanner(result: _result!),
            ),

          // Submit button
          if (!_submitted && !alreadyDone) ...[
            const SizedBox(height: 8),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: _selectedIndex != null && !_isAnswering
                    ? _submit
                    : null,
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.amber,
                  disabledBackgroundColor: Colors.amber.withOpacity(0.3),
                  foregroundColor: const Color(0xFF1A1A2E),
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14)),
                ),
                child: _isAnswering
                    ? const SizedBox(
                        width: 22,
                        height: 22,
                        child: CircularProgressIndicator(
                            strokeWidth: 2, color: Color(0xFF1A1A2E)))
                    : const Text('Submit Answer',
                        style: TextStyle(
                            fontSize: 16, fontWeight: FontWeight.bold)),
              ),
            ),
          ],

          if (alreadyDone)
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                  color: AppColors.success.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(
                      color: AppColors.success.withOpacity(0.4))),
              child: const Row(children: [
                Icon(Icons.check_circle_rounded,
                    color: AppColors.success),
                SizedBox(width: 12),
                Text('Already answered today!',
                    style: TextStyle(
                        color: AppColors.success,
                        fontWeight: FontWeight.bold)),
              ]),
            ),
        ]),
      ),
    );
  }
}

class _ResultBanner extends StatelessWidget {
  final Map<String, dynamic> result;
  const _ResultBanner({required this.result});

  @override
  Widget build(BuildContext context) {
    final correct = result['is_correct'] as bool? ?? false;
    final streak = result['daily_quiz_streak'] as int? ?? 0;

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: correct
              ? [const Color(0xFF1B5E20), const Color(0xFF2E7D32)]
              : [const Color(0xFFB71C1C), const Color(0xFFD32F2F)],
        ),
        borderRadius: BorderRadius.circular(16),
      ),
      child: Row(children: [
        Text(correct ? '🎉' : '😞', style: const TextStyle(fontSize: 32)),
        const SizedBox(width: 16),
        Expanded(
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(
              correct ? 'Correct! Well done!' : 'Not quite right',
              style: const TextStyle(
                  color: Colors.white,
                  fontSize: 16,
                  fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 4),
            if (streak > 0)
              Text(
                '🔥 $streak day streak!',
                style: const TextStyle(color: Colors.white70, fontSize: 13),
              ),
          ]),
        ),
      ]),
    );
  }
}
