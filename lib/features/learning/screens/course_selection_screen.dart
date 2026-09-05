import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../providers/learning_provider.dart';
// ignore_for_file: unused_element

// ─────────────────────────────────────────────────────────────────────────────
// CourseSelectionScreen — Ambition Guru-style subject/course gate
// Shown when no active category is selected. User picks a subject, which
// calls provider.setActiveCategory() and reveals the tutorials content.
// ─────────────────────────────────────────────────────────────────────────────

class CourseSelectionScreen extends StatefulWidget {
  const CourseSelectionScreen({super.key});

  @override
  State<CourseSelectionScreen> createState() => _CourseSelectionScreenState();
}

class _CourseSelectionScreenState extends State<CourseSelectionScreen>
    with SingleTickerProviderStateMixin {
  final TextEditingController _search = TextEditingController();
  String _query = '';

  @override
  void initState() {
    super.initState();
    _search.addListener(() => setState(() => _query = _search.text.trim()));
  }

  @override
  void dispose() {
    _search.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.dark,
    ));
    return Consumer<LearningProvider>(
      builder: (context, provider, _) {
        return CustomScrollView(
          slivers: [
            _buildHeroSliver(context),
            _buildSearchSliver(),
            _buildPopularSliver(context, provider),
            _buildAllCoursesSliver(context, provider),
            const SliverToBoxAdapter(child: SizedBox(height: 120)),
          ],
        );
      },
    );
  }

  // ── Simple Header ──────────────────────────────────────────────────────────
  Widget _buildHeroSliver(BuildContext context) {
    final isNe = Localizations.localeOf(context).languageCode == 'ne';
    
    return SliverAppBar(
      pinned: true,
      elevation: 0,
      backgroundColor: const Color(0xFFF2F5FA),
      foregroundColor: const Color(0xFF1A2B4A),
      title: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(
              color: AppColors.primary.withValues(alpha: 0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: const Icon(Icons.school_rounded, color: AppColors.primary, size: 20),
          ),
          const SizedBox(width: 12),
          Text(
            isNe ? 'सिकाई पोर्टल' : 'Learning Portal',
            style: const TextStyle(
              color: Color(0xFF1A2B4A),
              fontWeight: FontWeight.w900, 
              fontSize: 20, 
              letterSpacing: -0.5
            ),
          ),
        ],
      ),
      actions: [
        Consumer<LearningProvider>(
          builder: (context, provider, _) {
            return Container(
              margin: const EdgeInsets.only(right: 20, top: 8, bottom: 8),
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.05),
                    blurRadius: 8,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Icon(Icons.local_fire_department_rounded, color: Colors.deepOrange, size: 16),
                  const SizedBox(width: 4),
                  Text(
                    '${provider.streakDays}',
                    style: const TextStyle(color: Colors.black87, fontSize: 14, fontWeight: FontWeight.w800),
                  ),
                ],
              ),
            );
          },
        ),
      ],
    );
  }

  // ── Search bar ─────────────────────────────────────────────────────────────
  Widget _buildSearchSliver() {
    final isNe = Localizations.localeOf(context).languageCode == 'ne';
    return SliverToBoxAdapter(
      child: Padding(
        padding: const EdgeInsets.only(top: 16, bottom: 12),
        child: Container(
          margin: const EdgeInsets.symmetric(horizontal: 20),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: const Color(0xFFE2E8F0)),
            boxShadow: [
              BoxShadow(
                color: const Color(0xFF0F172A).withValues(alpha: 0.06),
                blurRadius: 24,
                offset: const Offset(0, 8),
              ),
            ],
          ),
          child: TextField(
            controller: _search,
            textInputAction: TextInputAction.search,
            style: const TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: Color(0xFF1A2B4A)),
            decoration: InputDecoration(
              hintText: isNe
                  ? 'विषय खोज्नुहोस्...'
                  : 'Search subjects...',
              hintStyle: const TextStyle(
                  color: Color(0xFFADB9C9), fontSize: 14),
              prefixIcon: const Icon(Icons.search_rounded,
                  color: AppColors.primary, size: 18),
              suffixIcon: _query.isEmpty
                  ? null
                  : IconButton(
                      icon: const Icon(Icons.close_rounded,
                          color: Color(0xFFADB9C9), size: 16),
                      onPressed: () {
                        _search.clear();
                        FocusScope.of(context).unfocus();
                      },
                    ),
              border: InputBorder.none,
              contentPadding: const EdgeInsets.symmetric(
                  horizontal: 16, vertical: 16),
            ),
          ),
        ),
      ),
    );
  }

  // ── Popular / Featured row ──────────────────────────────────────────────────
  Widget _buildPopularSliver(
      BuildContext context, LearningProvider provider) {
    final isNe = Localizations.localeOf(context).languageCode == 'ne';

    // Popular = first 4 static subjects; if API has categories, use those
    final popular = _allSubjects(provider)
        .where((s) => s.isFeatured)
        .toList();

    if (popular.isEmpty) return const SliverToBoxAdapter(child: SizedBox());

    // Filter by search
    final filtered = _query.isEmpty
        ? popular
        : popular.where((s) =>
            s.label.toLowerCase().contains(_query.toLowerCase()) ||
            s.labelNe.contains(_query)).toList();

    if (filtered.isEmpty) return const SliverToBoxAdapter(child: SizedBox());

    return SliverToBoxAdapter(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 0, 20, 12),
            child: Row(children: [
              Container(width: 4, height: 16,
                decoration: BoxDecoration(
                    color: AppColors.primary,
                    borderRadius: BorderRadius.circular(2))),
              const SizedBox(width: 8),
              Text(
                isNe ? 'लोकप्रिय विषयहरू' : 'Popular Subjects',
                style: const TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF1A2B4A)),
              ),
            ]),
          ),
          SizedBox(
            height: 130,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.only(left: 16, right: 8),
              itemCount: filtered.length,
              itemBuilder: (_, i) => _PopularCard(
                subject: filtered[i],
                isNe: isNe,
                onTap: () => _selectSubject(context, provider, filtered[i]),
              ),
            ),
          ),
          const SizedBox(height: 24),
        ],
      ),
    );
  }

  // ── All courses grid ────────────────────────────────────────────────────────
  Widget _buildAllCoursesSliver(
      BuildContext context, LearningProvider provider) {
    final isNe = Localizations.localeOf(context).languageCode == 'ne';
    final all = _allSubjects(provider);

    final filtered = _query.isEmpty
        ? all
        : all.where((s) =>
            s.label.toLowerCase().contains(_query.toLowerCase()) ||
            s.labelNe.contains(_query)).toList();

    return SliverToBoxAdapter(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(children: [
              Container(width: 4, height: 16,
                decoration: BoxDecoration(
                    color: AppColors.primary,
                    borderRadius: BorderRadius.circular(2))),
              const SizedBox(width: 8),
              Text(
                isNe ? 'सबै विषयहरू' : 'All Subjects',
                style: const TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF1A2B4A)),
              ),
              const Spacer(),
              if (provider.isLoadingCategories)
                const SizedBox(
                    width: 16, height: 16,
                    child: CircularProgressIndicator(strokeWidth: 2)),
            ]),
            const SizedBox(height: 14),
            if (filtered.isEmpty)
              _EmptySearch(query: _query, isNe: isNe)
            else
              GridView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                gridDelegate:
                    const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  childAspectRatio: 1.6,
                  crossAxisSpacing: 12,
                  mainAxisSpacing: 12,
                ),
                itemCount: filtered.length,
                itemBuilder: (_, i) => _SubjectCard(
                  subject: filtered[i],
                  isNe: isNe,
                  onTap: () =>
                      _selectSubject(context, provider, filtered[i]),
                ),
              ),
          ],
        ),
      ),
    );
  }

  // ── Subject selection handler ───────────────────────────────────────────────
  void _selectSubject(
      BuildContext context, LearningProvider provider, _Subject s) {
    HapticFeedback.selectionClick();
    FocusScope.of(context).unfocus();
    provider.setActiveCategory(s.slug);
  }

  // ── Build unified subject list (static fallbacks + API categories) ──────────
  List<_Subject> _allSubjects(LearningProvider provider) {
    // Static built-in subjects always present (Nagarik+ context)
    final statics = <_Subject>[
      _Subject(
        slug: 'driving-license',
        label: 'Driving License',
        labelNe: 'सवारी अनुमतिपत्र',
        subtitle: 'Prepare for your license exam',
        subtitleNe: 'ड्राइभिङ लाइसेन्स तयारी',
        icon: Icons.directions_car_rounded,
        color: const Color(0xFF1565C0),
        chapterCount: 12,
        mockTestCount: 8,
        isFeatured: true,
        badge: 'Popular',
      ),
      _Subject(
        slug: 'loksewa',
        label: 'Loksewa (PSC)',
        labelNe: 'लोक सेवा आयोग',
        subtitle: 'Public Service Commission prep',
        subtitleNe: 'लोक सेवा परीक्षा तयारी',
        icon: Icons.account_balance_rounded,
        color: const Color(0xFF2E7D32),
        chapterCount: 20,
        mockTestCount: 15,
        isFeatured: true,
        badge: 'Trending',
      ),
      _Subject(
        slug: 'traffic-rules',
        label: 'Traffic Rules',
        labelNe: 'यातायात नियम',
        subtitle: 'Road signs & regulations',
        subtitleNe: 'सडक संकेत र नियमहरू',
        icon: Icons.traffic_rounded,
        color: const Color(0xFFF57F17),
        chapterCount: 8,
        mockTestCount: 5,
        isFeatured: true,
        badge: null,
      ),
      _Subject(
        slug: 'citizenship',
        label: 'Citizenship',
        labelNe: 'नागरिकता',
        subtitle: 'Citizenship test preparation',
        subtitleNe: 'नागरिकता परीक्षा तयारी',
        icon: Icons.badge_rounded,
        color: const Color(0xFF6A1B9A),
        chapterCount: 10,
        mockTestCount: 6,
        isFeatured: true,
        badge: 'New',
      ),
      _Subject(
        slug: 'road-signs',
        label: 'Road Signs',
        labelNe: 'सडक संकेतहरू',
        subtitle: 'Learn all road signs',
        subtitleNe: 'सबै सडक संकेत सिक्नुहोस्',
        icon: Icons.sign_language_rounded,
        color: const Color(0xFF00838F),
        chapterCount: 6,
        mockTestCount: 4,
        isFeatured: false,
        badge: null,
      ),
      _Subject(
        slug: 'general-knowledge',
        label: 'General Knowledge',
        labelNe: 'सामान्य ज्ञान',
        subtitle: 'Nepal GK & Current Affairs',
        subtitleNe: 'नेपाल सामान्य ज्ञान',
        icon: Icons.lightbulb_rounded,
        color: const Color(0xFFD32F2F),
        chapterCount: 15,
        mockTestCount: 10,
        isFeatured: false,
        badge: null,
      ),
    ];

    // Merge API categories — if a slug already exists in statics, skip it
    final staticSlugs = statics.map((s) => s.slug).toSet();
    final apiSubjects = provider.categories
        .where((c) => !staticSlugs.contains(c.slug))
        .map((c) => _Subject(
              slug: c.slug,
              label: c.title,
              labelNe: c.titleNp ?? c.title,
              subtitle: c.description ?? '',
              subtitleNe: c.description ?? '',
              icon: Icons.menu_book_rounded,
              color: _colorFromCode(c.colorCode),
              chapterCount: c.chapterCount,
              mockTestCount: c.mockTestCount,
              isFeatured: false,
              badge: null,
            ))
        .toList();

    return [...statics, ...apiSubjects];
  }

  Color _colorFromCode(String? code) {
    if (code == null) return AppColors.primary;
    try {
      return Color(int.parse(code.replaceAll('#', '0xFF')));
    } catch (_) {
      return AppColors.primary;
    }
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Data model for a subject card
// ─────────────────────────────────────────────────────────────────────────────
class _Subject {
  final String slug;
  final String label;
  final String labelNe;
  final String subtitle;
  final String subtitleNe;
  final IconData icon;
  final Color color;
  final int chapterCount;
  final int mockTestCount;
  final bool isFeatured;
  final String? badge;

  const _Subject({
    required this.slug,
    required this.label,
    required this.labelNe,
    required this.subtitle,
    required this.subtitleNe,
    required this.icon,
    required this.color,
    required this.chapterCount,
    required this.mockTestCount,
    required this.isFeatured,
    this.badge,
  });
}

// ─────────────────────────────────────────────────────────────────────────────
// Hero stat chip
// ─────────────────────────────────────────────────────────────────────────────
class _HeroStat extends StatelessWidget {
  final String value;
  final String label;
  const _HeroStat(this.value, this.label);

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Text(value,
            style: const TextStyle(
                color: Colors.white,
                fontSize: 16,
                fontWeight: FontWeight.w800,
                letterSpacing: -0.5)),
        const SizedBox(height: 2),
        Text(label,
            style: TextStyle(
                color: Colors.white.withValues(alpha: 0.8),
                fontSize: 11,
                fontWeight: FontWeight.w500)),
      ],
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Decorative circle
// ─────────────────────────────────────────────────────────────────────────────
class _Circle extends StatelessWidget {
  final double size;
  final Color color;
  const _Circle(this.size, this.color);

  @override
  Widget build(BuildContext context) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(shape: BoxShape.circle, color: color),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Popular horizontal card (wide, colourful gradient)
// ─────────────────────────────────────────────────────────────────────────────
class _PopularCard extends StatelessWidget {
  final _Subject subject;
  final bool isNe;
  final VoidCallback onTap;

  const _PopularCard({
    required this.subject,
    required this.isNe,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final label = isNe ? subject.labelNe : subject.label;
    final sub   = isNe ? subject.subtitleNe : subject.subtitle;
    final c     = subject.color;

    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 145,
        margin: const EdgeInsets.only(right: 14),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: [c, Color.lerp(c, Colors.black, 0.3)!],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
          borderRadius: BorderRadius.circular(24),
          boxShadow: [
            BoxShadow(
              color: c.withValues(alpha: 0.35),
              blurRadius: 16,
              offset: const Offset(0, 6),
            ),
          ],
        ),
        child: Stack(
          clipBehavior: Clip.hardEdge,
          children: [
            // Watermark icon for premium look
            Positioned(
              right: -20,
              bottom: -20,
              child: Icon(subject.icon, size: 70, color: Colors.white.withValues(alpha: 0.1)),
            ),
            Padding(
              padding: const EdgeInsets.all(12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
            Row(children: [
              Container(
                width: 32, height: 32,
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.20),
                  borderRadius: BorderRadius.circular(11),
                ),
                child: Icon(subject.icon, color: Colors.white, size: 16),
              ),
              const Spacer(),
              if (subject.badge != null)
                Container(
                  padding: const EdgeInsets.symmetric(
                      horizontal: 8, vertical: 3),
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.25),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Text(subject.badge!,
                      style: const TextStyle(
                          color: Colors.white,
                          fontSize: 9,
                          fontWeight: FontWeight.w700)),
                ),
            ]),
            const Spacer(),
                Text(label,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                        color: Colors.white,
                        fontSize: 13,
                        fontWeight: FontWeight.w800)),
                const SizedBox(height: 4),
                Text(sub,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                        color: Colors.white.withValues(alpha: 0.8),
                        fontSize: 11.5,
                        fontWeight: FontWeight.w500)),
                const Spacer(),
                Row(children: [
                  _PillStat(Icons.play_circle_outline_rounded,
                      '${subject.chapterCount}'),
                  const SizedBox(width: 12),
                  _PillStat(
                      Icons.assignment_outlined, '${subject.mockTestCount}'),
                ]),
              ],
            ),
          ),
          ],
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// All-subjects 2-column grid card
// ─────────────────────────────────────────────────────────────────────────────
class _SubjectCard extends StatelessWidget {
  final _Subject subject;
  final bool isNe;
  final VoidCallback onTap;

