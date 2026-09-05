import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';

class CreatePostScreen extends StatefulWidget {
  final bool isNepali;

  const CreatePostScreen({super.key, required this.isNepali});

  @override
  State<CreatePostScreen> createState() => _CreatePostScreenState();
}

class _CreatePostScreenState extends State<CreatePostScreen> {
  final TextEditingController _textController = TextEditingController();
  bool _canPost = false;

  @override
  void initState() {
    super.initState();
    _textController.addListener(() {
      setState(() {
        _canPost = _textController.text.trim().isNotEmpty;
      });
    });
  }

  @override
  void dispose() {
    _textController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        scrolledUnderElevation: 0, // Prevent color change on scroll
        leading: IconButton(
          icon: const Icon(Icons.close_rounded, color: Colors.black87, size: 28),
          onPressed: () => Navigator.of(context).pop(),
        ),
        title: Text(
          widget.isNepali ? 'पोस्ट सिर्जना गर्नुहोस्' : 'Create post',
          style: const TextStyle(color: Colors.black87, fontSize: 18, fontWeight: FontWeight.w600),
        ),
        centerTitle: true,
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(color: Colors.grey.shade200, height: 1),
        ),
        actions: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
            child: ElevatedButton(
              onPressed: _canPost
                  ? () {
                      Navigator.of(context).pop();
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text(widget.isNepali ? 'तपाईंको पोस्ट प्रकाशित भयो' : 'Your post was published.'),
                          behavior: SnackBarBehavior.floating,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                        ),
                      );
                    }
                  : null,
              style: ElevatedButton.styleFrom(
                backgroundColor: _canPost ? AppColors.primary : const Color(0xFFF1F5F9),
                foregroundColor: _canPost ? Colors.white : const Color(0xFF94A3B8),
                elevation: 0,
                padding: const EdgeInsets.symmetric(horizontal: 20),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(20), // Pill shape for modern look
                ),
              ),
              child: Text(
                widget.isNepali ? 'पोस्ट' : 'Post',
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
              ),
            ),
          ),
        ],
      ),
      body: Column(
        children: [
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      const CircleAvatar(
                        radius: 24,
                        backgroundColor: Color(0xFFE2E8F0),
                        backgroundImage: AssetImage('assets/icons/app_icon.png'), // Or person icon
                        child: Icon(Icons.person, color: Color(0xFF94A3B8), size: 30),
                      ),
                      const SizedBox(width: 12),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Ramesh Sharma',
                            style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16, color: Colors.black87),
                          ),
                          const SizedBox(height: 4),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                            decoration: BoxDecoration(
                              color: const Color(0xFFE2E8F0).withValues(alpha: 0.5),
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                const Icon(Icons.public_rounded, size: 14, color: Color(0xFF475569)),
                                const SizedBox(width: 6),
                                Text(
                                  widget.isNepali ? 'सार्वजनिक' : 'Public',
                                  style: const TextStyle(fontSize: 13, color: Color(0xFF475569), fontWeight: FontWeight.w600),
                                ),
                                const SizedBox(width: 4),
                                const Icon(Icons.arrow_drop_down_rounded, size: 18, color: Color(0xFF475569)),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  TextField(
                    controller: _textController,
                    maxLines: null,
                    autofocus: true,
                    style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w400, color: Colors.black87),
                    cursorColor: AppColors.primary,
                    decoration: InputDecoration(
                      hintText: widget.isNepali ? 'समाचार, गुनासो वा सरकारप्रतिको विचार राख्नुहोस्...' : "Share news, issues, or thoughts on governance...",
                      hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 24, fontWeight: FontWeight.w400),
                      border: InputBorder.none,
                      enabledBorder: InputBorder.none,
                      focusedBorder: InputBorder.none,
                      errorBorder: InputBorder.none,
                      disabledBorder: InputBorder.none,
                      contentPadding: EdgeInsets.zero,
                    ),
                  ),
                ],
              ),
            ),
          ),
          // Tool bar that stays above keyboard
          Container(
            decoration: BoxDecoration(
              color: Colors.white,
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.05),
                  blurRadius: 10,
                  offset: const Offset(0, -2),
                ),
              ],
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                // Quick actions horizontal bar
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                  child: Row(
                    children: [
                      const Icon(Icons.palette_rounded, color: Colors.amber, size: 28), // Background color icon
                      const Spacer(),
                      _buildQuickActionIcon(Icons.photo_library_rounded, Colors.green),
                      const SizedBox(width: 16),
                      _buildQuickActionIcon(Icons.person_add_alt_1_rounded, Colors.blue),
                      const SizedBox(width: 16),
                      _buildQuickActionIcon(Icons.emoji_emotions_rounded, Colors.orange),
                      const SizedBox(width: 16),
                      _buildQuickActionIcon(Icons.location_on_rounded, Colors.red),
                      const SizedBox(width: 16),
                      _buildQuickActionIcon(Icons.more_horiz_rounded, Colors.grey.shade600),
                    ],
                  ),
                ),
                SizedBox(height: MediaQuery.of(context).padding.bottom),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildQuickActionIcon(IconData icon, Color color) {
    return InkWell(
      onTap: () {},
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.all(8),
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.1),
          shape: BoxShape.circle,
        ),
        child: Icon(icon, color: color, size: 24),
      ),
    );
  }
}
