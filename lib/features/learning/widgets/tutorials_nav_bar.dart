import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

// ─────────────────────────────────────────────────────────────────────────────
// Tutorials bottom navbar — deep-blue themed, 4 contextual learning tabs
// Shown only when the user is on the Tutorials tab in MainScreen
// ─────────────────────────────────────────────────────────────────────────────

enum TutorialsTab { tutorials, mockTests, progress, bookmarks }

class TutorialsNavBar extends StatelessWidget {
  final TutorialsTab selectedTab;
  final ValueChanged<TutorialsTab> onTabChanged;

  const TutorialsNavBar({
    super.key,
    required this.selectedTab,
    required this.onTabChanged,
  });

  @override
  Widget build(BuildContext context) {
    final isNepali = Localizations.localeOf(context).languageCode == 'ne';

    final items = [
      _TutNavItem(
        tab: TutorialsTab.tutorials,
        icon: Icons.play_circle_outline_rounded,
        activeIcon: Icons.play_circle_rounded,
        label: isNepali ? 'ट्यूटोरियल' : 'Tutorials',
      ),
      _TutNavItem(
        tab: TutorialsTab.mockTests,
        icon: Icons.assignment_outlined,
        activeIcon: Icons.assignment_rounded,
        label: isNepali ? 'परीक्षा' : 'Mock Tests',
      ),
      _TutNavItem(
        tab: TutorialsTab.progress,
        icon: Icons.bar_chart_outlined,
        activeIcon: Icons.bar_chart_rounded,
        label: isNepali ? 'प्रगति' : 'Progress',
      ),
      _TutNavItem(
        tab: TutorialsTab.bookmarks,
        icon: Icons.bookmark_border_rounded,
        activeIcon: Icons.bookmark_rounded,
        label: isNepali ? 'सेभ गरिएको' : 'Saved',
      ),
    ];

    return Container(
      // Outer background — matches system nav bar area
      color: const Color(0xFF0D47A1),
      child: SafeArea(
        top: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(16, 0, 16, 10),
          child: Container(
            height: 66,
            decoration: BoxDecoration(
              color: const Color(0xFF1565C0),
              borderRadius: BorderRadius.circular(22),
              boxShadow: [
                BoxShadow(
                  color: const Color(0xFF0D47A1).withValues(alpha: 0.55),
                  blurRadius: 20,
                  offset: const Offset(0, 6),
                ),
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.18),
                  blurRadius: 6,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Row(
              children: items.map((item) {
                final isActive = selectedTab == item.tab;
                return _TutNavButton(
                  item: item,
                  isActive: isActive,
                  onTap: () {
                    if (!isActive) {
                      HapticFeedback.selectionClick();
                      onTabChanged(item.tab);
                    }
                  },
                );
              }).toList(),
            ),
          ),
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Individual tab button
// ─────────────────────────────────────────────────────────────────────────────
class _TutNavButton extends StatelessWidget {
  final _TutNavItem item;
  final bool isActive;
  final VoidCallback onTap;

  const _TutNavButton({
    required this.item,
    required this.isActive,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: GestureDetector(
        onTap: onTap,
        behavior: HitTestBehavior.opaque,
        child: SizedBox(
          height: 66,
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 220),
            curve: Curves.easeInOut,
            margin: const EdgeInsets.symmetric(horizontal: 4, vertical: 8),
            decoration: BoxDecoration(
              color: isActive
                  ? Colors.white.withValues(alpha: 0.15)
                  : Colors.transparent,
              borderRadius: BorderRadius.circular(14),
            ),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                AnimatedSwitcher(
                  duration: const Duration(milliseconds: 200),
                  transitionBuilder: (child, anim) =>
                      ScaleTransition(scale: anim, child: child),
                  child: Icon(
                    isActive ? item.activeIcon : item.icon,
                    key: ValueKey(isActive),
                    color: isActive
                        ? Colors.white
                        : Colors.white.withValues(alpha: 0.55),
                    size: 24,
                  ),
                ),
                const SizedBox(height: 3),
                AnimatedDefaultTextStyle(
                  duration: const Duration(milliseconds: 200),
                  style: TextStyle(
                    color: isActive
                        ? Colors.white
                        : Colors.white.withValues(alpha: 0.55),
                    fontSize: 10.5,
                    fontWeight:
                        isActive ? FontWeight.w700 : FontWeight.w500,
                    letterSpacing: -0.1,
                  ),
                  child: Text(item.label, maxLines: 1),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Data holder
// ─────────────────────────────────────────────────────────────────────────────
class _TutNavItem {
  final TutorialsTab tab;
  final IconData icon;
  final IconData activeIcon;
  final String label;

  const _TutNavItem({
    required this.tab,
    required this.icon,
    required this.activeIcon,
    required this.label,
  });
}
