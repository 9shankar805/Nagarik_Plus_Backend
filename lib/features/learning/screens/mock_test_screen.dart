import 'dart:async';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../providers/learning_provider.dart';
import '../models/learning_models.dart';
import '../models/road_sign.dart'; // for QuizQuestion
import 'test_analytics_screen.dart';

class MockTestScreen extends StatefulWidget {
  final MockTest? test;
  final bool isCompetition;
  final bool isPracticeMode;

  const MockTestScreen({
    super.key, 
    this.test, 
    this.isCompetition = false,
    this.isPracticeMode = false,
  });

  @override
  State<MockTestScreen> createState() => _MockTestScreenState();
}

class _MockTestScreenState extends State<MockTestScreen> {
  int _currentQuestionIndex = 0;
  List<int?> _selectedAnswers = [];
  bool _testCompleted = false;
  
  Timer? _timer;
  int _secondsRemaining = 0;
  late int _totalSeconds;

  @override
  void initState() {
    super.initState();
    
    // Fallback to 10 minutes if no test duration is provided
    final durationMinutes = widget.test?.durationMinutes != null && widget.test!.durationMinutes > 0 
        ? widget.test!.durationMinutes 
        : 10;
        
    _totalSeconds = durationMinutes * 60;
    _secondsRemaining = _totalSeconds;
    
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = context.read<LearningProvider>();
      // If we are coming from a test card, load questions if they aren't loaded or mismatch
      provider.loadQuizQuestions(category: widget.test?.categorySlug, limit: widget.test?.totalQuestions ?? 20);
    });
  }

  void _startTimer() {
    _timer?.cancel();
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_secondsRemaining > 0) {
        setState(() => _secondsRemaining--);
      } else {
        _submitTest();
      }
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  void _selectAnswer(int optionIndex) {
    setState(() {
      _selectedAnswers[_currentQuestionIndex] = optionIndex;
    });
    // Auto next after 500ms
    Future.delayed(const Duration(milliseconds: 500), () {
      if (mounted && _currentQuestionIndex < _selectedAnswers.length - 1) {
         _nextQuestion();
      }
    });
  }

  void _nextQuestion() {
    if (_currentQuestionIndex < _selectedAnswers.length - 1) {
      setState(() => _currentQuestionIndex++);
    }
  }
  
  void _prevQuestion() {
    if (_currentQuestionIndex > 0) {
      setState(() => _currentQuestionIndex--);
    }
  }

  void _submitTest() {
    _timer?.cancel();
    setState(() => _testCompleted = true);
  }

  String _formatTime(int seconds) {
    final m = seconds ~/ 60;
    final s = seconds % 60;
    return '${m.toString().padLeft(2, '0')}:${s.toString().padLeft(2, '0')}';
  }

  @override
  Widget build(BuildContext context) {
    final provider = context.watch<LearningProvider>();
    final questions = provider.quizQuestions;

    if (provider.isLoadingQuizQuestions) {
      return const Scaffold(
        backgroundColor: AppColors.background,
        body: Center(child: CircularProgressIndicator()),
      );
    }

    if (questions.isEmpty) {
      return Scaffold(
        backgroundColor: AppColors.background,
        appBar: AppBar(title: const Text('Mock Test')),
        body: const Center(child: Text('No questions available for this test.')),
      );
    }

    if (_selectedAnswers.isEmpty || _selectedAnswers.length != questions.length) {
      _selectedAnswers = List.filled(questions.length, null);
      if (_timer == null && !widget.isPracticeMode) _startTimer();
    }

    if (_testCompleted) {
      return _ResultScreen(
        test: widget.test,
        isCompetition: widget.isCompetition,
        questions: questions,
        selectedAnswers: _selectedAnswers,
        timeTakenSeconds: _totalSeconds - _secondsRemaining,
        onRetry: () {
          setState(() {
            _currentQuestionIndex = 0;
            _selectedAnswers = List.filled(questions.length, null);
            _secondsRemaining = _totalSeconds;
            _testCompleted = false;
          });
          if (!widget.isPracticeMode) _startTimer();
        },
      );
    }

    final question = questions[_currentQuestionIndex];
    final isNe = Localizations.localeOf(context).languageCode == 'ne';
    final questionText = isNe ? (question.questionNp ?? question.question) : question.question;
    final options = isNe ? (question.optionsNp ?? question.options) : question.options;

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        title: Text('${isNe ? 'प्रश्न' : 'Question'} ${_currentQuestionIndex + 1}/${questions.length}',
            style: const TextStyle(fontWeight: FontWeight.w700, color: Colors.white, fontSize: 16)),
        leading: IconButton(
          icon: const Icon(Icons.close_rounded),
          onPressed: () {
             // Confirm exit
             showDialog(
               context: context,
               builder: (c) => AlertDialog(
                 title: Text(isNe ? 'परीक्षा छोड्नुहोस्?' : 'Quit Test?'),
                 content: Text(isNe ? 'तपाईंको प्रगति सुरक्षित हुनेछैन।' : 'Your progress will not be saved.'),
                 actions: [
                   TextButton(onPressed: () => Navigator.pop(c), child: Text(isNe ? 'रद्द' : 'Cancel')),
                   TextButton(onPressed: () { Navigator.pop(c); Navigator.pop(context); }, child: Text(isNe ? 'छोड्नुहोस्' : 'Quit', style: const TextStyle(color: AppColors.danger))),
                 ]
               )
             );
          },
        ),
        actions: [
          if (widget.isPracticeMode)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              margin: const EdgeInsets.only(right: 16),
              decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.2), borderRadius: BorderRadius.circular(16)),
              child: const Center(child: Text('Practice Mode', style: TextStyle(fontWeight: FontWeight.w700))),
            )
          else
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              margin: const EdgeInsets.only(right: 16),
              decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.2), borderRadius: BorderRadius.circular(16)),
              child: Row(
                children: [
                  const Icon(Icons.timer_outlined, size: 16, color: Colors.white),
                  const SizedBox(width: 4),
                  Text(_formatTime(_secondsRemaining), style: const TextStyle(fontWeight: FontWeight.w700)),
                ],
              ),
            )
        ],
      ),
      body: Column(
        children: [
          // Progress bar
          Container(
            height: 6,
            color: AppColors.primary.withValues(alpha: 0.2),
            child: FractionallySizedBox(
              widthFactor: (_currentQuestionIndex + 1) / questions.length,
              alignment: Alignment.centerLeft,
              child: Container(color: AppColors.primary),
            ),
          ),

          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFF1565C0), Color(0xFF0D47A1)],
                      ),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(isNe ? 'प्रश्न' : 'Question', style: const TextStyle(color: Colors.white60, fontSize: 12)),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              decoration: BoxDecoration(
                                color: Colors.orange,
                                borderRadius: BorderRadius.circular(8),
                              ),
                              child: const Text('Hard (+3 pts)', style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold)),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Text(questionText,
                            style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.w700, height: 1.4)),
                      ],
                    ),
                  ),

                  const SizedBox(height: 24),

                  // Options
                  ...options.asMap().entries.map((entry) {
                    final index = entry.key;
                    final option = entry.value;
                    final isSelected = _selectedAnswers[_currentQuestionIndex] == index;

                    Color bgColor = isSelected ? AppColors.primary.withValues(alpha: 0.1) : Colors.white;
                    Color borderColor = isSelected ? AppColors.primary : AppColors.divider;
                    Color textColor = isSelected ? AppColors.primary : AppColors.textDark;

                    return GestureDetector(
                      onTap: () => _selectAnswer(index),
                      child: Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: bgColor,
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: borderColor, width: 1.5),
                          boxShadow: const [AppColors.thinShadow],
                        ),
                        child: Row(
                          children: [
                            Container(
                              width: 28, height: 28,
                              decoration: BoxDecoration(
                                color: isSelected ? AppColors.primary : borderColor.withValues(alpha: 0.15),
                                shape: BoxShape.circle,
                              ),
                              child: Center(
                                child: Text(['A', 'B', 'C', 'D', 'E', 'F'][index],
                                  style: TextStyle(color: isSelected ? Colors.white : textColor, fontWeight: FontWeight.w700, fontSize: 13),
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(child: Text(option, style: TextStyle(color: textColor, fontWeight: FontWeight.w600, fontSize: 14))),
                          ],
                        ),
                      ),
                    );
                  }),
                ],
              ),
            ),
          ),

          // Bottom Nav
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              boxShadow: const [AppColors.cardShadow],
            ),
            child: Row(
              children: [
                if (_currentQuestionIndex > 0)
                  Expanded(
                    child: OutlinedButton(
                      onPressed: _prevQuestion,
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ),
                      child: Text(isNe ? 'अघिल्लो' : 'Previous'),
                    ),
                  ),
                if (_currentQuestionIndex > 0) const SizedBox(width: 12),
                Expanded(
                  flex: 2,
                  child: ElevatedButton(
                    onPressed: _currentQuestionIndex < questions.length - 1 ? _nextQuestion : _submitTest,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.primary,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    ),
                    child: Text(
                      _currentQuestionIndex < questions.length - 1 
                        ? (isNe ? 'अर्को प्रश्न' : 'Next Question')
                        : (isNe ? 'बुझाउनुहोस्' : 'Submit Test'),
                      style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w700),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _ResultScreen extends StatefulWidget {
  final MockTest? test;
  final bool isCompetition;
  final List<QuizQuestion> questions;
  final List<int?> selectedAnswers;
  final int timeTakenSeconds;
  final VoidCallback onRetry;

  const _ResultScreen({
    this.test,
    required this.isCompetition,
    required this.questions,
    required this.selectedAnswers,
    required this.timeTakenSeconds,
    required this.onRetry,
  });

  @override
  State<_ResultScreen> createState() => _ResultScreenState();
}

class _ResultScreenState extends State<_ResultScreen> {
  bool _isSaving = false;
  bool _isSaved = false;
  
  @override
  void initState() {
    super.initState();
    // Auto save result
    _saveResult();
  }
  
  Future<void> _saveResult() async {
    setState(() => _isSaving = true);
    try {
      final provider = context.read<LearningProvider>();
      
      int score = 0;
      for (int i=0; i<widget.questions.length; i++) {
        if (widget.selectedAnswers[i] == widget.questions[i].correctOptionIndex) {
          score++;
        }
      }
      
      final payload = {
        'test_id': widget.test?.id,
        'category_slug': widget.test?.categorySlug ?? 'general',
        'score': score,
        'total_questions': widget.questions.length,
        'time_taken_seconds': widget.timeTakenSeconds,
        'is_competition': widget.isCompetition,
      };
      
      if (widget.isCompetition) {
        await provider.submitCompetition(widget.test?.id ?? '', payload);
      } else {
        await provider.submitQuiz(payload);
      }
      
      if (mounted) setState(() => _isSaved = true);
    } catch (e) {
      // Ignore error for now in UI
    } finally {
      if (mounted) setState(() => _isSaving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    int score = 0;
    int skipped = 0;
    
    for (int i=0; i<widget.questions.length; i++) {
      if (widget.selectedAnswers[i] == widget.questions[i].correctOptionIndex) {
        score++;
      } else if (widget.selectedAnswers[i] == null) {
        skipped++;
      }
    }
    
    final total = widget.questions.length;
    
    return Stack(
      children: [
        TestAnalyticsScreen(
          score: score,
          total: total,
          skipped: skipped,
          timeTakenSeconds: widget.timeTakenSeconds,
          onRetry: widget.onRetry,
        ),
        if (_isSaving)
          Container(
            color: Colors.black.withValues(alpha: 0.3),
            child: const Center(child: CircularProgressIndicator()),
          ),
      ],
    );
  }
}

class _Stat extends StatelessWidget {
  final String label;
  final String value;
  final Color color;
  const _Stat(this.label, this.value, this.color);
  
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Text(value, style: TextStyle(fontSize: 22, fontWeight: FontWeight.w800, color: color)),
        const SizedBox(height: 4),
        Text(label, style: const TextStyle(fontSize: 12, color: Colors.grey, fontWeight: FontWeight.w600)),
      ],
    );
  }
}
