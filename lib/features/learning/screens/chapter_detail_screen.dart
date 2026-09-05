import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../providers/learning_provider.dart';
import '../models/learning_models.dart';
import '../models/road_sign.dart';
import '../widgets/rich_content_renderer.dart';

// ─────────────────────────────────────────────────────────────────────────────
// ChapterDetailScreen — Ambition Guru-style 3-tab chapter experience:
//   Tab 0 — Notes     : rich text content of the chapter
//   Tab 1 — Practice  : chapter-filtered MCQ questions from API
//   Tab 2 — Video     : placeholder player (YouTube-style thumbnail)
// ─────────────────────────────────────────────────────────────────────────────
class ChapterDetailScreen extends StatefulWidget {
  final LearningChapter chapter;

  const ChapterDetailScreen({super.key, required this.chapter});

  @override
  State<ChapterDetailScreen> createState() => _ChapterDetailScreenState();
}

class _ChapterDetailScreenState extends State<ChapterDetailScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tab;
  bool _bookmarked = false;

  @override
  void initState() {
    super.initState();
    _tab = TabController(length: 3, vsync: this);

    SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.light,
    ));

    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = context.read<LearningProvider>();
      // Load chapter-specific MCQs
      provider.loadQuizQuestions(
        category: widget.chapter.categorySlug,
        limit: 20,
      );
      // Mark as read when opened
      if (!widget.chapter.isRead) {
        provider.markChapterAsRead(widget.chapter.id);
      }
      // Check bookmark status
      final isSaved = provider.bookmarks.any((b) => b['id'].toString() == widget.chapter.id);
      if (mounted) setState(() => _bookmarked = isSaved);
    });
  }

  @override
  void dispose() {
    _tab.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final isNe = Localizations.localeOf(context).languageCode == 'ne';
    final title = isNe
        ? (widget.chapter.titleNp ?? widget.chapter.title)
        : widget.chapter.title;

    return Scaffold(
      backgroundColor: const Color(0xFFF2F5FA),
      body: NestedScrollView(
        headerSliverBuilder: (_, innerBoxIsScrolled) => [
          _buildAppBar(context, title, innerBoxIsScrolled),
        ],
        body: TabBarView(
          controller: _tab,
          children: [
            _NotesTab(chapter: widget.chapter, isNe: isNe),
            _PracticeTab(chapter: widget.chapter, isNe: isNe),
            _VideoTab(chapter: widget.chapter, isNe: isNe),
          ],
        ),
      ),
    );
  }

  // ── SliverAppBar ────────────────────────────────────────────────────────────
  Widget _buildAppBar(
      BuildContext context, String title, bool innerBoxIsScrolled) {
    final isNe = Localizations.localeOf(context).languageCode == 'ne';

    return SliverAppBar(
      expandedHeight: 200,
      pinned: true,
      stretch: true,
      backgroundColor: AppColors.primary,
      foregroundColor: Colors.white,
      leading: IconButton(
        icon: const Icon(Icons.arrow_back_ios_new_rounded),
        onPressed: () => Navigator.pop(context),
      ),
      actions: [
        IconButton(
          icon: Icon(
            _bookmarked ? Icons.bookmark_rounded : Icons.bookmark_border_rounded,
            color: Colors.white,
          ),
          onPressed: () {
            setState(() => _bookmarked = !_bookmarked);
            context
                .read<LearningProvider>()
                .toggleBookmark('chapter', widget.chapter.id);
          },
        ),
        const SizedBox(width: 4),
      ],
      flexibleSpace: FlexibleSpaceBar(
        titlePadding:
            const EdgeInsets.only(left: 56, right: 56, bottom: 56),
        title: Text(
          title,
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
          style: const TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.w800,
              color: Colors.white),
        ),
        background: Stack(
          fit: StackFit.expand,
          children: [
            // Gradient background
            Container(
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [Color(0xFF0D47A1), Color(0xFF1976D2)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
              ),
            ),
            // Decorative pattern
            Positioned(
              right: -30,
              top: -20,
              child: Container(
                width: 160,
                height: 160,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: Colors.white.withValues(alpha: 0.07),
                ),
              ),
            ),
            Positioned(
              left: -20,
              bottom: 20,
              child: Container(
                width: 100,
                height: 100,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: Colors.white.withValues(alpha: 0.05),
                ),
              ),
            ),
            // Chapter info
            Positioned(
              left: 20,
              right: 20,
              bottom: 64,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.18),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const Icon(Icons.access_time_rounded,
                            color: Colors.white70, size: 12),
                        const SizedBox(width: 4),
                        Text(
                          '${widget.chapter.readTimeMinutes} ${isNe ? 'मिनेट' : 'min read'}',
                          style: const TextStyle(
                              color: Colors.white70,
                              fontSize: 11,
                              fontWeight: FontWeight.w500),
                        ),
                        if (widget.chapter.isRead) ...[
                          const SizedBox(width: 8),
                          const Icon(Icons.check_circle_rounded,
                              color: Colors.greenAccent, size: 12),
                          const SizedBox(width: 3),
                          Text(
                            isNe ? 'पढिसकेको' : 'Completed',
                            style: const TextStyle(
                                color: Colors.greenAccent,
                                fontSize: 11,
                                fontWeight: FontWeight.w600),
                          ),
                        ],
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
      bottom: TabBar(
        controller: _tab,
        indicatorColor: Colors.white,
        indicatorWeight: 3,
        labelColor: Colors.white,
        unselectedLabelColor: Colors.white54,
        dividerColor: Colors.transparent,
        labelStyle:
            const TextStyle(fontSize: 13, fontWeight: FontWeight.w700),
        tabs: [
          Tab(
            icon: const Icon(Icons.article_rounded, size: 18),
            text: isNe ? 'नोट्स' : 'Notes',
          ),
          Tab(
            icon: const Icon(Icons.quiz_rounded, size: 18),
            text: isNe ? 'अभ्यास' : 'Practice',
          ),
          Tab(
            icon: const Icon(Icons.play_circle_rounded, size: 18),
            text: isNe ? 'भिडियो' : 'Video',
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Tab 0 — Notes
// ─────────────────────────────────────────────────────────────────────────────
// Tab 0 — Notes  (rich renderer + progress bar + font size + back-to-top)
// ─────────────────────────────────────────────────────────────────────────────
class _NotesTab extends StatefulWidget {
  final LearningChapter chapter;
  final bool isNe;
  const _NotesTab({required this.chapter, required this.isNe});

  @override
  State<_NotesTab> createState() => _NotesTabState();
}

class _NotesTabState extends State<_NotesTab> {
  final ScrollController _scroll = ScrollController();
  double _readProgress = 0.0;   // 0.0 → 1.0
  double _fontSize    = 15.0;   // user-adjustable
  bool   _showBackTop = false;

  static const double _minFont = 12.0;
  static const double _maxFont = 22.0;

  @override
  void initState() {
    super.initState();
    _scroll.addListener(_onScroll);
  }

  void _onScroll() {
    if (!_scroll.hasClients) return;
    final max = _scroll.position.maxScrollExtent;
    final cur = _scroll.offset;
    setState(() {
      _readProgress = max > 0 ? (cur / max).clamp(0.0, 1.0) : 0.0;
      _showBackTop  = cur > 300;
    });
  }

  @override
  void dispose() {
    _scroll.removeListener(_onScroll);
    _scroll.dispose();
    super.dispose();
  }

  void _scrollToTop() {
    _scroll.animateTo(0,
        duration: const Duration(milliseconds: 400),
        curve: Curves.easeOut);
  }

  @override
  Widget build(BuildContext context) {
    final rawContent =
        (widget.isNe ? widget.chapter.contentNp : null) ??
        widget.chapter.content;

    if (rawContent == null || rawContent.trim().isEmpty) {
      return _EmptyState(
        icon: Icons.article_outlined,
        message: widget.isNe
            ? 'यस अध्यायका नोट्स उपलब्ध छैनन्'
            : 'Notes for this chapter are not available yet.',
      );
    }

    return Stack(
      children: [
        // ── Main scrollable content ────────────────────────────────────────
        CustomScrollView(
          controller: _scroll,
          slivers: [
            // Top meta bar: read-time chip + font controls
            SliverToBoxAdapter(child: _buildMetaBar(context, rawContent)),

            // Reading progress bar (sticky under meta bar)
            SliverPersistentHeader(
              pinned: true,
              delegate: _ProgressBarDelegate(progress: _readProgress),
            ),

            // The actual content
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(20, 20, 20, 120),
                child: RichContentRenderer(
                  content: rawContent,
                  baseFontSize: _fontSize,
                ),
              ),
            ),
          ],
        ),

        // ── Back to top FAB ────────────────────────────────────────────────
        AnimatedPositioned(
          duration: const Duration(milliseconds: 250),
          curve: Curves.easeOut,
          bottom: _showBackTop ? 24 : -80,
          right: 20,
          child: _BackToTopButton(onTap: _scrollToTop),
        ),
      ],
    );
  }

  // ── Meta bar: read-time + font-size controls ───────────────────────────────
  Widget _buildMetaBar(BuildContext context, String content) {
    final wordCount = content.split(RegExp(r'\s+')).length;
    final readMins  = (wordCount / 200).ceil().clamp(1, 999);
    final isNe      = widget.isNe;

    return Container(
      padding: const EdgeInsets.fromLTRB(16, 12, 12, 10),
      color: Colors.white,
      child: Row(
        children: [
          // Read-time chip
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
            decoration: BoxDecoration(
              color: AppColors.primary.withValues(alpha: 0.08),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Row(mainAxisSize: MainAxisSize.min, children: [
              const Icon(Icons.access_time_rounded,
                  color: AppColors.primary, size: 13),
              const SizedBox(width: 5),
              Text(
                isNe ? '$readMins मिनेट पठन' : '$readMins min read',
                style: const TextStyle(
                    color: AppColors.primary,
                    fontSize: 11.5,
                    fontWeight: FontWeight.w700),
              ),
            ]),
          ),
          const Spacer(),
          // Font decrease
          _FontBtn(
            icon: Icons.text_decrease_rounded,
            enabled: _fontSize > _minFont,
            onTap: () => setState(() =>
                _fontSize = (_fontSize - 1).clamp(_minFont, _maxFont)),
          ),
          // Font size label
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 6),
            child: Text(
              '${_fontSize.toInt()}',
              style: const TextStyle(
                  color: Color(0xFF3D4A5C),
                  fontSize: 13,
                  fontWeight: FontWeight.w700),
            ),
          ),
          // Font increase
          _FontBtn(
            icon: Icons.text_increase_rounded,
            enabled: _fontSize < _maxFont,
            onTap: () => setState(() =>
                _fontSize = (_fontSize + 1).clamp(_minFont, _maxFont)),
          ),
        ],
      ),
    );
  }
}

// ── Font-size button ──────────────────────────────────────────────────────────
class _FontBtn extends StatelessWidget {
  final IconData icon;
  final bool enabled;
  final VoidCallback onTap;
  const _FontBtn(
      {required this.icon, required this.enabled, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: enabled ? onTap : null,
      child: Container(
        width: 32,
        height: 32,
        decoration: BoxDecoration(
          color: enabled
              ? AppColors.primary.withValues(alpha: 0.08)
              : const Color(0xFFF0F4FA),
          borderRadius: BorderRadius.circular(8),
        ),
        child: Icon(icon,
            size: 16,
            color: enabled ? AppColors.primary : const Color(0xFFCCD5E0)),
      ),
    );
  }
}

// ── Sticky reading progress bar ───────────────────────────────────────────────
class _ProgressBarDelegate extends SliverPersistentHeaderDelegate {
  final double progress;
  const _ProgressBarDelegate({required this.progress});

  @override
  double get minExtent => 4;
  @override
  double get maxExtent => 4;

  @override
  bool shouldRebuild(covariant _ProgressBarDelegate old) =>
      old.progress != progress;

  @override
  Widget build(BuildContext context, double shrinkOffset, bool overlapsContent) {
    return Stack(children: [
      Container(color: AppColors.primary.withValues(alpha: 0.10)),
      FractionallySizedBox(
        widthFactor: progress,
        child: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [Color(0xFF3D8EFF), AppColors.primary],
            ),
          ),
        ),
      ),
    ]);
  }
}

