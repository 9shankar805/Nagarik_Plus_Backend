import 'dart:math';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../providers/learning_provider.dart';

// ─────────────────────────────────────────────────────────────────────────────
// FlashcardsScreen — set browser or deck runner (pass setId to jump into deck)
// ─────────────────────────────────────────────────────────────────────────────
class FlashcardsScreen extends StatefulWidget {
  final String? setId; // null → show sets list
  const FlashcardsScreen({super.key, this.setId});

  @override
  State<FlashcardsScreen> createState() => _FlashcardsScreenState();
}

class _FlashcardsScreenState extends State<FlashcardsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final p = context.read<LearningProvider>();
      p.loadFlashcardSets();
      p.loadFlashcardsDue();
    });
  }

  @override
  Widget build(BuildContext context) {
    if (widget.setId != null) {
      return _FlashcardDeck(setId: widget.setId!);
    }

    return Consumer<LearningProvider>(builder: (context, p, _) {
      return Scaffold(
        backgroundColor: const Color(0xFFF5F7FA),
        appBar: AppBar(
          title: const Text('Flashcards'),
          backgroundColor: const Color(0xFF302B63),
          foregroundColor: Colors.white,
          elevation: 0,
        ),
        body: p.isLoadingFlashcards
            ? const Center(child: CircularProgressIndicator())
            : ListView(
                physics: const BouncingScrollPhysics(),
                padding: const EdgeInsets.all(16),
                children: [
                  // Due for review
                  if (p.flashcardsDue.isNotEmpty)
                    GestureDetector(
                      onTap: () => Navigator.push(
                          context,
                          MaterialPageRoute(
                              builder: (_) =>
                                  const _DueReviewDeck())),
                      child: Container(
                        margin: const EdgeInsets.only(bottom: 16),
                        padding: const EdgeInsets.all(18),
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(
                              colors: [
                                Color(0xFFE65100),
                                Color(0xFFBF360C)
                              ]),
                          borderRadius: BorderRadius.circular(18),
                        ),
                        child: Row(children: [
                          const Icon(Icons.access_time_rounded,
                              color: Colors.white, size: 28),
                          const SizedBox(width: 14),
                          Expanded(
                            child: Column(
                                crossAxisAlignment:
                                    CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    '${p.flashcardsDue.length} cards due today',
                                    style: const TextStyle(
                                        color: Colors.white,
                                        fontWeight: FontWeight.bold,
                                        fontSize: 15),
                                  ),
                                  const Text('Review now to maintain streak',
                                      style: TextStyle(
                                          color: Colors.white70,
                                          fontSize: 12)),
                                ]),
                          ),
                          const Icon(Icons.arrow_forward_rounded,
                              color: Colors.white),
                        ]),
                      ),
                    ),

                  const Text('All Sets',
                      style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w800,
                          color: AppColors.textDark)),
                  const SizedBox(height: 12),

                  if (p.flashcardSets.isEmpty)
                    Center(
                      child: Padding(
                        padding: const EdgeInsets.all(40),
                        child: Column(children: const [
                          Icon(Icons.style_rounded,
                              size: 56, color: AppColors.textLight),
                          SizedBox(height: 12),
                          Text('No flashcard sets yet',
                              style:
                                  TextStyle(color: AppColors.textMedium)),
                        ]),
                      ),
                    )
                  else
                    ...p.flashcardSets.map((s) => GestureDetector(
                          onTap: () => Navigator.push(
                              context,
                              MaterialPageRoute(
                                  builder: (_) =>
                                      _FlashcardDeck(setId: s.id))),
                          child: Container(
                            margin: const EdgeInsets.only(bottom: 10),
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                                color: Colors.white,
                                borderRadius: BorderRadius.circular(16),
                                boxShadow: const [AppColors.thinShadow]),
                            child: Row(children: [
                              Container(
                                padding: const EdgeInsets.all(12),
                                decoration: BoxDecoration(
                                    color: const Color(0xFF302B63)
                                        .withOpacity(0.08),
                                    borderRadius:
                                        BorderRadius.circular(12)),
                                child: const Icon(Icons.style_rounded,
                                    color: Color(0xFF302B63), size: 24),
                              ),
                              const SizedBox(width: 14),
                              Expanded(
                                child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(s.title,
                                          style: const TextStyle(
                                              fontWeight: FontWeight.bold,
                                              fontSize: 14)),
                                      const SizedBox(height: 4),
                                      Text('${s.cardCount} cards',
                                          style: const TextStyle(
                                              color: AppColors.textLight,
                                              fontSize: 12)),
                                    ]),
                              ),
                              const Icon(Icons.chevron_right_rounded,
                                  color: AppColors.textLight),
                            ]),
                          ),
                        )),
                ],
              ),
      );
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Flashcard Deck Runner (full flip-card experience)
// ─────────────────────────────────────────────────────────────────────────────
class _FlashcardDeck extends StatefulWidget {
  final String setId;
  const _FlashcardDeck({required this.setId});

