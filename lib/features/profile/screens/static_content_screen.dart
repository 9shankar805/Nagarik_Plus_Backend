import 'package:flutter/material.dart';
import 'package:nagarik_plus/core/constants/app_colors.dart';

class StaticContentScreen extends StatelessWidget {
  final String title;
  final String content;

  const StaticContentScreen({
    super.key,
    required this.title,
    required this.content,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: Text(title, style: const TextStyle(fontWeight: FontWeight.w700)),
        backgroundColor: Colors.white,
        foregroundColor: AppColors.textDark,
        elevation: 0,
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        physics: const BouncingScrollPhysics(),
        padding: const EdgeInsets.all(24.0),
        child: content.isEmpty
            ? Center(
                child: Padding(
                  padding: const EdgeInsets.only(top: 100.0),
                  child: Text(
                    'No content available.',
                    style: TextStyle(color: AppColors.textMedium, fontSize: 16),
                  ),
                ),
              )
            : Text(
                content,
                style: const TextStyle(
                  fontSize: 15,
                  height: 1.6,
                  color: AppColors.textDark,
                ),
              ),
      ),
    );
  }
}
