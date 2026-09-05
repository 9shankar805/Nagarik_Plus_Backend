import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';
import 'package:provider/provider.dart';
import '../../scanner/screens/vault_screen.dart';
import '../models/document_model.dart';
import '../providers/documents_provider.dart';
import '../services/local_document_store.dart';
import 'member_list_screen.dart';

// ─────────────────────────────────────────────────────────────────────────────
// Master document-type catalogue — always shown whether uploaded or not
// ─────────────────────────────────────────────────────────────────────────────
class DocTypeDef {
  final String type;
  final String label;
  final String category;
  final IconData icon;
  final Color color;
  const DocTypeDef({required this.type, required this.label,
      required this.category, required this.icon, required this.color});
}

const List<DocTypeDef> allDocTypes = [
  DocTypeDef(type:'national_id',          label:'National ID',          category:'Identity', icon:Icons.badge_rounded,                       color:Color(0xFF1565C0)),
  DocTypeDef(type:'citizenship',           label:'Citizenship',          category:'Identity', icon:Icons.assignment_ind_rounded,              color:Color(0xFF6A1B9A)),
  DocTypeDef(type:'passport',              label:'Passport',             category:'Identity', icon:Icons.book_rounded,                        color:Color(0xFF00695C)),
  DocTypeDef(type:'voter_id',              label:'Voter ID',             category:'Identity', icon:Icons.how_to_vote_rounded,                 color:Color(0xFFB71C1C)),
  DocTypeDef(type:'driving_license',       label:'Driving License',      category:'Vehicle',  icon:Icons.drive_eta_rounded,                   color:Color(0xFF2E7D32)),
  DocTypeDef(type:'vehicle_bluebook',      label:'Vehicle Bluebook',     category:'Vehicle',  icon:Icons.directions_car_rounded,              color:Color(0xFF1565C0)),
  DocTypeDef(type:'pan',                   label:'PAN Card',             category:'Finance',  icon:Icons.receipt_long_rounded,                color:Color(0xFFF57F17)),
  DocTypeDef(type:'insurance',             label:'Insurance',            category:'Finance',  icon:Icons.security_rounded,                    color:Color(0xFF00838F)),
  DocTypeDef(type:'property',              label:'Property Deed',        category:'Finance',  icon:Icons.home_work_rounded,                   color:Color(0xFF4527A0)),
  DocTypeDef(type:'birth_certificate',     label:'Birth Certificate',    category:'Vital',    icon:Icons.child_care_rounded,                  color:Color(0xFF558B2F)),
  DocTypeDef(type:'marriage_certificate',  label:'Marriage Certificate', category:'Vital',    icon:Icons.favorite_rounded,                    color:Color(0xFFC62828)),
  DocTypeDef(type:'death_certificate',     label:'Death Certificate',    category:'Vital',    icon:Icons.sentiment_very_dissatisfied_rounded, color:Color(0xFF37474F)),
  DocTypeDef(type:'migration_certificate', label:'Migration Cert.',      category:'Vital',    icon:Icons.transfer_within_a_station_rounded,   color:Color(0xFF00695C)),
  DocTypeDef(type:'medical',               label:'Medical Record',       category:'Medical',  icon:Icons.medical_services_rounded,            color:Color(0xFF00838F)),
  DocTypeDef(type:'academic',              label:'Academic Cert.',       category:'Academic', icon:Icons.school_rounded,                      color:Color(0xFF5D4037)),
  DocTypeDef(type:'nea_bill',              label:'NEA Bill',             category:'Other',    icon:Icons.electric_bolt_rounded,               color:Color(0xFFF9A825)),
  DocTypeDef(type:'press_pass',            label:'Press Pass',           category:'Other',    icon:Icons.newspaper_rounded,                   color:Color(0xFF1565C0)),
  DocTypeDef(type:'other',                 label:'Other Document',       category:'Other',    icon:Icons.insert_drive_file_rounded,           color:Color(0xFF546E7A)),
];

// ─────────────────────────────────────────────────────────────────────────────
// DocumentsScreen
// ─────────────────────────────────────────────────────────────────────────────
class DocumentsScreen extends StatefulWidget {
  const DocumentsScreen({super.key});
  @override
  State<DocumentsScreen> createState() => _DocumentsScreenState();
}

class _DocumentsScreenState extends State<DocumentsScreen> {
  int    _selCat = 0;
  String _search = '';
  bool   _showSyncBanner = false;

