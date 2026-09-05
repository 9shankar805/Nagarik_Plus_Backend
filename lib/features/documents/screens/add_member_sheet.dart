import 'package:flutter/material.dart';
import 'package:dio/dio.dart';
import 'package:image_picker/image_picker.dart';
import 'package:file_picker/file_picker.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../providers/documents_provider.dart';
import 'documents_screen.dart' show DocTypeDef;
class AddMemberSheet extends StatefulWidget {
  final DocTypeDef typeDef;
  const AddMemberSheet({super.key, required this.typeDef});

  @override
  State<AddMemberSheet> createState() => _AddMemberSheetState();
}

class _AddMemberSheetState extends State<AddMemberSheet> {
  final _formKey      = GlobalKey<FormState>();
  final _holderCtrl   = TextEditingController();
  final _docNumCtrl   = TextEditingController();
  final _issuedByCtrl = TextEditingController();

  DateTime? _issueDate;
  DateTime? _expiryDate;
  String?   _filePath;
  String?   _fileName;
  bool      _uploading = false;
  double    _progress  = 0.0;

  // Quick-pick family members
  static const _quickNames = [
    'Myself', 'Father', 'Mother', 'Spouse',
    'Son', 'Daughter', 'Brother', 'Sister', 'Other',
  ];

  @override
  void dispose() {
    _holderCtrl.dispose();
    _docNumCtrl.dispose();
    _issuedByCtrl.dispose();
    super.dispose();
  }

  // ── Date pickers ────────────────────────────────────────────────────────
  Future<void> _pickIssueDate() async {
    final d = await showDatePicker(
      context: context,
      initialDate: _issueDate ?? DateTime.now(),
      firstDate: DateTime(1950), lastDate: DateTime.now(),
    );
    if (d != null) setState(() => _issueDate = d);
  }

  Future<void> _pickExpiryDate() async {
    final d = await showDatePicker(
      context: context,
      initialDate: _expiryDate ?? DateTime.now().add(const Duration(days: 365)),
      firstDate: DateTime.now().subtract(const Duration(days: 1)),
      lastDate: DateTime(2099),
    );
    if (d != null) setState(() => _expiryDate = d);
  }

  // ── File pickers ─────────────────────────────────────────────────────────
  Future<void> _pickFromCamera() async {
    final img = await ImagePicker().pickImage(
        source: ImageSource.camera, imageQuality: 90, maxWidth: 1920);
    if (img == null) return;
    setState(() { _filePath = img.path; _fileName = img.name; });
  }

  Future<void> _pickFromGallery() async {
    final img = await ImagePicker().pickImage(
        source: ImageSource.gallery, imageQuality: 90);
    if (img == null) return;
    setState(() { _filePath = img.path; _fileName = img.name; });
  }

  Future<void> _pickFile() async {
    final r = await FilePicker.platform.pickFiles(
        type: FileType.custom,
        allowedExtensions: ['pdf', 'jpg', 'jpeg', 'png']);
    if (r == null || r.files.single.path == null) return;
    setState(() {
      _filePath = r.files.single.path;
      _fileName = r.files.single.name;
    });
  }

  // ── Submit ────────────────────────────────────────────────────────────────
  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    FocusScope.of(context).unfocus();

    setState(() { _uploading = true; _progress = 0; });
    final provider  = context.read<DocumentsProvider>();
    final navigator = Navigator.of(context);
    final messenger = ScaffoldMessenger.of(context);