// ── Back to top button ────────────────────────────────────────────────────────
class _BackToTopButton extends StatelessWidget {
  final VoidCallback onTap;
  const _BackToTopButton({required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 44,
        height: 44,
        decoration: BoxDecoration(
          color: AppColors.primary,
          shape: BoxShape.circle,
          boxShadow: [
            BoxShadow(
              color: AppColors.primary.withValues(alpha: 0.40),
              blurRadius: 14,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: const Icon(Icons.keyboard_arrow_up_rounded,
            color: Colors.white, size: 24),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Tab 1 — Practice (MCQ)
// ─────────────────────────────────────────────────────────────────────────────
class _PracticeTab extends StatefulWidget {
  final LearningChapter chapter;
  final bool isNe;
  const _PracticeTab({required this.chapter, required this.isNe});

  @override
  State<_PracticeTab> createState() => _PracticeTabState();
}

class _PracticeTabState extends State<_PracticeTab>
    with AutomaticKeepAliveClientMixin {
  int _current = 0;
  int? _selected;
  bool _answered = false;
  int _score = 0;
  bool _done = false;

  @override
  bool get wantKeepAlive => true;

  void _answer(int idx, List<QuizQuestion> qs) {
    if (_answered) return;
    setState(() {
      _selected = idx;
      _answered = true;
      if (idx == qs[_current].correctOptionIndex) _score++;
    });
  }

  void _next(List<QuizQuestion> qs) {
    if (_current < qs.length - 1) {
      setState(() {
        _current++;
        _selected = null;
        _answered = false;
      });
    } else {
      setState(() => _done = true);
    }
  }

  void _reset() => setState(() {
        _current = 0;
        _selected = null;
        _answered = false;
        _score = 0;
        _done = false;
      });

  @override
  Widget build(BuildContext context) {
    super.build(context);
    return Consumer<LearningProvider>(
      builder: (context, provider, _) {
        if (provider.isLoadingQuizQuestions) {
          return const Center(child: CircularProgressIndicator());
        }

        final qs = provider.quizQuestions;
        if (qs.isEmpty) {
          return _EmptyState(
            icon: Icons.quiz_outlined,
            message: widget.isNe
                ? 'यस अध्यायका प्रश्नहरू उपलब्ध छैनन्'
                : 'No practice questions available for this chapter.',
          );
        }

        if (_done) {
          return _PracticeResult(
            score: _score,
            total: qs.length,
            isNe: widget.isNe,
            onRetry: _reset,
          );
        }

        final q = qs[_current];
        final questionText =
            (widget.isNe ? q.questionNp : null) ?? q.question;
        final opts = (widget.isNe ? q.optionsNp : null) ?? q.options;
        final progress = (_current + 1) / qs.length;

        return Column(children: [
          // Progress bar
          LinearProgressIndicator(
            value: progress,
            minHeight: 5,
            backgroundColor: AppColors.primary.withValues(alpha: 0.12),
            valueColor: const AlwaysStoppedAnimation(AppColors.primary),
          ),
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(16, 20, 16, 100),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Counter + score
                  Row(children: [
                    Text(
                      widget.isNe
                          ? '${qs.length} मध्ये ${_current + 1}'
                          : '${_current + 1} of ${qs.length}',
                      style: const TextStyle(
                          color: Color(0xFF8A9BB0),
                          fontSize: 12,
                          fontWeight: FontWeight.w600),
                    ),
                    const Spacer(),
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: AppColors.secondary.withValues(alpha: 0.10),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Row(mainAxisSize: MainAxisSize.min, children: [
                        const Icon(Icons.star_rounded,
                            color: AppColors.accent, size: 14),
                        const SizedBox(width: 4),
                        Text(
                          '$_score pts',
                          style: const TextStyle(
                              color: AppColors.secondary,
                              fontSize: 12,
                              fontWeight: FontWeight.w700),
                        ),
                      ]),
                    ),
                  ]),
                  const SizedBox(height: 16),
                  // Question card
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFF1565C0), Color(0xFF0D47A1)],
                      ),
                      borderRadius: BorderRadius.circular(18),
                      boxShadow: [
                        BoxShadow(
                          color: AppColors.primary.withValues(alpha: 0.30),
                          blurRadius: 16,
                          offset: const Offset(0, 6),
                        ),
                      ],
                    ),
                    child: Text(
                      questionText,
                      style: const TextStyle(
                          color: Colors.white,
                          fontSize: 16,
                          fontWeight: FontWeight.w700,
                          height: 1.5),
                    ),
                  ),
                  const SizedBox(height: 18),
                  // Options
                  ...List.generate(opts.length, (i) {
                    final isSelected = _selected == i;
                    final isCorrect = i == q.correctOptionIndex;
                    Color bg = Colors.white;
                    Color border = const Color(0xFFE5EAF2);
                    Color text = const Color(0xFF1A2B4A);
                    IconData? trailing;
                    if (_answered) {
                      if (isCorrect) {
                        bg = AppColors.success.withValues(alpha: 0.08);
                        border = AppColors.success;
                        text = AppColors.success;
                        trailing = Icons.check_circle_rounded;
                      } else if (isSelected) {
                        bg = AppColors.danger.withValues(alpha: 0.08);
                        border = AppColors.danger;
                        text = AppColors.danger;
                        trailing = Icons.cancel_rounded;
                      }
                    } else if (isSelected) {
                      bg = AppColors.primary.withValues(alpha: 0.08);
                      border = AppColors.primary;
                      text = AppColors.primary;
                    }
                    return GestureDetector(
                      onTap: () => _answer(i, qs),
                      child: Container(
                        margin: const EdgeInsets.only(bottom: 10),
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          color: bg,
                          borderRadius: BorderRadius.circular(14),
                          border: Border.all(color: border, width: 1.5),
                          boxShadow: [
                            BoxShadow(
                                color: Colors.black.withValues(alpha: 0.04),
                                blurRadius: 4),
                          ],
                        ),
                        child: Row(children: [
                          Container(
                            width: 28,
                            height: 28,
                            decoration: BoxDecoration(
                              color: border.withValues(alpha: 0.15),
                              shape: BoxShape.circle,
                            ),
                            child: Center(
                              child: Text(
                                ['A', 'B', 'C', 'D'][i],
                                style: TextStyle(
                                    color: text,
                                    fontWeight: FontWeight.w700,
                                    fontSize: 12),
                              ),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Text(opts[i],
                                style: TextStyle(
                                    color: text,
                                    fontWeight: FontWeight.w600,
                                    fontSize: 14)),
                          ),
                          if (trailing != null)
                            Icon(trailing, color: text, size: 20),
                        ]),
                      ),
                    );
                  }),
                  // Explanation
                  if (_answered && q.explanation != null) ...[
                    const SizedBox(height: 4),
                    Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        color: AppColors.info.withValues(alpha: 0.08),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                            color: AppColors.info.withValues(alpha: 0.3)),
                      ),
                      child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                        const Icon(Icons.info_outline_rounded,
                            color: AppColors.info, size: 16),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(q.explanation!,
                              style: const TextStyle(
                                  color: Color(0xFF3D4A5C),
                                  fontSize: 13,
                                  height: 1.5)),
                        ),
                      ]),
                    ),
                  ],
                ],
              ),
            ),
          ),
          // Next button
          if (_answered)
            SafeArea(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 0, 16, 12),
                child: ElevatedButton(
                  onPressed: () => _next(qs),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    minimumSize: const Size(double.infinity, 52),
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14)),
                    elevation: 0,
                  ),
                  child: Text(
                    _current < qs.length - 1
                        ? (widget.isNe ? 'अर्को प्रश्न' : 'Next Question')
                        : (widget.isNe ? 'नतिजा हेर्नुहोस्' : 'See Results'),
                    style: const TextStyle(
                        color: Colors.white,
                        fontSize: 15,
                        fontWeight: FontWeight.w700),
                  ),
                ),
              ),
            ),
        ]);
      },
    );
  }
}