  const _SubjectCard({
    required this.subject,
    required this.isNe,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final label = isNe ? subject.labelNe : subject.label;
    final sub   = isNe ? subject.subtitleNe : subject.subtitle;
    final c     = subject.color;

    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: const Color(0xFFF1F5F9)),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF0F172A).withValues(alpha: 0.04),
              blurRadius: 16,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 36, height: 36,
                  decoration: BoxDecoration(
                    color: c.withValues(alpha: 0.10),
                    shape: BoxShape.circle, // Rounded circle for icons looks more modern
                  ),
                  child: Icon(subject.icon, color: c, size: 18),
                ),
                const Spacer(),
                if (subject.badge != null)
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 7, vertical: 3),
                    decoration: BoxDecoration(
                      color: c.withValues(alpha: 0.12),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(subject.badge!,
                        style: TextStyle(
                            color: c,
                            fontSize: 9,
                            fontWeight: FontWeight.w700)),
                  ),
              ],
            ),
            const Spacer(),
            Text(label,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF1E293B))),
            const SizedBox(height: 3),
            Text(sub,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(
                    fontSize: 11,
                    color: Color(0xFF64748B),
                    fontWeight: FontWeight.w500)),
            const Spacer(),
            // Count pills
            Row(children: [
              _PillStatDark(c, Icons.play_circle_outline_rounded,
                  '${subject.chapterCount} ch'),
              const SizedBox(width: 8),
              _PillStatDark(c, Icons.assignment_outlined,
                  '${subject.mockTestCount} tests'),
            ]),
          ],
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Pill stat for popular card (white-on-transparent)
// ─────────────────────────────────────────────────────────────────────────────
class _PillStat extends StatelessWidget {
  final IconData icon;
  final String value;
  const _PillStat(this.icon, this.value);

