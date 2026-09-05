import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/document_model.dart';

/// Pure local storage for documents — no mock seed data, no API calls.
/// Used when the user has not enabled cloud sync.
/// Each document is keyed by its local id (UUID or timestamp string).
class LocalDocumentStore {
  static const _key       = 'local_docs_v2';
  static const _syncKey   = 'docs_sync_enabled';
  static const _firstKey  = 'docs_sync_prompted';

  // ── Singleton ─────────────────────────────────────────────────────────────
  static final LocalDocumentStore instance = LocalDocumentStore._();
  LocalDocumentStore._();

  // ── Sync preference ───────────────────────────────────────────────────────

  /// Whether the user has consented to cloud sync.
  Future<bool> isSyncEnabled() async {
    final p = await SharedPreferences.getInstance();
    return p.getBool(_syncKey) ?? false; // default OFF — local only
  }

  Future<void> setSyncEnabled(bool value) async {
    final p = await SharedPreferences.getInstance();
    await p.setBool(_syncKey, value);
  }

  /// True if we have already shown the first-run sync consent prompt.
  Future<bool> hasBeenPrompted() async {
    final p = await SharedPreferences.getInstance();
    return p.getBool(_firstKey) ?? false;
  }

  Future<void> markPrompted() async {
    final p = await SharedPreferences.getInstance();
    await p.setBool(_firstKey, true);
  }

  // ── CRUD ──────────────────────────────────────────────────────────────────

  Future<List<DocumentModel>> loadAll() async {
    final p    = await SharedPreferences.getInstance();
    final raw  = p.getStringList(_key) ?? [];
    return raw.map((s) {
      try { return DocumentModel.fromJson(jsonDecode(s) as Map<String, dynamic>); }
      catch (_) { return null; }
    }).whereType<DocumentModel>().toList();
  }

  /// Upsert a document — replaces existing entry with same id, or appends.
  Future<void> save(DocumentModel doc) async {
    final all = await loadAll();
    final idx = all.indexWhere((d) => d.id == doc.id);
    if (idx >= 0) { all[idx] = doc; } else { all.insert(0, doc); }
    await _persist(all);
  }

  /// Save multiple documents at once (e.g. after API sync).
  Future<void> saveAll(List<DocumentModel> docs) async {
    final existing = await loadAll();
    final byId = {for (final d in existing) d.id: d};
    for (final d in docs) { byId[d.id] = d; }
    await _persist(byId.values.toList());
  }

  Future<void> delete(String id) async {
    final all = await loadAll();
    all.removeWhere((d) => d.id == id);
    await _persist(all);
  }

  Future<void> clear() async {
    final p = await SharedPreferences.getInstance();
    await p.remove(_key);
  }

  // ── Helper ────────────────────────────────────────────────────────────────
  Future<void> _persist(List<DocumentModel> docs) async {
    final p = await SharedPreferences.getInstance();
    await p.setStringList(
      _key,
      docs.map((d) => jsonEncode(d.toJson())).toList(),
    );
  }

  /// Generate a local-only id that won't clash with numeric server ids.
  static String generateLocalId() =>
      'local_${DateTime.now().millisecondsSinceEpoch}';
}