  static const _cats = [
    ('All',      Icons.grid_view_rounded),
    ('Identity', Icons.badge_rounded),
    ('Vehicle',  Icons.drive_eta_rounded),
    ('Finance',  Icons.account_balance_rounded),
    ('Vital',    Icons.family_restroom_rounded),
    ('Medical',  Icons.medical_services_rounded),
    ('Academic', Icons.school_rounded),
    ('Other',    Icons.more_horiz_rounded),
  ];

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final p = context.read<DocumentsProvider>();
      await p.initSync();
      await p.loadDocuments();
      // Show consent banner if user has never been prompted
      final prompted = await LocalDocumentStore.instance.hasBeenPrompted();
      if (!prompted && mounted) {
        setState(() => _showSyncBanner = true);
      }
    });
  }

  Map<String, List<DocumentModel>> _grouped(List<DocumentModel> docs) {
    final map = <String, List<DocumentModel>>{};
    for (final d in docs) { (map[d.type] ??= []).add(d); }
    return map;
  }

  List<DocTypeDef> _visibleTypes() {
    final cat = _cats[_selCat].$1;
    var list = _selCat == 0 ? allDocTypes
        : allDocTypes.where((d) => d.category == cat).toList();
    if (_search.trim().isNotEmpty) {
      final q = _search.toLowerCase();
      list = list.where((d) =>
          d.label.toLowerCase().contains(q) || d.type.contains(q)).toList();
    }
    return list;
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<DocumentsProvider>(builder: (ctx, p, _) {
      final syncOn  = p.syncEnabled;
      final grouped = _grouped(p.documents);
      final types   = _visibleTypes();
      final uploaded = allDocTypes.where((d) => (grouped[d.type]?.isNotEmpty ?? false)).length;
      final missing  = allDocTypes.length - uploaded;
      final expiring = p.expiringDocuments.length;

      return Scaffold(
        backgroundColor: const Color(0xFFF2F5FA),
        appBar: AppBar(
          backgroundColor: AppColors.primary,
          foregroundColor: Colors.white,
          elevation: 0,
          title: const Text('Digital Locker',
              style: TextStyle(fontWeight: FontWeight.w800)),
          centerTitle: true,
          leading: IconButton(
            icon: const Icon(Icons.arrow_back_rounded),
            onPressed: () => Navigator.pop(context),
          ),
          actions: [
            // Sync toggle chip in app bar
            _SyncChip(
              enabled: syncOn,
              onToggle: () => _handleSyncToggle(context, p, !syncOn),
            ),
            IconButton(
              icon: const Icon(Icons.folder_special_rounded),
              tooltip: 'Scan Vault',
              onPressed: () => Navigator.push(ctx,
                  MaterialPageRoute(builder: (_) => const VaultScreen())),
            ),
          ],
        ),
        body: Column(children: [

          // ── First-run consent banner ─────────────────────────────────
          if (_showSyncBanner)
            _SyncConsentBanner(
              onEnable: () async {
                setState(() => _showSyncBanner = false);
                await p.setSyncEnabled(true);
              },
              onDismiss: () async {
                setState(() => _showSyncBanner = false);
                await LocalDocumentStore.instance.markPrompted();
                // Keep sync OFF — user chose local only
              },
            ),

          // ── Sync-off notice bar ──────────────────────────────────────
          if (!syncOn && !_showSyncBanner)
            _SyncOffBar(onEnable: () => _handleSyncToggle(ctx, p, true)),

          // ── Stats banner ─────────────────────────────────────────────
          _Banner(
            uploaded: uploaded,
            missing: missing,
            expiring: expiring,
            syncOn: syncOn,
          ),

          // ── Search ───────────────────────────────────────────────────
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: TextField(
              onChanged: (v) => setState(() => _search = v),
              decoration: InputDecoration(
                hintText: 'Search document type…',
                hintStyle: const TextStyle(color: AppColors.textLight, fontSize: 13),
                prefixIcon: const Icon(Icons.search_rounded,
                    color: AppColors.textLight, size: 20),
                filled: true, fillColor: Colors.white,
                contentPadding:
                    const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(12),
                    borderSide: BorderSide.none),
              ),
            ),
          ),

          // ── Category pills ────────────────────────────────────────────
          SizedBox(
            height: 42,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.fromLTRB(16, 10, 16, 0),
              itemCount: _cats.length,
              itemBuilder: (_, i) {
                final active = _selCat == i;
                return GestureDetector(
                  onTap: () => setState(() => _selCat = i),
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
                    margin: const EdgeInsets.only(right: 8),
                    padding: const EdgeInsets.symmetric(
                        horizontal: 14, vertical: 4),
                    decoration: BoxDecoration(
                      color: active ? AppColors.primary : Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(
                          color: active
                              ? AppColors.primary
                              : AppColors.divider),
                    ),
                    child: Text(_cats[i].$1,
                        style: TextStyle(
                            color: active
                                ? Colors.white
                                : AppColors.textMedium,
                            fontSize: 12,
                            fontWeight: active
                                ? FontWeight.w700
                                : FontWeight.w500)),
                  ),
                );
              },
            ),
          ),
          const SizedBox(height: 8),

          // ── Grid ──────────────────────────────────────────────────────
          Expanded(
            child: RefreshIndicator(
              onRefresh: p.refreshDocuments,
              color: AppColors.primary,
              child: p.status == DocumentsStatus.loading &&
                      p.documents.isEmpty
                  ? const Center(child: CircularProgressIndicator())
                  : GridView.builder(
                      padding:
                          const EdgeInsets.fromLTRB(16, 4, 16, 100),
                      gridDelegate:
                          const SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: 2,
                        childAspectRatio: 1.18,
                        crossAxisSpacing: 12,
                        mainAxisSpacing: 12,
                      ),
                      itemCount: types.length,
                      itemBuilder: (_, i) {
                        final def     = types[i];
                        final members = grouped[def.type] ?? [];
                        return _TypeCard(
                          def: def,
                          members: members,
                          syncOn: syncOn,
                          onTap: () => Navigator.push(ctx,
                                  MaterialPageRoute(
                                    builder: (_) => MemberListScreen(
                                      typeDef: def,
                                      members: members,
                                    ),
                                  ))
                              .then((_) => p.refreshDocuments()),
                        );
                      },
                    ),
            ),
          ),
        ]),
      );
    });
  }

  Future<void> _handleSyncToggle(
      BuildContext ctx, DocumentsProvider p, bool enable) async {
    if (enable) {
      // Confirm before enabling — uploading local docs
      final ok = await showDialog<bool>(
        context: ctx,
        builder: (_) => const _SyncEnableDialog(),
      );
      if (ok != true) return;
    }
    await p.setSyncEnabled(enable);
    if (!enable && ctx.mounted) {
      ScaffoldMessenger.of(ctx).showSnackBar(const SnackBar(
        content: Text('Cloud sync disabled. Documents saved locally only.'),
        backgroundColor: AppColors.warning,
      ));
    }
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Sync-enable confirmation dialog
// ─────────────────────────────────────────────────────────────────────────────
class _SyncEnableDialog extends StatelessWidget {
  const _SyncEnableDialog();
  @override
  Widget build(BuildContext context) => AlertDialog(
    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
    title: const Row(children: [
      Icon(Icons.cloud_upload_rounded, color: AppColors.primary, size: 24),
      SizedBox(width: 10),
      Text('Enable Cloud Sync?',
          style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16)),
    ]),
    content: const Text(
      'Your documents will be encrypted and uploaded to the secure Nagarik+ server.\n\n'
      'Any documents saved locally will be uploaded now.\n\n'
      'You can turn this off at any time.',
      style: TextStyle(fontSize: 13, height: 1.5, color: AppColors.textMedium),
    ),
    actions: [
      TextButton(
        onPressed: () => Navigator.pop(context, false),
        child: const Text('Not Now'),
      ),
      ElevatedButton(
        style: ElevatedButton.styleFrom(
            backgroundColor: AppColors.primary,
            foregroundColor: Colors.white,
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10))),
        onPressed: () => Navigator.pop(context, true),
        child: const Text('Enable Sync',
            style: TextStyle(fontWeight: FontWeight.w700)),
      ),
    ],
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// First-run consent banner (shown once, never again after dismissed)
// ─────────────────────────────────────────────────────────────────────────────
class _SyncConsentBanner extends StatelessWidget {
  final VoidCallback onEnable;
  final VoidCallback onDismiss;
  const _SyncConsentBanner({required this.onEnable, required this.onDismiss});