    try {
      final holder = _holderCtrl.text.trim();
      final title  = '$holder — ${widget.typeDef.label}';

      final fields = <String, dynamic>{
        'title':        title,
        'type':         widget.typeDef.type,
        'holder_name':  holder,
        if (_docNumCtrl.text.trim().isNotEmpty)
          'document_number': _docNumCtrl.text.trim(),
        if (_issuedByCtrl.text.trim().isNotEmpty)
          'issued_by': _issuedByCtrl.text.trim(),
        if (_issueDate != null)
          'issue_date': _issueDate!.toIso8601String().substring(0, 10),
        if (_expiryDate != null)
          'expiry_date': _expiryDate!.toIso8601String().substring(0, 10),
        'metadata': {'holder_name': holder},
      };

      if (_filePath != null) {
        fields['file'] = await MultipartFile.fromFile(
          _filePath!,
          filename: _fileName ?? _filePath!.split('/').last,
        );
      }

      final formData = FormData.fromMap(fields);
      await provider.uploadDocument(formData, onSendProgress: (s, t) {
        if (t > 0 && mounted) setState(() => _progress = s / t);
      });

      if (mounted) {
        navigator.pop(true); // signal refresh
        messenger.showSnackBar(SnackBar(
          content: Text('$holder\'s ${widget.typeDef.label} saved!'),
          backgroundColor: AppColors.success,
        ));
      }
    } catch (e) {
      if (mounted) {
        messenger.showSnackBar(SnackBar(
          content: Text('Failed: ${e.toString().replaceFirst('Exception: ', '')}'),
          backgroundColor: AppColors.danger,
        ));
      }
    } finally {
      if (mounted) setState(() => _uploading = false);
    }
  }

  // ── UI helpers ────────────────────────────────────────────────────────────
  String _fmtDate(DateTime d) =>
      '${d.day.toString().padLeft(2,'0')}/${d.month.toString().padLeft(2,'0')}/${d.year}';

  @override
  Widget build(BuildContext context) {
    final color = widget.typeDef.color;

    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      padding: EdgeInsets.fromLTRB(
          20, 16, 20, MediaQuery.of(context).viewInsets.bottom + 24),
      child: SingleChildScrollView(
        child: Form(
          key: _formKey,
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [

            // Handle
            Center(child: Container(width: 40, height: 4,
                decoration: BoxDecoration(color: AppColors.divider,
                    borderRadius: BorderRadius.circular(2)))),
            const SizedBox(height: 14),

            // Header
            Row(children: [
              Container(padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(color: color.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(10)),
                  child: Icon(widget.typeDef.icon, color: color, size: 22)),
              const SizedBox(width: 12),
              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                const Text('Add Member', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w800)),
                Text(widget.typeDef.label,
                    style: TextStyle(color: color, fontSize: 12, fontWeight: FontWeight.w600)),
              ])),
              IconButton(icon: const Icon(Icons.close_rounded),
                  onPressed: () => Navigator.pop(context)),
            ]),
            const SizedBox(height: 12),

            // ── Sync status indicator ─────────────────────────────────────
            Consumer<DocumentsProvider>(builder: (_, p, __) {
              final syncOn = p.syncEnabled;
              return Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                decoration: BoxDecoration(
                  color: syncOn
                      ? AppColors.success.withOpacity(0.08)
                      : Colors.orange.withOpacity(0.08),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(
                      color: syncOn
                          ? AppColors.success.withOpacity(0.3)
                          : Colors.orange.withOpacity(0.3)),
                ),
                child: Row(children: [
                  Icon(
                    syncOn ? Icons.cloud_done_rounded : Icons.phone_android_rounded,
                    color: syncOn ? AppColors.success : Colors.orange,
                    size: 16,
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      syncOn
                          ? 'Cloud sync ON — document will be uploaded to secure server'
                          : 'Sync OFF — document will be saved on this device only',
                      style: TextStyle(
                          color: syncOn ? AppColors.success : Colors.orange,
                          fontSize: 11, fontWeight: FontWeight.w600),
                    ),
                  ),
                ]),
              );
            }),
            const SizedBox(height: 16),

            // ── Quick-pick member ─────────────────────────────────────────
            const Text('Who is this for?',
                style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13, color: AppColors.textDark)),
            const SizedBox(height: 8),
            Wrap(
              spacing: 8, runSpacing: 8,
              children: _quickNames.map((n) {
                final sel = _holderCtrl.text == n;
                return GestureDetector(
                  onTap: () => setState(() => _holderCtrl.text = n),
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 180),
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 7),
                    decoration: BoxDecoration(
                      color: sel ? color : color.withOpacity(0.07),
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: sel ? color : color.withOpacity(0.25)),
                    ),
                    child: Text(n, style: TextStyle(
                        color: sel ? Colors.white : color,
                        fontSize: 12, fontWeight: FontWeight.w600)),
                  ),
                );
              }).toList(),
            ),
            const SizedBox(height: 14),

            // Member name field (free-type or quick-filled)
            TextFormField(
              controller: _holderCtrl,
              textCapitalization: TextCapitalization.words,
              decoration: _deco('Member Name *', Icons.person_rounded),
              validator: (v) => (v == null || v.trim().isEmpty) ? 'Enter a name' : null,
            ),
            const SizedBox(height: 12),

            // Document number
            TextFormField(
              controller: _docNumCtrl,
              decoration: _deco('Document Number', Icons.tag_rounded),
            ),
            const SizedBox(height: 12),

            // Issued by
            TextFormField(
              controller: _issuedByCtrl,
              decoration: _deco('Issued By', Icons.account_balance_rounded),
            ),
            const SizedBox(height: 14),

            // Date row
            Row(children: [
              Expanded(child: _DateTile(
                label: 'Issue Date',
                value: _issueDate != null ? _fmtDate(_issueDate!) : null,
                onTap: _pickIssueDate,
                color: color,
              )),
              const SizedBox(width: 10),
              Expanded(child: _DateTile(
                label: 'Expiry Date',
                value: _expiryDate != null ? _fmtDate(_expiryDate!) : null,
                onTap: _pickExpiryDate,
                color: color,
                isExpiry: true,
              )),
            ]),
            const SizedBox(height: 16),

            // ── File attachment ───────────────────────────────────────────
            const Text('Attach File (optional)',
                style: TextStyle(fontWeight: FontWeight.w700, fontSize: 13,
                    color: AppColors.textDark)),
            const SizedBox(height: 8),

            if (_filePath != null)
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: AppColors.success.withOpacity(0.08),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppColors.success.withOpacity(0.3)),
                ),
                child: Row(children: [
                  const Icon(Icons.check_circle_rounded, color: AppColors.success, size: 20),
                  const SizedBox(width: 10),
                  Expanded(child: Text(_fileName ?? 'File selected',
                      style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 12),
                      maxLines: 1, overflow: TextOverflow.ellipsis)),
                  GestureDetector(
                    onTap: () => setState(() { _filePath = null; _fileName = null; }),
                    child: const Icon(Icons.close_rounded, size: 18, color: AppColors.textLight),
                  ),
                ]),
              )
            else
              Row(children: [
                _FileBtn(icon: Icons.camera_alt_rounded, label: 'Camera',
                    color: color, onTap: _pickFromCamera),
                const SizedBox(width: 8),
                _FileBtn(icon: Icons.photo_library_rounded, label: 'Gallery',
                    color: color, onTap: _pickFromGallery),
                const SizedBox(width: 8),
                _FileBtn(icon: Icons.upload_file_rounded, label: 'File',
                    color: color, onTap: _pickFile),
              ]),

            const SizedBox(height: 20),

            // Upload progress
            if (_uploading) ...[
              LinearProgressIndicator(value: _progress > 0 ? _progress : null, color: color),
              const SizedBox(height: 8),
              Center(child: Text('Uploading… ${(_progress * 100).toInt()}%',
                  style: TextStyle(fontSize: 12, color: color, fontWeight: FontWeight.w600))),
              const SizedBox(height: 12),
            ],

            // Submit button
            Consumer<DocumentsProvider>(builder: (_, p, __) {
              final syncOn = p.syncEnabled;
              return SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  onPressed: _uploading ? null : _submit,
                  icon: _uploading
                      ? const SizedBox(width: 18, height: 18,
                          child: CircularProgressIndicator(
                              strokeWidth: 2, color: Colors.white))
                      : Icon(syncOn ? Icons.cloud_upload_rounded : Icons.save_rounded),
                  label: Text(
                    _uploading
                        ? (syncOn ? 'Uploading…' : 'Saving…')
                        : (syncOn ? 'Save & Upload to Cloud' : 'Save Locally'),
                    style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: syncOn ? color : Colors.orange,
                    foregroundColor: Colors.white,
                    disabledBackgroundColor: (syncOn ? color : Colors.orange).withOpacity(0.5),
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                  ),
                ),
              );
            }),
          ]),
        ),
      ),
    );
  }

  InputDecoration _deco(String label, IconData icon) => InputDecoration(
    labelText: label,
    prefixIcon: Icon(icon, size: 18),
    filled: true, fillColor: const Color(0xFFF5F7FA),
    border: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: AppColors.divider)),
    enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: AppColors.divider)),
    focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
        borderSide: BorderSide(color: widget.typeDef.color, width: 1.5)),
    contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
    labelStyle: const TextStyle(fontSize: 13),
  );
}

