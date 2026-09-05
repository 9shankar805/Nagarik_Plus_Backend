import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import '../models/document_model.dart';
import '../repositories/document_repository.dart';
import '../services/local_document_store.dart';

enum DocumentsStatus { initial, loading, loaded, error }

// ─────────────────────────────────────────────────────────────────────────────
// DocumentsProvider
//
// Sync gate logic:
//   syncEnabled = false  →  ALL reads/writes go to LocalDocumentStore only.
//                           Nothing is sent to the backend.
//   syncEnabled = true   →  Reads from API (falling back to local cache).
//                           Writes POST to API and also mirror to local store.
//                           On first enable: pending local docs are uploaded.
// ─────────────────────────────────────────────────────────────────────────────
class DocumentsProvider extends ChangeNotifier {
  final DocumentRepository   _repository;
  final LocalDocumentStore   _local = LocalDocumentStore.instance;

  DocumentsStatus _status    = DocumentsStatus.initial;
  List<DocumentModel> _documents         = [];
  List<DocumentModel> _expiringDocuments = [];
  String?  _errorMessage;
  double   _uploadProgress  = 0.0;
  bool     _isUploading     = false;
  bool     _syncEnabled     = false;
  bool     _syncInitialized = false;

  DocumentsStatus        get status            => _status;
  List<DocumentModel>    get documents          => _documents;
  List<DocumentModel>    get expiringDocuments  => _expiringDocuments;
  String?                get errorMessage       => _errorMessage;
  double                 get uploadProgress     => _uploadProgress;
  bool                   get isUploading        => _isUploading;
  bool                   get syncEnabled        => _syncEnabled;

  DocumentsProvider({DocumentRepository? repository})
      : _repository = repository ?? DocumentRepository();

  // ── Init — load sync preference ──────────────────────────────────────────
  Future<void> initSync() async {
    if (_syncInitialized) return;
    _syncEnabled     = await _local.isSyncEnabled();
    _syncInitialized = true;
    notifyListeners();
  }

  // ── Toggle sync consent ──────────────────────────────────────────────────
  /// Called when the user flips the sync toggle.
  /// If enabling: upload any pending local-only docs to the server,
  ///              then refresh from API.
  /// If disabling: keep the current list in local store, stop API calls.
  Future<void> setSyncEnabled(bool value) async {
    _syncEnabled = value;
    await _local.setSyncEnabled(value);
    await _local.markPrompted();
    notifyListeners();

    if (value) {
      // Upload pending local-only docs before refreshing
      await _uploadPendingLocal();
      await loadDocuments(forceRefresh: true);
    }
    // If disabling, nothing else needed — local store already has the docs
  }

  // ── Upload any local-only docs to the API ────────────────────────────────
  Future<void> _uploadPendingLocal() async {
    final localDocs = await _local.loadAll();
    final pending   = localDocs.where((d) => d.id.startsWith('local_')).toList();
    for (final doc in pending) {
      try {
        final fields = <String, dynamic>{
          'title':       doc.title,
          'type':        doc.type,
          'holder_name': doc.holderName,
          if (doc.expiryDate != null)
            'expiry_date': doc.expiryDate!.toIso8601String().substring(0, 10),
          'metadata': {'holder_name': doc.holderName},
        };
        // Attach local file if it exists
        if (doc.localFilePath != null) {
          fields['file'] = await MultipartFile.fromFile(
            doc.localFilePath!,
            filename: doc.localFilePath!.split('/').last,
          );
        }
        final fd       = FormData.fromMap(fields);
        final uploaded = await _repository.uploadDocument(fd);
        // Replace local entry with the server-assigned id
        await _local.delete(doc.id);
        await _local.save(uploaded);
      } catch (_) {
        // Best-effort — leave local doc in place if upload fails
      }
    }
  }