  @override
  Widget build(BuildContext context) => Container(
    margin: const EdgeInsets.fromLTRB(16, 12, 16, 0),
    decoration: BoxDecoration(
      gradient: const LinearGradient(
        colors: [Color(0xFF1565C0), Color(0xFF1976D2)],
        begin: Alignment.topLeft, end: Alignment.bottomRight,
      ),
      borderRadius: BorderRadius.circular(16),
      boxShadow: [
        BoxShadow(
            color: AppColors.primary.withOpacity(0.3),
            blurRadius: 12, offset: const Offset(0, 4)),
      ],
    ),
    child: Padding(
      padding: const EdgeInsets.all(16),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        const Row(children: [
          Icon(Icons.cloud_done_rounded, color: Colors.white, size: 20),
          SizedBox(width: 8),
          Expanded(
            child: Text('Enable Cloud Backup?',
                style: TextStyle(color: Colors.white,
                    fontWeight: FontWeight.w800, fontSize: 15)),
          ),
        ]),
        const SizedBox(height: 8),
        const Text(
          'Keep your documents safe with encrypted cloud sync. '
          'Without sync, documents are stored on this device only — '
          'they will be lost if you reinstall the app.',
          style: TextStyle(color: Colors.white70, fontSize: 12, height: 1.5),
        ),
        const SizedBox(height: 14),
        Row(children: [
          Expanded(
            child: OutlinedButton(
              style: OutlinedButton.styleFrom(
                foregroundColor: Colors.white,
                side: const BorderSide(color: Colors.white38),
                padding: const EdgeInsets.symmetric(vertical: 10),
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10)),
              ),
              onPressed: onDismiss,
              child: const Text('Keep Local',
                  style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600)),
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.white,
                foregroundColor: AppColors.primary,
                padding: const EdgeInsets.symmetric(vertical: 10),
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10)),
              ),
              onPressed: onEnable,
              child: const Text('Enable Sync',
                  style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
            ),
          ),
        ]),
      ]),
    ),
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Sync-off notice bar (persistent, shown whenever sync is disabled)
// ─────────────────────────────────────────────────────────────────────────────
class _SyncOffBar extends StatelessWidget {
  final VoidCallback onEnable;
  const _SyncOffBar({required this.onEnable});