// Practice result mini-screen (shown inline within the tab)
class _PracticeResult extends StatelessWidget {
  final int score;
  final int total;
  final bool isNe;
  final VoidCallback onRetry;
  const _PracticeResult(
      {required this.score,
      required this.total,
      required this.isNe,
      required this.onRetry});

  @override
  Widget build(BuildContext context) {
    final pct = total > 0 ? (score / total * 100).round() : 0;
    final passed = pct >= 60;
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Container(
            width: 100,
            height: 100,
            decoration: BoxDecoration(
              color: (passed ? AppColors.success : AppColors.danger)
                  .withValues(alpha: 0.10),
              shape: BoxShape.circle,
            ),
            child: Icon(
              passed
                  ? Icons.emoji_events_rounded
                  : Icons.sentiment_dissatisfied_rounded,
              size: 52,
              color: passed ? AppColors.success : AppColors.danger,
            ),
          ),
          const SizedBox(height: 20),
          Text(
            passed
                ? (isNe ? 'उत्कृष्ट! 🎉' : 'Well done! 🎉')
                : (isNe ? 'फेरि प्रयास गर्नुहोस्' : 'Keep practising'),
            style: const TextStyle(
                fontSize: 20, fontWeight: FontWeight.w800),
          ),
          const SizedBox(height: 8),
          Text(
            isNe
                ? '$total मध्ये $score सही ($pct%)'
                : '$score / $total correct ($pct%)',
            style: const TextStyle(
                color: Color(0xFF8A9BB0), fontSize: 14),
          ),
          const SizedBox(height: 28),
          ElevatedButton.icon(
            onPressed: onRetry,
            icon: const Icon(Icons.refresh_rounded),
            label: Text(isNe ? 'फेरि प्रयास' : 'Try Again'),
            style: ElevatedButton.styleFrom(
              minimumSize: const Size(200, 50),
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(14)),
            ),
          ),
        ]),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Tab 2 — Video (YouTube-style placeholder)
