import 'dart:convert';

class DocumentModel {
  final String id;
  final String title;
  final String subtitle;
  final String type;
  final String category;
  final String? assetImagePath;
  final String? localFilePath;
  final String? localBackFilePath;
  final DateTime? expiryDate;
  final bool isUploaded;
  final Map<String, String> fields;
  /// Person this document belongs to — e.g. "Ram", "Mother", "Sister".
  /// Stored in metadata.holder_name on the backend.
  final String holderName;
  /// Raw metadata map from the backend (may contain holder_name and more).
  final Map<String, dynamic> metadata;

  const DocumentModel({
    required this.id,
    required this.title,
    required this.subtitle,
    required this.type,
    required this.category,
    this.assetImagePath,
    this.localFilePath,
    this.localBackFilePath,
    this.expiryDate,
    required this.isUploaded,
    this.fields = const {},
    this.holderName = '',
    this.metadata = const {},
  });

  DocumentModel copyWith({
    String? localFilePath,
    String? localBackFilePath,
    bool? isUploaded,
    Map<String, String>? fields,
    String? subtitle,
    String? holderName,
    DateTime? expiryDate,
    Map<String, dynamic>? metadata,
  }) {
    return DocumentModel(
      id: id,
      title: title,
      subtitle: subtitle ?? this.subtitle,
      type: type,
      category: category,
      assetImagePath: assetImagePath,
      localFilePath: localFilePath ?? this.localFilePath,
      localBackFilePath: localBackFilePath ?? this.localBackFilePath,
      expiryDate: expiryDate ?? this.expiryDate,
      isUploaded: isUploaded ?? this.isUploaded,
      fields: fields ?? this.fields,
      holderName: holderName ?? this.holderName,
      metadata: metadata ?? this.metadata,
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'title': title,
        'subtitle': subtitle,
        'type': type,
        'category': category,
        'assetImagePath': assetImagePath,
        'localFilePath': localFilePath,
        'localBackFilePath': localBackFilePath,
        'expiryDate': expiryDate?.toIso8601String(),
        'isUploaded': isUploaded,
        'fields': fields,
        'holderName': holderName,
        'metadata': metadata,
      };

  static String _categoryForType(String type) {
    switch (type.toLowerCase()) {
      case 'citizenship':
      case 'national_id':
      case 'passport':
      case 'voter_id':
        return 'Identity';
      case 'driving_license':
      case 'vehicle_bluebook':
        return 'Vehicle';
      case 'pan':
      case 'insurance':
      case 'property':
        return 'Finance';
      case 'academic':
        return 'Education';
      case 'birth_certificate':
      case 'marriage_certificate':
      case 'death_certificate':
      case 'migration_certificate':
        return 'Vital Events';
      default:
        return 'Personal';
    }
  }

  factory DocumentModel.fromJson(Map<String, dynamic> json) {
    final typeStr = json['type']?.toString() ?? 'other';
    final rawExpiry = json['expiryDate'] ?? json['expiry_date'];
    DateTime? expDate;
    if (rawExpiry != null) {
      expDate = DateTime.tryParse(rawExpiry.toString());
    }

    // Parse metadata — may arrive as Map or already decoded
    final rawMeta = json['metadata'];
    Map<String, dynamic> meta = {};
    if (rawMeta is Map<String, dynamic>) {
      meta = rawMeta;
    } else if (rawMeta is String && rawMeta.isNotEmpty) {
      try { meta = jsonDecode(rawMeta) as Map<String, dynamic>; } catch (_) {}
    }

    // holder_name: prefer explicit metadata field, fall back to json top-level,
    // last resort derive from title by stripping the doc type label.
    final holderName = meta['holder_name']?.toString() ??
        json['holder_name']?.toString() ??
        '';

    return DocumentModel(
      id: json['id']?.toString() ?? DateTime.now().millisecondsSinceEpoch.toString(),
      title: json['title']?.toString() ?? 'Document',
      subtitle: json['subtitle']?.toString() ??
          json['document_number']?.toString() ??
          json['issue_date']?.toString() ??
          'Official Document',
      type: typeStr,
      category: json['category']?.toString() ?? _categoryForType(typeStr),
      assetImagePath: json['assetImagePath']?.toString(),
      localFilePath: json['localFilePath']?.toString() ??
          json['file_path']?.toString() ??
          json['file_url']?.toString(),
      localBackFilePath: json['localBackFilePath']?.toString(),
      expiryDate: expDate,
      isUploaded: json['isUploaded'] as bool? ?? (json['has_file'] as bool? ?? false),
      fields: (json['fields'] as Map<String, dynamic>? ?? {})
          .map((k, v) => MapEntry(k, v.toString())),
      holderName: holderName,
      metadata: meta,
    );
  }

  String toJsonString() => jsonEncode(toJson());
  factory DocumentModel.fromJsonString(String s) =>
      DocumentModel.fromJson(jsonDecode(s) as Map<String, dynamic>);
}