  @override
  Widget build(BuildContext context) => Container(
    margin: const EdgeInsets.fromLTRB(16, 10, 16, 0),
    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
    decoration: BoxDecoration(
      color: const Color(0xFFFFF3E0),
      borderRadius: BorderRadius.circular(12),
      border: Border.all(color: Colors.orange.shade200),
    ),
    child: Row(children: [
      const Icon(Icons.cloud_off_rounded, color: Colors.orange, size: 18),
      const SizedBox(width: 10),
      const Expanded(
        child: Text(
          'Sync off — documents saved on this device only.',
          style: TextStyle(color: Color(0xFF795548),
              fontSize: 12, fontWeight: FontWeight.w600),
        ),
      ),
      GestureDetector(
        onTap: onEnable,
        child: Container(
          padding:
              const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
          decoration: BoxDecoration(
              color: Colors.orange,
              borderRadius: BorderRadius.circular(8)),
          child: const Text('Turn On',
              style: TextStyle(
                  color: Colors.white,
                  fontSize: 11,
                  fontWeight: FontWeight.w700)),
        ),
      ),
    ]),
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// App-bar sync chip (ON / OFF)
// ─────────────────────────────────────────────────────────────────────────────
class _SyncChip extends StatelessWidget {
  final bool enabled;
  final VoidCallback onToggle;
  const _SyncChip({required this.enabled, required this.onToggle});

  @override
  Widget build(BuildContext context) => GestureDetector(
    onTap: onToggle,
    child: Container(
      margin: const EdgeInsets.only(right: 4, top: 10, bottom: 10),
      padding: const EdgeInsets.symmetric(horizontal: 10),
      decoration: BoxDecoration(
        color: enabled
            ? Colors.white.withOpacity(0.2)
            : Colors.orange.withOpacity(0.25),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
            color: enabled
                ? Colors.white.withOpacity(0.4)
                : Colors.orange.withOpacity(0.5)),
      ),
      child: Row(mainAxisSize: MainAxisSize.min, children: [
        Icon(
          enabled ? Icons.cloud_done_rounded : Icons.cloud_off_rounded,
          color: enabled ? Colors.white : Colors.orange,
          size: 14,
        ),
        const SizedBox(width: 4),
        Text(
          enabled ? 'Sync On' : 'Sync Off',
          style: TextStyle(
              color: enabled ? Colors.white : Colors.orange,
              fontSize: 11,
              fontWeight: FontWeight.w700),
        ),
      ]),
    ),
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// DocumentsScreen
// ─────────────────────────────────────────────────────────────────────────────
class DocumentsScreen extends StatefulWidget {
  const DocumentsScreen({super.key});
  @override
  State<DocumentsScreen> createState() => _DocumentsScreenState();
}

class _DocumentsScreenState extends State<DocumentsScreen> {
  int    _selCat       = 0;
  String _search       = '';
  bool   _showConsent  = false;

  static const _cats = [
    ('All',      Icons.grid_view_rounded),
    ('Identity', Icons.badge_rounded),
    ('Vehicle',  Icons.drive_eta_rounded),
    ('Finance',  Icons.account_balance_rounded),
    ('Vital',    Icons.family_restroom_rounded),
    ('Medical',  Icons.medical_services_rounded),
    ('Academic', Icons.school_rounded),
    ('Other',    Icons.more_horiz_rounded),
  ];

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final p = context.read<DocumentsProvider>();
      await p.initSync();
      await p.loadDocuments();
      final prompted = await LocalDocumentStore.instance.hasBeenPrompted();
      if (!prompted && mounted) setState(() => _showConsent = true);
    });
  }

  Map<String, List<DocumentModel>> _grouped(List<DocumentModel> docs) {
    final m = <String, List<DocumentModel>>{};
    for (final d in docs) { (m[d.type] ??= []).add(d); }
    return m;
  }

  List<DocTypeDef> _visible() {
    final cat = _cats[_selCat].$1;
    var list = _selCat == 0 ? allDocTypes
        : allDocTypes.where((d) => d.category == cat).toList();
    if (_search.trim().isNotEmpty) {
      final q = _search.toLowerCase();
      list = list.where((d) =>
          d.label.toLowerCase().contains(q) || d.type.contains(q)).toList();
    }
    return list;
  }