// ─────────────────────────────────────────────────────────────────────────────
class _VideoTab extends StatelessWidget {
  final LearningChapter chapter;
  final bool isNe;
  const _VideoTab({required this.chapter, required this.isNe});

  @override
  Widget build(BuildContext context) {
    return ListView(
      padding: const EdgeInsets.fromLTRB(16, 20, 16, 100),
      children: [
        // Video thumbnail placeholder
        GestureDetector(
          onTap: () {
            ScaffoldMessenger.of(context).showSnackBar(SnackBar(
              content: Text(isNe
                  ? 'भिडियो प्लेयर छिट्टै आउँदैछ'
                  : 'Video player coming soon'),
              behavior: SnackBarBehavior.floating,
              shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12)),
            ));
          },
          child: Container(
            height: 210,
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [Color(0xFF0D47A1), Color(0xFF1565C0)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(18),
              boxShadow: [
                BoxShadow(
                  color: AppColors.primary.withValues(alpha: 0.30),
                  blurRadius: 16,
                  offset: const Offset(0, 6),
                ),
              ],
            ),
            child: Stack(
              alignment: Alignment.center,
              children: [
                // Decorative circles
                Positioned(
                  right: -20,
                  top: -20,
                  child: Container(
                    width: 120,
                    height: 120,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: Colors.white.withValues(alpha: 0.06),
                    ),
                  ),
                ),
                // Play button
                Container(
                  width: 68,
                  height: 68,
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.20),
                    shape: BoxShape.circle,
                    border: Border.all(
                        color: Colors.white.withValues(alpha: 0.6),
                        width: 2),
                  ),
                  child: const Icon(Icons.play_arrow_rounded,
                      color: Colors.white, size: 40),
                ),
                // Coming soon badge
                Positioned(
                  top: 14,
                  right: 14,
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.black.withValues(alpha: 0.40),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Text(
                      isNe ? 'छिट्टै आउँदैछ' : 'Coming Soon',
                      style: const TextStyle(
                          color: Colors.white,
                          fontSize: 10,
                          fontWeight: FontWeight.w700),
                    ),
                  ),
                ),
                // Chapter title at bottom
                Positioned(
                  left: 16,
                  right: 16,
                  bottom: 16,
                  child: Text(
                    isNe
                        ? (chapter.titleNp ?? chapter.title)
                        : chapter.title,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                        color: Colors.white,
                        fontSize: 13,
                        fontWeight: FontWeight.w700),
                  ),
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: 24),
        // What's covered section
        Row(children: [
          Container(
              width: 4,
              height: 16,
              decoration: BoxDecoration(
                  color: AppColors.primary,
                  borderRadius: BorderRadius.circular(2))),
          const SizedBox(width: 8),
          Text(
            isNe ? 'यसमा के छ' : "What's covered",
            style: const TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.w800,
                color: Color(0xFF1A2B4A)),
          ),
        ]),
        const SizedBox(height: 14),
        ..._bulletPoints(isNe).map(
          (point) => Padding(
            padding: const EdgeInsets.only(bottom: 10),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  margin: const EdgeInsets.only(top: 5),
                  width: 7,
                  height: 7,
                  decoration: const BoxDecoration(
                      color: AppColors.primary, shape: BoxShape.circle),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: Text(
                    point,
                    style: const TextStyle(
                        fontSize: 14,
                        color: Color(0xFF3D4A5C),
                        height: 1.5),
                  ),
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: 24),
        // Duration info card
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(14),
            boxShadow: [
              BoxShadow(
                  color: Colors.black.withValues(alpha: 0.05),
                  blurRadius: 8,
                  offset: const Offset(0, 2)),
            ],
          ),
          child: Row(children: [
            _InfoTile(
              icon: Icons.access_time_rounded,
              value: '${chapter.readTimeMinutes} min',
              label: isNe ? 'अवधि' : 'Duration',
              color: AppColors.primary,
            ),
            _VDivider(),
            _InfoTile(
              icon: Icons.quiz_rounded,
              value: isNe ? 'MCQ' : 'MCQ',
              label: isNe ? 'अभ्यास' : 'Practice',
              color: AppColors.secondary,
            ),
            _VDivider(),
            _InfoTile(
              icon: Icons.article_rounded,
              value: isNe ? 'नोट्स' : 'Notes',
              label: isNe ? 'उपलब्ध' : 'Available',
              color: const Color(0xFFF57F17),
            ),
          ]),
        ),
      ],
    );
  }

  List<String> _bulletPoints(bool ne) => ne
      ? [
          'यस अध्यायका मुख्य अवधारणाहरू',
          'व्यावहारिक उदाहरण र प्रदर्शनहरू',
          'परीक्षा सम्बन्धी महत्त्वपूर्ण बुँदाहरू',
          'MCQ अभ्यास प्रश्नहरू',
        ]
      : [
          'Key concepts of this chapter',
          'Practical examples and demonstrations',
          'Important exam-relevant points',
          'MCQ practice questions',
        ];
}

