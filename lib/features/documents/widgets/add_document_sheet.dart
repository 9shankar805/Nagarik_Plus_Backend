import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:file_picker/file_picker.dart';
import 'package:dio/dio.dart';
import 'package:provider/provider.dart';

import '../../../core/constants/app_colors.dart';
import '../../scanner/screens/camera_scanner_screen.dart';
import '../../scanner/controllers/scanner_controller.dart';
import '../providers/documents_provider.dart';

class AddDocumentSheet extends StatefulWidget {
  final String documentType;
  const AddDocumentSheet({super.key, this.documentType = 'other'});

  @override
  State<AddDocumentSheet> createState() => _AddDocumentSheetState();
}

class _AddDocumentSheetState extends State<AddDocumentSheet> {
  bool _isUploading = false;
  double _progress = 0.0;

  Future<void> _handleFilePick(BuildContext context, {required bool isCamera}) async {
    final provider = context.read<DocumentsProvider>();
    final navigator = Navigator.of(context);
    final messenger = ScaffoldMessenger.of(context);
    try {
      String? filePath;
      String title = 'Uploaded Document';
      if (isCamera) {
        final picker = ImagePicker();
        final picked = await picker.pickImage(source: ImageSource.camera);
        filePath = picked?.path;
      } else {
        final result = await FilePicker.platform.pickFiles(
          type: FileType.custom,
          allowedExtensions: ['pdf', 'jpg', 'jpeg', 'png'],
        );
        filePath = result?.files.single.path;
        if (result?.files.single.name != null) {
          title = result!.files.single.name;
        }
      }

      if (filePath == null) return;

      setState(() {
        _isUploading = true;
        _progress = 0.0;
      });

      final formData = FormData.fromMap({
        'title': title,
        'type': widget.documentType,
        'file': await MultipartFile.fromFile(
          filePath,
          filename: filePath.split('/').last.split('\\').last,
        ),
      });

      if (!mounted) return;
      await provider.uploadDocument(
        formData,
        onSendProgress: (sent, total) {
          if (total > 0 && mounted) {
            setState(() => _progress = sent / total);
          }
        },
      );

      if (mounted) {
        navigator.pop();
        messenger.showSnackBar(
          const SnackBar(
            content: Text('Document saved securely in Digital Locker!'),
            backgroundColor: AppColors.secondary,
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        messenger.showSnackBar(
          SnackBar(
            content: Text('Upload failed: ${e.toString().replaceFirst('Exception: ', '')}'),
            backgroundColor: AppColors.danger,
          ),
        );
      }
    } finally {
      if (mounted) {
        setState(() => _isUploading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final options = [
      _UploadOption(Icons.camera_alt_rounded, 'Take Photo', AppColors.primary),
      _UploadOption(Icons.upload_file_rounded, 'Upload File', AppColors.secondary),
      _UploadOption(Icons.document_scanner_rounded, 'Scan Document', AppColors.accent),
    ];

    return Container(
      padding: const EdgeInsets.all(24),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Text(
                'Add Document',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.w800,
                ),
              ),
              const Spacer(),
              IconButton(
                onPressed: () => Navigator.pop(context),
                icon: const Icon(Icons.close_rounded),
              ),
            ],
          ),
          const SizedBox(height: 16),
          if (_isUploading) ...[
            LinearProgressIndicator(value: _progress > 0 ? _progress : null, color: AppColors.primary),
            const SizedBox(height: 12),
            Center(
              child: Text(
                'Uploading document... ${(_progress * 100).toInt()}%',
                style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.primary),
              ),
            ),
            const SizedBox(height: 16),
          ] else
            Row(
              children: options.asMap().entries.map((entry) {
                final i = entry.key;
                final o = entry.value;
                return Expanded(
                  child: GestureDetector(
                    onTap: () {
                      if (i == 0) {
                        _handleFilePick(context, isCamera: true);
                      } else if (i == 1) {
                        _handleFilePick(context, isCamera: false);
                      } else if (i == 2) {
                        Navigator.pop(context);
                        Navigator.of(context).push(MaterialPageRoute(
                            builder: (_) => ChangeNotifierProvider(
                                  create: (_) => ScannerController(),
                                  child: const CameraScannerScreen(),
                                )));
                      }
                    },
                    child: Container(
                      margin: const EdgeInsets.symmetric(horizontal: 4),
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      decoration: BoxDecoration(
                        color: o.color.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: o.color.withValues(alpha: 0.2)),
                      ),
                      child: Column(
                        children: [
                          Icon(o.icon, color: o.color, size: 32),
                          const SizedBox(height: 8),
                          Text(
                            o.label,
                            textAlign: TextAlign.center,
                            style: TextStyle(
                              color: o.color,
                              fontSize: 12,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                );
              }).toList(),
            ),
        ],
      ),
    );
  }
}

class _UploadOption {
  final IconData icon;
  final String label;
  final Color color;
  _UploadOption(this.icon, this.label, this.color);
}