  // ── Sync toggle handler ───────────────────────────────────────────────────
  Future<void> _toggleSync(BuildContext ctx, DocumentsProvider p) async {
    if (!p.syncEnabled) {
      final ok = await showDialog<bool>(
          context: ctx, builder: (_) => const _SyncEnableDialog());
      if (ok != true) return;
      await p.setSyncEnabled(true);
      if (ctx.mounted) {
        ScaffoldMessenger.of(ctx).showSnackBar(const SnackBar(
          content: Text('☁️ Cloud sync enabled. Local docs are being uploaded.'),
          backgroundColor: AppColors.success,
        ));
      }
    } else {
      final ok = await showDialog<bool>(
          context: ctx, builder: (_) => const _SyncDisableDialog());
      if (ok != true) return;
      await p.setSyncEnabled(false);
      if (ctx.mounted) {
        ScaffoldMessenger.of(ctx).showSnackBar(const SnackBar(
          content: Text('Sync off — new documents stay on this device only.'),
          backgroundColor: AppColors.warning,
        ));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<DocumentsProvider>(builder: (ctx, p, _) {
      final syncOn  = p.syncEnabled;
      final grouped = _grouped(p.documents);
      final types   = _visible();
      final uploaded = allDocTypes.where((d) => (grouped[d.type]?.isNotEmpty ?? false)).length;
      final missing  = allDocTypes.length - uploaded;
      final expiring = p.expiringDocuments.length;

      return Scaffold(
        backgroundColor: const Color(0xFFF2F5FA),
        appBar: AppBar(
          backgroundColor: AppColors.primary,
          foregroundColor: Colors.white,
          elevation: 0,
          title: const Text('Digital Locker', style: TextStyle(fontWeight: FontWeight.w800)),
          centerTitle: true,
          leading: IconButton(icon: const Icon(Icons.arrow_back_rounded),
              onPressed: () => Navigator.pop(context)),
          actions: [
            _SyncChip(enabled: syncOn, onToggle: () => _toggleSync(ctx, p)),
            IconButton(
              icon: const Icon(Icons.folder_special_rounded),
              tooltip: 'Scan Vault',
              onPressed: () => Navigator.push(ctx,
                  MaterialPageRoute(builder: (_) => const VaultScreen())),
            ),
          ],
        ),
        body: Column(children: [

          // ── First-run consent banner ─────────────────────────────
          if (_showConsent)
            _SyncConsentBanner(
              onEnable: () async {
                setState(() => _showConsent = false);
                final ok = await showDialog<bool>(
                    context: ctx, builder: (_) => const _SyncEnableDialog());
                if (ok == true) await p.setSyncEnabled(true);
                await LocalDocumentStore.instance.markPrompted();
              },
              onDismiss: () async {
                setState(() => _showConsent = false);
                await LocalDocumentStore.instance.markPrompted();
              },
            ),

          // ── Persistent sync-off notice ───────────────────────────
          if (!syncOn && !_showConsent)
            _SyncOffBar(onEnable: () => _toggleSync(ctx, p)),

          // ── Stats banner ─────────────────────────────────────────
          _Banner(uploaded: uploaded, missing: missing,
              expiring: expiring, syncOn: syncOn),

          // ── Search ───────────────────────────────────────────────
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: TextField(
              onChanged: (v) => setState(() => _search = v),
              decoration: InputDecoration(
                hintText: 'Search document type…',
                hintStyle: const TextStyle(color: AppColors.textLight, fontSize: 13),
                prefixIcon: const Icon(Icons.search_rounded, color: AppColors.textLight, size: 20),
                filled: true, fillColor: Colors.white,
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
              ),
            ),
          ),

          // ── Category pills ────────────────────────────────────────
          SizedBox(
            height: 42,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.fromLTRB(16, 10, 16, 0),
              itemCount: _cats.length,
              itemBuilder: (_, i) {
                final active = _selCat == i;
                return GestureDetector(
                  onTap: () => setState(() => _selCat = i),
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
                    margin: const EdgeInsets.only(right: 8),
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
                    decoration: BoxDecoration(
                      color: active ? AppColors.primary : Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: active ? AppColors.primary : AppColors.divider),
                    ),
                    child: Text(_cats[i].$1,
                        style: TextStyle(
                            color: active ? Colors.white : AppColors.textMedium,
                            fontSize: 12,
                            fontWeight: active ? FontWeight.w700 : FontWeight.w500)),
                  ),
                );
              },
            ),
          ),
          const SizedBox(height: 8),

          // ── Grid ──────────────────────────────────────────────────
          Expanded(child: RefreshIndicator(
            onRefresh: p.refreshDocuments,
            color: AppColors.primary,
            child: p.status == DocumentsStatus.loading && p.documents.isEmpty
                ? const Center(child: CircularProgressIndicator())
                : GridView.builder(
                    padding: const EdgeInsets.fromLTRB(16, 4, 16, 100),
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2, childAspectRatio: 1.18,
                      crossAxisSpacing: 12, mainAxisSpacing: 12,
                    ),
                    itemCount: types.length,
                    itemBuilder: (_, i) {
                      final def     = types[i];
                      final members = grouped[def.type] ?? [];
                      return _TypeCard(
                        def: def, members: members, syncOn: syncOn,
                        onTap: () => Navigator.push(ctx,
                          MaterialPageRoute(builder: (_) => MemberListScreen(
                            typeDef: def, members: members,
                          )),
                        ).then((_) => p.refreshDocuments()),
                      );
                    },
                  ),
          )),
        ]),
      );
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Type-group card — member count + sync indicator
// ─────────────────────────────────────────────────────────────────────────────
class _TypeCard extends StatelessWidget {
  final DocTypeDef def;
  final List<DocumentModel> members;
  final bool syncOn;
  final VoidCallback onTap;
  const _TypeCard({required this.def, required this.members,
      required this.syncOn, required this.onTap});