// ── Shared helpers ─────────────────────────────────────────────────────────

class _InfoTile extends StatelessWidget {
  final IconData icon;
  final String value;
  final String label;
  final Color color;
  const _InfoTile(
      {required this.icon,
      required this.value,
      required this.label,
      required this.color});

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: Column(mainAxisSize: MainAxisSize.min, children: [
        Icon(icon, color: color, size: 20),
        const SizedBox(height: 4),
        Text(value,
            style: TextStyle(
                color: color, fontSize: 13, fontWeight: FontWeight.w800)),
        Text(label,
            style: const TextStyle(
                color: Color(0xFF8A9BB0),
                fontSize: 10,
                fontWeight: FontWeight.w500)),
      ]),
    );
  }
}

class _VDivider extends StatelessWidget {
  @override
  Widget build(BuildContext context) =>
      Container(width: 1, height: 36, color: const Color(0xFFEEF2F8));
}

class _EmptyState extends StatelessWidget {
  final IconData icon;
  final String message;
  const _EmptyState({required this.icon, required this.message});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Icon(icon, size: 56, color: Colors.grey.shade300),
          const SizedBox(height: 16),
          Text(
            message,
            textAlign: TextAlign.center,
            style: const TextStyle(
                color: Color(0xFF8A9BB0),
                fontSize: 14,
                fontWeight: FontWeight.w500),
          ),
        ]),
      ),
    );
  }
}
