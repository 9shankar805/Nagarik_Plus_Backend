import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../models/document_model.dart';
import '../providers/documents_provider.dart';
import 'document_detail_screen.dart';
import 'documents_screen.dart' show DocTypeDef;
import 'add_member_sheet.dart';

// ─────────────────────────────────────────────────────────────────────────────
// MemberListScreen — shows every member for one document type
// e.g. all National IDs: Ram, Shyam, Mother, Sister...
// ─────────────────────────────────────────────────────────────────────────────
class MemberListScreen extends StatelessWidget {
  final DocTypeDef typeDef;
  final List<DocumentModel> members;

  const MemberListScreen({super.key, required this.typeDef, required this.members});

  @override
  Widget build(BuildContext context) {
    final color = typeDef.color;
    final now   = DateTime.now();

    return Scaffold(
      backgroundColor: const Color(0xFFF2F5FA),
      body: CustomScrollView(
        slivers: [
          // ── Gradient header ──────────────────────────────────────────────
          SliverAppBar(
            expandedHeight: 160,
            pinned: true,
            elevation: 0,
            backgroundColor: color,
            foregroundColor: Colors.white,
            flexibleSpace: FlexibleSpaceBar(
              background: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [color, color.withOpacity(0.75)],
                    begin: Alignment.topLeft, end: Alignment.bottomRight,
                  ),
                ),
                child: SafeArea(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(20, 48, 20, 16),
                    child: Row(children: [
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.2),
                            borderRadius: BorderRadius.circular(14)),
                        child: Icon(typeDef.icon, color: Colors.white, size: 28),
                      ),
                      const SizedBox(width: 16),
                      Expanded(child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(typeDef.label,
                              style: const TextStyle(color: Colors.white,
                                  fontSize: 20, fontWeight: FontWeight.w800)),
                          const SizedBox(height: 4),
                          Text(
                            members.isEmpty
                                ? 'No documents yet — add your first member'
                                : '${members.length} member${members.length == 1 ? '' : 's'}',
                            style: const TextStyle(color: Colors.white70, fontSize: 13),
                          ),
                        ],
                      )),
                    ]),
                  ),
                ),
              ),
            ),
          ),

          // ── Empty state ──────────────────────────────────────────────────
          if (members.isEmpty)
            SliverFillRemaining(
              child: Center(
                child: Padding(
                  padding: const EdgeInsets.all(40),
                  child: Column(mainAxisSize: MainAxisSize.min, children: [
                    Container(
                      padding: const EdgeInsets.all(24),
                      decoration: BoxDecoration(
                          color: color.withOpacity(0.08),
                          shape: BoxShape.circle),
                      child: Icon(typeDef.icon, color: color, size: 48),
                    ),
                    const SizedBox(height: 20),
                    Text('No ${typeDef.label} added yet',
                        style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
                    const SizedBox(height: 8),
                    const Text(
                      'Tap "Add Member" to upload a document\nfor yourself or a family member.',
                      textAlign: TextAlign.center,
                      style: TextStyle(color: AppColors.textMedium, fontSize: 13, height: 1.5),
                    ),
                    const SizedBox(height: 28),
                    ElevatedButton.icon(
                      onPressed: () => _showAddSheet(context),
                      icon: const Icon(Icons.person_add_rounded),
                      label: const Text('Add Member'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: color,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(horizontal: 28, vertical: 14),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                      ),
                    ),
                  ]),
                ),
              ),
            )
          else ...[
            // ── Info bar ──────────────────────────────────────────────────
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
                child: Row(children: [
                  Text('${members.length} Member${members.length == 1 ? '' : 's'}',
                      style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w800,
                          color: AppColors.textDark)),
                  const Spacer(),
                  // Expiry summary chips
                  ...() {
                    final expired  = members.where((d) => d.expiryDate != null && d.expiryDate!.isBefore(now)).length;
                    final expiring = members.where((d) => d.expiryDate != null &&
                        !d.expiryDate!.isBefore(now) &&
                        d.expiryDate!.difference(now).inDays < 90).length;
                    return [
                      if (expired > 0)  _MiniChip('$expired Expired',  AppColors.danger),
                      if (expiring > 0) _MiniChip('$expiring Expiring', AppColors.warning),
                    ];
                  }(),
                ]),
              ),
            ),

            // ── Member list ───────────────────────────────────────────────
            SliverPadding(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 20),
              sliver: SliverList(
                delegate: SliverChildBuilderDelegate(
                  (_, i) => _MemberTile(
                    doc: members[i],
                    color: color,
                    onTap: () => Navigator.push(context,
                        MaterialPageRoute(builder: (_) =>
                            DocumentDetailScreen(document: members[i]))),
                    onDelete: () => _confirmDelete(context, members[i]),
                  ),
                  childCount: members.length,
                ),
              ),
            ),

            // ── Bottom padding for FAB ────────────────────────────────────
            const SliverToBoxAdapter(child: SizedBox(height: 80)),
          ],
        ],
      ),

      // ── Add member FAB ───────────────────────────────────────────────────
      floatingActionButton: FloatingActionButton.extended(
        backgroundColor: color,
        foregroundColor: Colors.white,
        icon: const Icon(Icons.person_add_rounded),
        label: const Text('Add Member', style: TextStyle(fontWeight: FontWeight.w700)),
        onPressed: () => _showAddSheet(context),
      ),
    );
  }

  void _showAddSheet(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => AddMemberSheet(typeDef: typeDef),
    ).then((_) => context.read<DocumentsProvider>().refreshDocuments());
  }

  void _confirmDelete(BuildContext context, DocumentModel doc) {
    showDialog(
      context: context,
      builder: (_) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('Delete Document', style: TextStyle(fontWeight: FontWeight.w800)),
        content: Text(
            'Remove ${doc.holderName.isNotEmpty ? doc.holderName : "this member"}'
            "'s ${typeDef.label}?"),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('Cancel')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.danger, foregroundColor: Colors.white),
            onPressed: () async {
              Navigator.pop(context);
              final id = int.tryParse(doc.id);
              if (id != null) {
                await context.read<DocumentsProvider>().deleteDoc(id);
              }
              if (context.mounted) Navigator.pop(context); // pop member list
            },
            child: const Text('Delete'),
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Member row tile
// ─────────────────────────────────────────────────────────────────────────────
class _MemberTile extends StatelessWidget {
  final DocumentModel doc;
  final Color color;
  final VoidCallback onTap;
  final VoidCallback onDelete;
  const _MemberTile({required this.doc, required this.color,
      required this.onTap, required this.onDelete});

  @override
  Widget build(BuildContext context) {
    final now       = DateTime.now();
    final isExpired  = doc.expiryDate != null && doc.expiryDate!.isBefore(now);
    final isExpiring = doc.expiryDate != null && !isExpired &&
        doc.expiryDate!.difference(now).inDays < 90;
    final holder    = doc.holderName.isNotEmpty ? doc.holderName : 'Unknown';
    final initial   = holder[0].toUpperCase();

    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: isExpired
              ? Border.all(color: AppColors.danger, width: 1.5)
              : isExpiring
                  ? Border.all(color: AppColors.warning, width: 1.5)
                  : null,
          boxShadow: const [AppColors.thinShadow],
        ),
        child: Row(children: [
          // Avatar circle with initial
          Container(
            width: 46, height: 46,
            decoration: BoxDecoration(
                color: color.withOpacity(0.12), shape: BoxShape.circle),
            child: Center(child: Text(initial,
                style: TextStyle(color: color, fontSize: 18, fontWeight: FontWeight.w800))),
          ),
          const SizedBox(width: 14),

          // Name + subtitle
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(holder,
                style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14,
                    color: AppColors.textDark)),
            const SizedBox(height: 3),
            Text(doc.subtitle,
                style: const TextStyle(color: AppColors.textLight, fontSize: 12),
                maxLines: 1, overflow: TextOverflow.ellipsis),
            if (doc.expiryDate != null) ...[
              const SizedBox(height: 4),
              Row(children: [
                Icon(Icons.calendar_today_rounded, size: 10,
                    color: isExpired ? AppColors.danger
                        : isExpiring ? AppColors.warning : AppColors.textLight),
                const SizedBox(width: 3),
                Text(
                  'Exp ${doc.expiryDate!.year}/${doc.expiryDate!.month.toString().padLeft(2, '0')}',
                  style: TextStyle(fontSize: 10, fontWeight: FontWeight.w600,
                      color: isExpired ? AppColors.danger
                          : isExpiring ? AppColors.warning : AppColors.textLight),
                ),
              ]),
            ],
          ])),

          // Status badge
          Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
            if (isExpired)
              const _MiniChip('Expired', AppColors.danger)
            else if (isExpiring)
              const _MiniChip('Expiring', AppColors.warning)
            else if (doc.isUploaded)
              const _MiniChip('✓ Saved', AppColors.success)
            else
              const _MiniChip('No file', Colors.grey),
            const SizedBox(height: 8),
            GestureDetector(
              onTap: onDelete,
              child: const Icon(Icons.delete_outline_rounded,
                  size: 18, color: AppColors.textLight),
            ),
          ]),
        ]),
      ),
    );
  }
}

class _MiniChip extends StatelessWidget {
  final String label;
  final Color color;
  const _MiniChip(this.label, this.color);
  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 3),
    decoration: BoxDecoration(
        color: color.withOpacity(0.12), borderRadius: BorderRadius.circular(6)),
    child: Text(label,
        style: TextStyle(color: color, fontSize: 9, fontWeight: FontWeight.w700)),
  );
}