  @override
  Widget build(BuildContext context) {
    final count  = members.length;
    final hasAny = count > 0;
    final color  = def.color;
    final now    = DateTime.now();
    final anyExpired  = members.any((d) => d.expiryDate != null && d.expiryDate!.isBefore(now));
    final anyExpiring = !anyExpired && members.any((d) =>
        d.expiryDate != null && d.expiryDate!.difference(now).inDays < 90);
    // Docs that exist locally but not yet synced to server
    final anyUnsynced = members.any((d) => d.id.startsWith('local_'));

    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: anyExpired
              ? Border.all(color: AppColors.danger, width: 1.5)
              : anyExpiring
                  ? Border.all(color: AppColors.warning, width: 1.5)
                  : !hasAny
                      ? Border.all(color: color.withOpacity(0.22), width: 1)
                      : null,
          boxShadow: [BoxShadow(
              color: Colors.black.withOpacity(hasAny ? 0.07 : 0.04),
              blurRadius: 8, offset: const Offset(0, 2))],
        ),
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Row(children: [
              Container(
                width: 42, height: 42,
                decoration: BoxDecoration(
                  color: hasAny ? color.withOpacity(0.12) : const Color(0xFFF4F4F4),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(def.icon, color: hasAny ? color : AppColors.textLight, size: 22),
              ),
              const Spacer(),
              // Storage-mode indicator on card
              if (hasAny && !syncOn && anyUnsynced)
                _MiniChip('📱 Local', Colors.orange)
              else if (hasAny && syncOn && !anyUnsynced)
                _MiniChip('☁ Synced', AppColors.success)
              else if (!hasAny)
                _MiniChip('Empty', color.withOpacity(0.65))
              else if (anyExpired)
                const _MiniChip('Expired', AppColors.danger)
              else if (anyExpiring)
                const _MiniChip('Expiring', AppColors.warning)
              else
                _MiniChip('$count Added', AppColors.success),
            ]),
            const Spacer(),
            Text(def.label,
                style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13,
                    color: hasAny ? AppColors.textDark : AppColors.textMedium),
                maxLines: 1, overflow: TextOverflow.ellipsis),
            const SizedBox(height: 3),
            if (hasAny)
              _MemberAvatarRow(members: members, color: color)
            else
              Row(children: [
                Icon(Icons.add_circle_outline_rounded, size: 12, color: color),
                const SizedBox(width: 4),
                Text('Tap to add', style: TextStyle(
                    color: color, fontSize: 10, fontWeight: FontWeight.w600)),
              ]),
          ]),
        ),
      ),
    );
  }
}

class _MemberAvatarRow extends StatelessWidget {
  final List<DocumentModel> members;
  final Color color;
  const _MemberAvatarRow({required this.members, required this.color});
  @override
  Widget build(BuildContext context) {
    final show  = members.take(4).toList();
    final extra = members.length - show.length;
    return Row(children: [
      ...show.map((m) {
        final init = (m.holderName.isNotEmpty ? m.holderName[0] : '?').toUpperCase();
        final isLocal = m.id.startsWith('local_');
        return Stack(clipBehavior: Clip.none, children: [
          Container(
            width: 22, height: 22,
            margin: const EdgeInsets.only(right: 3),
            decoration: BoxDecoration(
                color: color.withOpacity(0.15), shape: BoxShape.circle,
                border: Border.all(
                    color: isLocal ? Colors.orange.withOpacity(0.7) : color.withOpacity(0.4),
                    width: isLocal ? 1.5 : 1)),
            child: Center(child: Text(init, style: TextStyle(
                color: color, fontSize: 9, fontWeight: FontWeight.w800))),
          ),
          // Small cloud-off dot for local-only docs
          if (isLocal)
            Positioned(right: 1, bottom: 0,
              child: Container(width: 8, height: 8,
                  decoration: BoxDecoration(
                      color: Colors.orange, shape: BoxShape.circle,
                      border: Border.all(color: Colors.white, width: 1)),
                  child: const Center(child: Icon(Icons.cloud_off_rounded,
                      size: 5, color: Colors.white))),
            ),
        ]);
      }),
      if (extra > 0)
        Container(width: 22, height: 22,
            decoration: BoxDecoration(color: color.withOpacity(0.1), shape: BoxShape.circle),
            child: Center(child: Text('+$extra', style: TextStyle(
                color: color, fontSize: 8, fontWeight: FontWeight.w800)))),
    ]);
  }
}

