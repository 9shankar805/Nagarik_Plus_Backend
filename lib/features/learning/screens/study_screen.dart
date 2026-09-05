import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../providers/learning_provider.dart';
import '../models/learning_models.dart';
import 'chapter_detail_screen.dart';
import 'flashcards_screen.dart';
import 'video_lectures_screen.dart';

// ─────────────────────────────────────────────────────────────────────────────
// StudyScreen — Categories → Chapters, Videos, Flashcards
// ─────────────────────────────────────────────────────────────────────────────
class StudyScreen extends StatefulWidget {
  const StudyScreen({super.key});

  @override
  State<StudyScreen> createState() => _StudyScreenState();
}

class _StudyScreenState extends State<StudyScreen>
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
    p.loadCategories().then((_) {
      if (p.categories.isNotEmpty && p.activeCategorySlug == null) {
        p.setActiveCategory(p.categories.first.slug);
      }
    });
    p.loadFlashcardSets();
    p.loadVideoClasses();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FA),
      body: Column(
        children: [
          // ── Gradient Header ──────────────────────────────────────────────
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
              child: Column(
                children: [
                  const Padding(
                    padding: EdgeInsets.fromLTRB(20, 16, 20, 0),
                    child: Row(
                      children: [
                        Icon(Icons.menu_book_rounded,
                            color: Colors.white70, size: 22),
                        SizedBox(width: 10),
                        Text(
                          'Study Material',
                          style: TextStyle(
                              color: Colors.white,
                              fontSize: 20,
                              fontWeight: FontWeight.bold),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 12),
                  TabBar(
                    controller: _tab,
                    indicatorColor: Colors.amber,
                    indicatorWeight: 3,
                    labelColor: Colors.amber,
                    unselectedLabelColor: Colors.white60,
                    labelStyle: const TextStyle(
                        fontWeight: FontWeight.bold, fontSize: 14),
                    tabs: const [
                      Tab(text: 'Chapters'),
                      Tab(text: 'Videos'),
                      Tab(text: 'Flashcards'),
                    ],
                  ),
                ],
              ),
            ),
          ),

          // ── Tab Body ─────────────────────────────────────────────────────
          Expanded(
            child: TabBarView(
              controller: _tab,
              children: const [
                _ChaptersTab(),
                _VideosTab(),
                _FlashcardsTab(),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Chapters Tab
// ─────────────────────────────────────────────────────────────────────────────
class _ChaptersTab extends StatelessWidget {
  const _ChaptersTab();

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (context, p, _) {
      final categories = p.categories;
      final chapters = p.chapters;

      return RefreshIndicator(
        onRefresh: () async {
          await p.loadCategories();
          if (p.activeCategorySlug != null) {
            await p.loadChapters(categoryId: p.activeCategorySlug);
          }
        },
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(),
          slivers: [
            // Category pills
            SliverToBoxAdapter(
              child: SizedBox(
                height: 52,
                child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.fromLTRB(16, 10, 16, 4),
                  itemCount: categories.length,
                  itemBuilder: (_, i) {
                    final cat = categories[i];
                    final active = p.activeCategorySlug == cat.slug;
                    return GestureDetector(
                      onTap: () => p.setActiveCategory(cat.slug),
                      child: AnimatedContainer(
                        duration: const Duration(milliseconds: 200),
                        margin: const EdgeInsets.only(right: 8),
                        padding: const EdgeInsets.symmetric(
                            horizontal: 16, vertical: 6),
                        decoration: BoxDecoration(
                          color: active
                              ? const Color(0xFF302B63)
                              : Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(
                            color: active
                                ? const Color(0xFF302B63)
                                : AppColors.divider,
                          ),
                          boxShadow: active
                              ? [
                                  BoxShadow(
                                      color: const Color(0xFF302B63)
                                          .withOpacity(0.25),
                                      blurRadius: 8,
                                      offset: const Offset(0, 3))
                                ]
                              : [],
                        ),
                        child: Text(
                          cat.title,
                          style: TextStyle(
                            color: active
                                ? Colors.white
                                : AppColors.textMedium,
                            fontWeight: active
                                ? FontWeight.bold
                                : FontWeight.w500,
                            fontSize: 13,
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ),
            ),

            // Loading / empty
            if (p.isLoadingChapters)
              const SliverFillRemaining(
                  child: Center(child: CircularProgressIndicator()))
            else if (chapters.isEmpty)
              const SliverFillRemaining(
                child: Center(
                  child: Text('No chapters found',
                      style: TextStyle(color: AppColors.textLight)),
                ),
              )
            else
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(16, 8, 16, 100),
                sliver: SliverList(
                  delegate: SliverChildBuilderDelegate(
                    (_, i) => _ChapterTile(chapter: chapters[i], index: i),
                    childCount: chapters.length,
                  ),
                ),
              ),
          ],
        ),
      );
    });
  }
}

class _ChapterTile extends StatelessWidget {
  final LearningChapter chapter;
  final int index;
  const _ChapterTile({required this.chapter, required this.index});

  @override
  Widget build(BuildContext context) {
    final colors = [
      const Color(0xFF4A148C),
      const Color(0xFF00695C),
      const Color(0xFF1565C0),
      const Color(0xFFB71C1C),
      const Color(0xFFF57F17),
    ];
    final color = colors[index % colors.length];

    return GestureDetector(
      onTap: () => Navigator.push(
          context,
          MaterialPageRoute(
              builder: (_) => ChapterDetailScreen(chapter: chapter))),
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: const [AppColors.thinShadow],
          border: chapter.isRead
              ? Border.all(color: AppColors.success.withOpacity(0.3))
              : null,
        ),
        child: Row(children: [
          // Number badge
          Container(
            width: 44,
            height: 44,
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Center(
              child: chapter.isRead
                  ? Icon(Icons.check_rounded, color: color, size: 22)
                  : Text(
                      '${index + 1}',
                      style: TextStyle(
                          color: color,
                          fontWeight: FontWeight.bold,
                          fontSize: 16),
                    ),
            ),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    chapter.title,
                    style: const TextStyle(
                        fontWeight: FontWeight.w700,
                        fontSize: 14,
                        color: AppColors.textDark),
                  ),
                  if (chapter.readTimeMinutes > 0) ...[
                    const SizedBox(height: 4),
                    Row(children: [
                      const Icon(Icons.schedule_rounded,
                          size: 12, color: AppColors.textLight),
                      const SizedBox(width: 4),
                      Text(
                        '${chapter.readTimeMinutes} min read',
                        style: const TextStyle(
                            color: AppColors.textLight, fontSize: 11),
                      ),
                    ]),
                  ],
                ]),
          ),
          Icon(
            chapter.isRead
                ? Icons.check_circle_rounded
                : Icons.chevron_right_rounded,
            color: chapter.isRead ? AppColors.success : AppColors.textLight,
          ),
        ]),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Videos Tab
// ─────────────────────────────────────────────────────────────────────────────
class _VideosTab extends StatelessWidget {
  const _VideosTab();

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (context, p, _) {
      if (p.isLoadingVideos) {
        return const Center(child: CircularProgressIndicator());
      }

      // Live classes section
      final live =
          p.videoClasses.where((v) => v.isLive).toList();
      final recorded =
          p.videoClasses.where((v) => !v.isLive).toList();

      return ListView(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
        children: [
          if (live.isNotEmpty) ...[
            const _SubHeader(title: '🔴 Live Now'),
            const SizedBox(height: 10),
            ...live.map((v) => _VideoTile(video: v, isLive: true)),
            const SizedBox(height: 20),
          ],
          const _SubHeader(title: 'Recorded Classes'),
          const SizedBox(height: 10),
          if (recorded.isEmpty)
            Container(
              padding: const EdgeInsets.all(32),
              decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16)),
              child: const Column(children: [
                Icon(Icons.ondemand_video_rounded,
                    size: 48, color: AppColors.textLight),
                SizedBox(height: 12),
                Text('No videos available yet',
                    style: TextStyle(color: AppColors.textMedium)),
              ]),
            )
          else
            ...recorded.map((v) => _VideoTile(video: v)),
        ],
      );
    });
  }
}

class _VideoTile extends StatelessWidget {
  final VideoClass video;
  final bool isLive;
  const _VideoTile({required this.video, this.isLive = false});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => const VideoLecturesScreen())),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            boxShadow: const [AppColors.thinShadow]),
        clipBehavior: Clip.antiAlias,
        child: Column(children: [
          // Thumbnail
          Stack(
            children: [
              Container(
                height: 150,
                color: const Color(0xFF24243E),
                child: Center(
                    child: Icon(
                  isLive
                      ? Icons.videocam_rounded
                      : Icons.play_circle_fill_rounded,
                  color: Colors.white,
                  size: 52,
                )),
              ),
              if (isLive)
                Positioned(
                  top: 10,
                  left: 10,
                  child: Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                        color: Colors.red,
                        borderRadius: BorderRadius.circular(6)),
                    child: const Text('LIVE',
                        style: TextStyle(
                            color: Colors.white,
                            fontSize: 10,
                            fontWeight: FontWeight.bold)),
                  ),
                ),
            ],
          ),
          Padding(
            padding: const EdgeInsets.all(14),
            child: Row(children: [
              Expanded(
                child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(video.title,
                          style: const TextStyle(
                              fontWeight: FontWeight.bold, fontSize: 14)),
                      if (video.durationSeconds > 0) ...[
                        const SizedBox(height: 4),
                        Text(
                          _formatDuration(video.durationSeconds),
                          style: const TextStyle(
                              color: AppColors.textLight, fontSize: 12),
                        ),
                      ],
                    ]),
              ),
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                    color: const Color(0xFF302B63).withOpacity(0.1),
                    borderRadius: BorderRadius.circular(8)),
                child: const Text('Watch',
                    style: TextStyle(
                        color: Color(0xFF302B63),
                        fontWeight: FontWeight.bold,
                        fontSize: 12)),
              ),
            ]),
          ),
        ]),
      ),
    );
  }

  String _formatDuration(int seconds) {
    final m = seconds ~/ 60;
    final s = seconds % 60;
    return '${m}m ${s}s';
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Flashcards Tab
// ─────────────────────────────────────────────────────────────────────────────
class _FlashcardsTab extends StatelessWidget {
  const _FlashcardsTab();

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (context, p, _) {
      if (p.isLoadingFlashcards) {
        return const Center(child: CircularProgressIndicator());
      }

      final sets = p.flashcardSets;

      return ListView(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 100),
        children: [
          // Due for review banner
          if (p.flashcardsDue.isNotEmpty)
            GestureDetector(
              onTap: () => Navigator.push(context,
                  MaterialPageRoute(builder: (_) => const FlashcardsScreen())),
              child: Container(
                margin: const EdgeInsets.only(bottom: 16),
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                      colors: [Color(0xFF1565C0), Color(0xFF0D47A1)]),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Row(children: [
                  const Icon(Icons.schedule_rounded,
                      color: Colors.white, size: 28),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            '${p.flashcardsDue.length} cards due for review',
                            style: const TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.bold,
                                fontSize: 15),
                          ),
                          const Text('Tap to review now',
                              style: TextStyle(
                                  color: Colors.white70, fontSize: 12)),
                        ]),
                  ),
                  const Icon(Icons.chevron_right_rounded, color: Colors.white),
                ]),
              ),
            ),

          const _SubHeader(title: 'Flashcard Sets'),
          const SizedBox(height: 10),
          if (sets.isEmpty)
            Container(
              padding: const EdgeInsets.all(32),
              decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(16)),
              child: const Column(children: [
                Icon(Icons.style_rounded, size: 48, color: AppColors.textLight),
                SizedBox(height: 12),
                Text('No flashcard sets available',
                    style: TextStyle(color: AppColors.textMedium)),
              ]),
            )
          else
            ...sets.map((s) => _FlashcardSetTile(set: s)),
        ],
      );
    });
  }
}

class _FlashcardSetTile extends StatelessWidget {
  final FlashcardSet set;
  const _FlashcardSetTile({required this.set});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => Navigator.push(
          context,
          MaterialPageRoute(
              builder: (_) => FlashcardsScreen(setId: set.id))),
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
                color: const Color(0xFF302B63).withOpacity(0.08),
                borderRadius: BorderRadius.circular(12)),
            child: const Icon(Icons.style_rounded,
                color: Color(0xFF302B63), size: 24),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(set.title,
                      style: const TextStyle(
                          fontWeight: FontWeight.bold, fontSize: 14)),
                  const SizedBox(height: 4),
                  Text('${set.cardCount} cards',
                      style: const TextStyle(
                          color: AppColors.textLight, fontSize: 12)),
                ]),
          ),
          const Icon(Icons.chevron_right_rounded, color: AppColors.textLight),
        ]),
      ),
    );
  }
}

class _SubHeader extends StatelessWidget {
  final String title;
  const _SubHeader({required this.title});
  @override
  Widget build(BuildContext context) => Text(title,
      style: const TextStyle(
          fontSize: 15, fontWeight: FontWeight.w800, color: AppColors.textDark));
}