  // ── Load documents ───────────────────────────────────────────────────────
  Future<void> loadDocuments({bool forceRefresh = false}) async {
    await initSync();
    _status = DocumentsStatus.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      if (!_syncEnabled) {
        // ── Local-only mode ─────────────────────────────────────────────
        _documents = await _local.loadAll();
        _expiringDocuments = _expiringFilter(_documents);
      } else {
        // ── Cloud sync mode ─────────────────────────────────────────────
        try {
          final apiDocs = await _repository.getDocuments(forceRefresh: forceRefresh);
          // Mirror to local store so they're available offline
          await _local.saveAll(apiDocs);
          _documents = apiDocs;
        } catch (_) {
          // API failed — fall back to local cache
          _documents = await _local.loadAll();
        }
        try {
          _expiringDocuments = await _repository.getExpiringDocuments();
        } catch (_) {
          _expiringDocuments = _expiringFilter(_documents);
        }
      }
      _status = DocumentsStatus.loaded;
    } catch (e) {
      _errorMessage = e.toString().replaceFirst('Exception: ', '');
      _status       = DocumentsStatus.error;
    }
    notifyListeners();
  }

  Future<void> refreshDocuments() => loadDocuments(forceRefresh: true);

  // ── Upload / save a document ─────────────────────────────────────────────
  static const double _progressThreshold = 0.02;

  /// If sync is disabled → save to local store only.
  /// If sync is enabled  → POST to API and mirror to local store.
  Future<DocumentModel> uploadDocument(
    FormData formData, {
    Function(int, int)? onSendProgress,
  }) async {
    await initSync();

    if (!_syncEnabled) {
      return _saveLocally(formData);
    }
    return _uploadToApi(formData, onSendProgress: onSendProgress);
  }

  Future<DocumentModel> _saveLocally(FormData formData) async {
    // Extract plain fields from FormData (files not stored in this path —
    // caller already saved the file locally before calling uploadDocument)
    final fields  = <String, String>{};
    String? localFilePath;
    for (final field in formData.fields) {
      fields[field.key] = field.value;
    }
    for (final file in formData.files) {
      // file.value is a MultipartFile — its filename gives us path context
      localFilePath = file.value.filename;
    }

    final doc = DocumentModel(
      id:          LocalDocumentStore.generateLocalId(),
      title:       fields['title']       ?? 'Document',
      subtitle:    fields['document_number'] ?? fields['holder_name'] ?? 'Local copy',
      type:        fields['type']        ?? 'other',
      category:    _categoryForType(fields['type'] ?? ''),
      holderName:  fields['holder_name'] ?? '',
      isUploaded:  localFilePath != null,
      localFilePath: localFilePath,
      expiryDate: fields['expiry_date'] != null
          ? DateTime.tryParse(fields['expiry_date']!) : null,
      metadata: fields['holder_name'] != null
          ? {'holder_name': fields['holder_name']!} : {},
    );

    await _local.save(doc);
    _documents.insert(0, doc);
    _status = DocumentsStatus.loaded;
    notifyListeners();
    return doc;
  }

  Future<DocumentModel> _uploadToApi(
    FormData formData, {
    Function(int, int)? onSendProgress,
  }) async {
    _isUploading    = true;
    _uploadProgress = 0.0;
    notifyListeners();
    try {
      final doc = await _repository.uploadDocument(
        formData,
        onSendProgress: (sent, total) {
          if (total > 0) {
            final p = sent / total;
            if ((p - _uploadProgress) >= _progressThreshold || p >= 1.0) {
              _uploadProgress = p;
              notifyListeners();
            }
          }
          onSendProgress?.call(sent, total);
        },
      );
      await _local.save(doc); // mirror
      _documents.insert(0, doc);
      _status = DocumentsStatus.loaded;
      return doc;
    } catch (e) {
      _errorMessage = e.toString().replaceFirst('Exception: ', '');
      rethrow;
    } finally {
      _isUploading    = false;
      _uploadProgress = 0.0;
      notifyListeners();
    }
  }

  // ── Get by id ─────────────────────────────────────────────────────────────
  Future<DocumentModel?> getDocById(int id) async {
    final cached = _documents.where((d) => d.id == id.toString()).toList();
    if (cached.isNotEmpty) return cached.first;
    if (!_syncEnabled) return null;
    try { return await _repository.getDocument(id); } catch (_) { return null; }
  }

  // ── Update ────────────────────────────────────────────────────────────────
  Future<void> updateDoc(int id, Map<String, dynamic> data) async {
    if (!_syncEnabled) {
      // Local update only
      final idx = _documents.indexWhere((d) => d.id == id.toString());
      if (idx != -1) {
        final updated = _documents[idx].copyWith(
          subtitle: data['title']?.toString(),
        );
        _documents[idx] = updated;
        await _local.save(updated);
        notifyListeners();
      }
      return;
    }
    try {
      final updatedDoc = await _repository.updateDocument(id, data);
      final index = _documents.indexWhere((d) => d.id == id.toString());
      if (index != -1) { _documents[index] = updatedDoc; }
      await _local.save(updatedDoc);
      notifyListeners();
    } catch (e) {
      _errorMessage = e.toString().replaceFirst('Exception: ', '');
      notifyListeners();
      rethrow;
    }
  }

  // ── Delete ────────────────────────────────────────────────────────────────
  Future<void> deleteDoc(int id) async {
    _documents.removeWhere((d) => d.id == id.toString());
    await _local.delete(id.toString());
    notifyListeners();

    if (_syncEnabled) {
      try { await _repository.deleteDocument(id); } catch (_) {}
    }
  }

  // ── Download ──────────────────────────────────────────────────────────────
  Future<String> downloadDoc(int id, {String fileName = 'document'}) async {
    if (!_syncEnabled) throw Exception('Enable cloud sync to download files.');
    try {
      return await _repository.downloadDocument(id, fileName);
    } catch (e) {
      _errorMessage = e.toString().replaceFirst('Exception: ', '');
      rethrow;
    }
  }

  // ── Helpers ───────────────────────────────────────────────────────────────
  List<DocumentModel> _expiringFilter(List<DocumentModel> docs) {
    final now = DateTime.now();
    return docs.where((d) {
      if (d.expiryDate == null) return false;
      final diff = d.expiryDate!.difference(now).inDays;
      return diff >= 0 && diff <= 90;
    }).toList();
  }

  String _categoryForType(String type) {
    switch (type) {
      case 'citizenship': case 'national_id': case 'passport': case 'voter_id':
        return 'Identity';
      case 'driving_license': case 'vehicle_bluebook':
        return 'Vehicle';
      case 'pan': case 'insurance': case 'property':
        return 'Finance';
      case 'birth_certificate': case 'marriage_certificate':
      case 'death_certificate': case 'migration_certificate':
        return 'Vital';
      case 'medical':  return 'Medical';
      case 'academic': return 'Academic';
      default:         return 'Other';
    }
  }
}