class _MiniChip extends StatelessWidget {
  final String label;
  final Color color;
  const _MiniChip(this.label, this.color);
  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
    decoration: BoxDecoration(color: color.withOpacity(0.12), borderRadius: BorderRadius.circular(6)),
    child: Text(label, style: TextStyle(color: color, fontSize: 9, fontWeight: FontWeight.w700)),
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Stats banner
// ─────────────────────────────────────────────────────────────────────────────
class _Banner extends StatelessWidget {
  final int uploaded, missing, expiring;
  final bool syncOn;
  const _Banner({required this.uploaded, required this.missing,
      required this.expiring, required this.syncOn});

  @override
  Widget build(BuildContext context) => Container(
    margin: const EdgeInsets.fromLTRB(16, 12, 16, 0),
    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
    decoration: BoxDecoration(
      gradient: LinearGradient(
          colors: [AppColors.primary, AppColors.primary.withOpacity(0.82)]),
      borderRadius: BorderRadius.circular(16),
      boxShadow: [BoxShadow(color: AppColors.primary.withOpacity(0.3),
          blurRadius: 12, offset: const Offset(0, 4))],
    ),
    child: Row(children: [
      Container(
        padding: const EdgeInsets.all(8),
        decoration: BoxDecoration(color: Colors.white.withOpacity(0.2),
            borderRadius: BorderRadius.circular(10)),
        child: Icon(syncOn ? Icons.cloud_done_rounded : Icons.phone_android_rounded,
            color: Colors.white, size: 20),
      ),
      const SizedBox(width: 12),
      Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        const Text('Family Document Locker',
            style: TextStyle(color: Colors.white,
                fontWeight: FontWeight.w700, fontSize: 13)),
        Text(syncOn ? 'Encrypted cloud backup ON' : '📱 Local device only',
            style: TextStyle(
                color: syncOn ? Colors.white70 : Colors.orange.shade200,
                fontSize: 11)),
      ])),
      _Pill('$uploaded', 'Types', AppColors.success),
      const SizedBox(width: 6),
      _Pill('$missing', 'Empty', Colors.orange),
      if (expiring > 0) ...[
        const SizedBox(width: 6),
        _Pill('$expiring', 'Expiring', AppColors.danger),
      ],
    ]),
  );
}

class _Pill extends StatelessWidget {
  final String count, label;
  final Color color;
  const _Pill(this.count, this.label, this.color);
  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
    decoration: BoxDecoration(
        color: color.withOpacity(0.18), borderRadius: BorderRadius.circular(8)),
    child: Column(mainAxisSize: MainAxisSize.min, children: [
      Text(count, style: TextStyle(color: color, fontWeight: FontWeight.w900, fontSize: 14)),
      Text(label, style: TextStyle(color: color, fontSize: 9, fontWeight: FontWeight.w600)),
    ]),
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Sync UI widgets
// ─────────────────────────────────────────────────────────────────────────────

// App-bar chip
class _SyncChip extends StatelessWidget {
  final bool enabled;
  final VoidCallback onToggle;
  const _SyncChip({required this.enabled, required this.onToggle});
  @override
  Widget build(BuildContext context) => GestureDetector(
    onTap: onToggle,
    child: Container(
      margin: const EdgeInsets.only(right: 4, top: 10, bottom: 10),
      padding: const EdgeInsets.symmetric(horizontal: 10),
      decoration: BoxDecoration(
        color: enabled ? Colors.white.withOpacity(0.2) : Colors.orange.withOpacity(0.25),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(
            color: enabled ? Colors.white.withOpacity(0.4) : Colors.orange.withOpacity(0.5)),
      ),
      child: Row(mainAxisSize: MainAxisSize.min, children: [
        Icon(enabled ? Icons.cloud_done_rounded : Icons.cloud_off_rounded,
            color: enabled ? Colors.white : Colors.orange, size: 14),
        const SizedBox(width: 4),
        Text(enabled ? 'Sync On' : 'Sync Off',
            style: TextStyle(
                color: enabled ? Colors.white : Colors.orange,
                fontSize: 11, fontWeight: FontWeight.w700)),
      ]),
    ),
  );
}

// First-run consent banner
class _SyncConsentBanner extends StatelessWidget {
  final VoidCallback onEnable;
  final VoidCallback onDismiss;
  const _SyncConsentBanner({required this.onEnable, required this.onDismiss});