  @override
  Widget build(BuildContext context) {
    return Row(mainAxisSize: MainAxisSize.min, children: [
      Icon(icon, color: Colors.white.withValues(alpha: 0.8), size: 12),
      const SizedBox(width: 3),
      Text(value,
          style: TextStyle(
              color: Colors.white.withValues(alpha: 0.85),
              fontSize: 10.5,
              fontWeight: FontWeight.w600)),
    ]);
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Pill stat for grid card (coloured-on-white)
// ─────────────────────────────────────────────────────────────────────────────
class _PillStatDark extends StatelessWidget {
  final Color color;
  final IconData icon;
  final String value;
  const _PillStatDark(this.color, this.icon, this.value);

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.10),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(mainAxisSize: MainAxisSize.min, children: [
        Icon(icon, color: color, size: 11),
        const SizedBox(width: 3),
        Text(value,
            style: TextStyle(
                color: color,
                fontSize: 10,
                fontWeight: FontWeight.w700)),
      ]),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Empty search state
// ─────────────────────────────────────────────────────────────────────────────
class _EmptySearch extends StatelessWidget {
  final String query;
  final bool isNe;
  const _EmptySearch({required this.query, required this.isNe});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(top: 40),
      child: Center(
        child: Column(children: [
          Icon(Icons.search_off_rounded,
              size: 52, color: Colors.grey.shade300),
          const SizedBox(height: 12),
          Text(
            isNe
                ? '"$query" को लागि कुनै विषय फेला परेन'
                : 'No subjects found for "$query"',
            textAlign: TextAlign.center,
            style: const TextStyle(
                color: Color(0xFF8A9BB0),
                fontSize: 13,
                fontWeight: FontWeight.w600),
          ),
        ]),
      ),
    );
  }
}