  @override
  State<_FlashcardDeck> createState() => _FlashcardDeckState();
}

class _FlashcardDeckState extends State<_FlashcardDeck>
    with SingleTickerProviderStateMixin {
  late AnimationController _flipCtrl;
  late Animation<double> _flipAnim;
  bool _isFlipped = false;
  int _current = 0;
  int _knew = 0;
  bool _finished = false;
  List<Map<String, dynamic>> _cards = [];
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _flipCtrl = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 400));
    _flipAnim =
        Tween<double>(begin: 0, end: 1).animate(CurvedAnimation(
      parent: _flipCtrl,
      curve: Curves.easeInOut,
    ));
    _loadSet();
  }

  @override
  void dispose() {
    _flipCtrl.dispose();
    super.dispose();
  }

  Future<void> _loadSet() async {
    // Load cards from the set detail endpoint via provider
    // For now we map FlashcardsDue if available, else fall back to flashcardSets
    final p = context.read<LearningProvider>();
    // Use due cards if this is a due-review session
    final due = p.flashcardsDue
        .map((f) => {
              'id': f.id,
              'front': f.front,
              'front_np': f.frontNp,
              'back': f.back,
              'back_np': f.backNp,
            })
        .toList();
    setState(() {
      _cards = due.isNotEmpty ? due : [];
      _loading = false;
    });
  }

  void _flip() {
    if (_isFlipped) {
      _flipCtrl.reverse();
    } else {
      _flipCtrl.forward();
    }
    setState(() => _isFlipped = !_isFlipped);
  }

  void _answer(bool knew) async {
    if (_cards.isEmpty) return;
    final cardId = _cards[_current]['id']?.toString() ?? '';
    if (cardId.isNotEmpty) {
      context.read<LearningProvider>().reviewFlashcard(cardId, quality: knew ? 5 : 1);
    }

    if (knew) setState(() => _knew++);

    if (_current < _cards.length - 1) {
      setState(() {
        _current++;
        _isFlipped = false;
      });
      _flipCtrl.reset();
    } else {
      setState(() => _finished = true);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const Scaffold(
          body: Center(child: CircularProgressIndicator()));
    }

    if (_cards.isEmpty) {
      return Scaffold(
        appBar: AppBar(
          title: const Text('Flashcards'),
          backgroundColor: const Color(0xFF302B63),
          foregroundColor: Colors.white,
        ),
        body: Center(
          child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
            const Icon(Icons.check_circle_outline_rounded,
                size: 80, color: AppColors.success),
            const SizedBox(height: 16),
            const Text('All caught up!',
                style:
                    TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
            const SizedBox(height: 8),
            const Text('No cards due for review right now.',
                style: TextStyle(color: AppColors.textMedium)),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Go Back'),
            ),
          ]),
        ),
      );
    }

    if (_finished) {
      return Scaffold(
        backgroundColor: const Color(0xFF1A1A2E),
        body: SafeArea(
          child: Center(
            child: Padding(
              padding: const EdgeInsets.all(32),
              child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Text('🎉', style: TextStyle(fontSize: 60)),
                    const SizedBox(height: 20),
                    const Text('Session Complete!',
                        style: TextStyle(
                            color: Colors.white,
                            fontSize: 24,
                            fontWeight: FontWeight.bold)),
                    const SizedBox(height: 20),
                    Row(
                        mainAxisAlignment:
                            MainAxisAlignment.spaceEvenly,
                        children: [
                          _StatCircle(
                              label: 'Knew',
                              value: '$_knew',
                              color: AppColors.success),
                          _StatCircle(
                              label: 'Review',
                              value: '${_cards.length - _knew}',
                              color: Colors.orange),
                          _StatCircle(
                              label: 'Total',
                              value: '${_cards.length}',
                              color: Colors.blue),
                        ]),
                    const SizedBox(height: 36),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        onPressed: () => Navigator.pop(context),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.amber,
                          foregroundColor: const Color(0xFF1A1A2E),
                          padding:
                              const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(14)),
                        ),
                        child: const Text('Done',
                            style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold)),
                      ),
                    ),
                  ]),
            ),
          ),
        ),
      );
    }

    final card = _cards[_current];

    return Scaffold(
      backgroundColor: const Color(0xFF1A1A2E),
      appBar: AppBar(
        backgroundColor: const Color(0xFF1A1A2E),
        foregroundColor: Colors.white,
        elevation: 0,
        title: Text('${_current + 1} / ${_cards.length}',
            style: const TextStyle(fontWeight: FontWeight.bold)),
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: 16),
            child: Center(
              child: Text(
                '$_knew knew',
                style: const TextStyle(
                    color: AppColors.success, fontWeight: FontWeight.bold),
              ),
            ),
          ),
        ],
      ),
      body: Column(children: [
        // Progress
        LinearProgressIndicator(
          value: (_current + 1) / _cards.length,
          backgroundColor: Colors.white12,
          color: Colors.amber,
          minHeight: 4,
        ),

        // Card
        Expanded(
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  // Flip hint
                  const Text('Tap card to flip',
                      style: TextStyle(
                          color: Colors.white38, fontSize: 12)),
                  const SizedBox(height: 16),

                  // 3D Flip Card
                  GestureDetector(
                    onTap: _flip,
                    child: AnimatedBuilder(
                      animation: _flipAnim,
                      builder: (_, __) {
                        final angle = _flipAnim.value * pi;
                        final isBack = angle > pi / 2;
                        return Transform(
                          alignment: Alignment.center,
                          transform: Matrix4.identity()
                            ..setEntry(3, 2, 0.001)
                            ..rotateY(angle),
                          child: isBack
                              ? Transform(
                                  alignment: Alignment.center,
                                  transform: Matrix4.identity()
                                    ..rotateY(pi),
                                  child: _CardFace(
                                    text: card['back']?.toString() ?? '',
                                    label: 'ANSWER',
                                    color:
                                        const Color(0xFF16213E),
                                    textColor: Colors.white,
                                    labelColor: AppColors.success,
                                    borderColor: AppColors.success,
                                  ),
                                )
                              : _CardFace(
                                  text:
                                      card['front']?.toString() ?? '',
                                  label: 'TERM',
                                  color: const Color(0xFF302B63),
                                  textColor: Colors.white,
                                  labelColor: Colors.amber,
                                  borderColor: Colors.amber,
                                ),
                        );
                      },
                    ),
                  ),

                  const SizedBox(height: 32),

                  // Know / Don't Know buttons (only show when flipped)
                  if (_isFlipped)
                    Row(children: [
                      Expanded(
                        child: GestureDetector(
                          onTap: () => _answer(false),
                          child: Container(
                            padding: const EdgeInsets.symmetric(
                                vertical: 14),
                            decoration: BoxDecoration(
                              color:
                                  AppColors.danger.withOpacity(0.15),
                              borderRadius: BorderRadius.circular(14),
                              border: Border.all(
                                  color: AppColors.danger
                                      .withOpacity(0.4)),
                            ),
                            child: const Column(children: [
                              Text('😕',
                                  style: TextStyle(fontSize: 20)),
                              SizedBox(height: 4),
                              Text('Review Again',
                                  style: TextStyle(
                                      color: AppColors.danger,
                                      fontWeight: FontWeight.bold,
                                      fontSize: 12)),
                            ]),
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: GestureDetector(
                          onTap: () => _answer(true),
                          child: Container(
                            padding: const EdgeInsets.symmetric(
                                vertical: 14),
                            decoration: BoxDecoration(
                              color: AppColors.success.withOpacity(0.15),
                              borderRadius: BorderRadius.circular(14),
                              border: Border.all(
                                  color: AppColors.success
                                      .withOpacity(0.4)),
                            ),
                            child: const Column(children: [
                              Text('😊',
                                  style: TextStyle(fontSize: 20)),
                              SizedBox(height: 4),
                              Text('Got It!',
                                  style: TextStyle(
                                      color: AppColors.success,
                                      fontWeight: FontWeight.bold,
                                      fontSize: 12)),
                            ]),
                          ),
                        ),
                      ),
                    ])
                  else
                    const SizedBox(
                      height: 60,
                      child: Center(
                        child: Text(
                          'Flip to reveal the answer',
                          style: TextStyle(
                              color: Colors.white38, fontSize: 13),
                        ),
                      ),
                    ),
                ]),
          ),
        ),
      ]),
    );
  }
}