  @override
  Widget build(BuildContext context) => Container(
    margin: const EdgeInsets.fromLTRB(16, 12, 16, 0),
    padding: const EdgeInsets.all(16),
    decoration: BoxDecoration(
      gradient: const LinearGradient(
          colors: [Color(0xFF1565C0), Color(0xFF1976D2)],
          begin: Alignment.topLeft, end: Alignment.bottomRight),
      borderRadius: BorderRadius.circular(16),
      boxShadow: [BoxShadow(color: AppColors.primary.withOpacity(0.3),
          blurRadius: 12, offset: const Offset(0, 4))],
    ),
    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      const Row(children: [
        Icon(Icons.cloud_done_rounded, color: Colors.white, size: 20),
        SizedBox(width: 8),
        Expanded(child: Text('Enable Cloud Backup?',
            style: TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 15))),
      ]),
      const SizedBox(height: 8),
      const Text(
        'Your documents will be AES-256 encrypted and backed up to the secure '
        'Nagarik+ server.\n\nWithout sync, documents are stored on this device '
        'only — they will be lost if you uninstall the app.',
        style: TextStyle(color: Colors.white70, fontSize: 12, height: 1.5),
      ),
      const SizedBox(height: 14),
      Row(children: [
        Expanded(child: OutlinedButton(
          style: OutlinedButton.styleFrom(
            foregroundColor: Colors.white,
            side: const BorderSide(color: Colors.white38),
            padding: const EdgeInsets.symmetric(vertical: 10),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          ),
          onPressed: onDismiss,
          child: const Text('Keep Local Only',
              style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600)),
        )),
        const SizedBox(width: 10),
        Expanded(child: ElevatedButton(
          style: ElevatedButton.styleFrom(
            backgroundColor: Colors.white, foregroundColor: AppColors.primary,
            padding: const EdgeInsets.symmetric(vertical: 10),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          ),
          onPressed: onEnable,
          child: const Text('Enable Sync',
              style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
        )),
      ]),
    ]),
  );
}

// Persistent sync-off notice bar
class _SyncOffBar extends StatelessWidget {
  final VoidCallback onEnable;
  const _SyncOffBar({required this.onEnable});
  @override
  Widget build(BuildContext context) => Container(
    margin: const EdgeInsets.fromLTRB(16, 10, 16, 0),
    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
    decoration: BoxDecoration(
      color: const Color(0xFFFFF3E0),
      borderRadius: BorderRadius.circular(12),
      border: Border.all(color: Colors.orange.shade200),
    ),
    child: Row(children: [
      const Icon(Icons.cloud_off_rounded, color: Colors.orange, size: 18),
      const SizedBox(width: 10),
      const Expanded(child: Text(
        'Sync off — documents saved on this device only.',
        style: TextStyle(color: Color(0xFF795548), fontSize: 12, fontWeight: FontWeight.w600),
      )),
      GestureDetector(
        onTap: onEnable,
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
          decoration: BoxDecoration(color: Colors.orange, borderRadius: BorderRadius.circular(8)),
          child: const Text('Turn On', style: TextStyle(
              color: Colors.white, fontSize: 11, fontWeight: FontWeight.w700)),
        ),
      ),
    ]),
  );
}

// Enable-sync confirmation dialog
class _SyncEnableDialog extends StatelessWidget {
  const _SyncEnableDialog();
  @override
  Widget build(BuildContext context) => AlertDialog(
    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
    title: const Row(children: [
      Icon(Icons.cloud_upload_rounded, color: AppColors.primary, size: 24),
      SizedBox(width: 10),
      Expanded(child: Text('Enable Cloud Sync?',
          style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16))),
    ]),
    content: const Text(
      'Your documents will be AES-256 encrypted and uploaded to the secure '
      'Nagarik+ server.\n\nAny locally stored documents will be uploaded now.\n\n'
      'You can turn this off at any time.',
      style: TextStyle(fontSize: 13, height: 1.5, color: AppColors.textMedium),
    ),
    actions: [
      TextButton(onPressed: () => Navigator.pop(context, false),
          child: const Text('Not Now')),
      ElevatedButton(
        style: ElevatedButton.styleFrom(backgroundColor: AppColors.primary,
            foregroundColor: Colors.white,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))),
        onPressed: () => Navigator.pop(context, true),
        child: const Text('Enable Sync', style: TextStyle(fontWeight: FontWeight.w700)),
      ),
    ],
  );
}

// Disable-sync confirmation dialog
class _SyncDisableDialog extends StatelessWidget {
  const _SyncDisableDialog();
  @override
  Widget build(BuildContext context) => AlertDialog(
    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
    title: const Row(children: [
      Icon(Icons.cloud_off_rounded, color: Colors.orange, size: 24),
      SizedBox(width: 10),
      Expanded(child: Text('Disable Cloud Sync?',
          style: TextStyle(fontWeight: FontWeight.w800, fontSize: 16))),
    ]),
    content: const Text(
      'New documents will only be saved on this device.\n\n'
      'Documents already uploaded to the server will remain there.\n\n'
      'You can re-enable sync at any time.',
      style: TextStyle(fontSize: 13, height: 1.5, color: AppColors.textMedium),
    ),
    actions: [
      TextButton(onPressed: () => Navigator.pop(context, false),
          child: const Text('Cancel')),
      ElevatedButton(
        style: ElevatedButton.styleFrom(backgroundColor: Colors.orange,
            foregroundColor: Colors.white,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10))),
        onPressed: () => Navigator.pop(context, true),
        child: const Text('Turn Off Sync', style: TextStyle(fontWeight: FontWeight.w700)),
      ),
    ],
  );
}