// ─────────────────────────────────────────────────────────────────────────────
// Helper widgets
// ─────────────────────────────────────────────────────────────────────────────
class _DateTile extends StatelessWidget {
  final String label;
  final String? value;
  final VoidCallback onTap;
  final Color color;
  final bool isExpiry;
  const _DateTile({required this.label, this.value, required this.onTap,
      required this.color, this.isExpiry = false});

  @override
  Widget build(BuildContext context) => GestureDetector(
    onTap: onTap,
    child: Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
      decoration: BoxDecoration(
        color: const Color(0xFFF5F7FA),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: value != null && isExpiry
            ? color.withOpacity(0.5) : AppColors.divider),
      ),
      child: Row(children: [
        Icon(Icons.calendar_today_rounded, size: 15,
            color: value != null ? color : AppColors.textLight),
        const SizedBox(width: 8),
        Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(label, style: const TextStyle(fontSize: 10, color: AppColors.textLight)),
          Text(value ?? 'Not set',
              style: TextStyle(
                  fontSize: 12, fontWeight: FontWeight.w600,
                  color: value != null ? AppColors.textDark : AppColors.textLight)),
        ])),
      ]),
    ),
  );
}

class _FileBtn extends StatelessWidget {
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;
  const _FileBtn({required this.icon, required this.label,
      required this.color, required this.onTap});

  @override
  Widget build(BuildContext context) => Expanded(
    child: GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12),
        decoration: BoxDecoration(
            color: color.withOpacity(0.07),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: color.withOpacity(0.2))),
        child: Column(children: [
          Icon(icon, color: color, size: 22),
          const SizedBox(height: 4),
          Text(label, style: TextStyle(color: color, fontSize: 10, fontWeight: FontWeight.w700)),
        ]),
      ),
    ),
  );
}