// Due-review session — shows flashcardsDue cards
class _DueReviewDeck extends StatelessWidget {
  const _DueReviewDeck();
  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(
      builder: (_, p, __) {
        if (p.flashcardsDue.isEmpty) {
          return const Scaffold(
              body: Center(child: Text('No cards due')));
        }
        // Reuse FlashcardDeck with first set or a special key
        return const _FlashcardDeck(setId: '__due__');
      },
    );
  }
}

class _CardFace extends StatelessWidget {
  final String text, label;
  final Color color, textColor, labelColor, borderColor;
  const _CardFace(
      {required this.text,
      required this.label,
      required this.color,
      required this.textColor,
      required this.labelColor,
      required this.borderColor});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      height: 280,
      padding: const EdgeInsets.all(28),
      decoration: BoxDecoration(
        color: color,
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: borderColor.withOpacity(0.4), width: 2),
        boxShadow: [
          BoxShadow(
              color: borderColor.withOpacity(0.2),
              blurRadius: 20,
              offset: const Offset(0, 8))
        ],
      ),
      child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
        Text(label,
            style: TextStyle(
                color: labelColor,
                fontSize: 10,
                fontWeight: FontWeight.bold,
                letterSpacing: 2)),
        const SizedBox(height: 20),
        Text(
          text,
          textAlign: TextAlign.center,
          style: TextStyle(
              color: textColor,
              fontSize: 22,
              fontWeight: FontWeight.w700,
              height: 1.4),
        ),
      ]),
    );
  }
}

class _StatCircle extends StatelessWidget {
  final String label, value;
  final Color color;
  const _StatCircle(
      {required this.label, required this.value, required this.color});

  @override
  Widget build(BuildContext context) => Column(children: [
        Container(
          width: 70,
          height: 70,
          decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              shape: BoxShape.circle,
              border: Border.all(color: color, width: 2)),
          child: Center(
              child: Text(value,
                  style: TextStyle(
                      color: color,
                      fontSize: 24,
                      fontWeight: FontWeight.bold))),
        ),
        const SizedBox(height: 8),
        Text(label,
            style: const TextStyle(
                color: Colors.white60, fontSize: 13)),
      ]);
}
