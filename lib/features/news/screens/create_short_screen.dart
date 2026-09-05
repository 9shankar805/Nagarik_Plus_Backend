import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:async';
import '../../../core/constants/app_colors.dart';

class CreateShortScreen extends StatefulWidget {
  final bool isNepali;

  const CreateShortScreen({super.key, required this.isNepali});

  @override
  State<CreateShortScreen> createState() => _CreateShortScreenState();
}

class _CreateShortScreenState extends State<CreateShortScreen> with TickerProviderStateMixin {
  int _selectedDuration = 15;
  bool _isRecording = false;
  
  // Tools State
  bool _isFlashOn = false;
  double _selectedSpeed = 1.0;
  bool _showSpeedOptions = false;
  
  // Timer State
  int _countdown = 0;
  Timer? _countdownTimer;
  
  // Image Picker
  final ImagePicker _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
  }

  @override
  void dispose() {
    _countdownTimer?.cancel();
    super.dispose();
  }

  void _toggleFlash() {
    setState(() => _isFlashOn = !_isFlashOn);
  }

  void _startTimer() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (context) => _buildTimerBottomSheet(),
    );
  }

  void _beginCountdown(int seconds) {
    Navigator.of(context).pop(); // Close bottom sheet
    setState(() => _countdown = seconds);
    
    _countdownTimer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_countdown > 1) {
        setState(() => _countdown--);
      } else {
        timer.cancel();
        setState(() {
          _countdown = 0;
          _isRecording = true;
        });
        
        // Auto stop after duration
        Future.delayed(Duration(seconds: _selectedDuration), () {
          if (_isRecording) _stopRecording();
        });
      }
    });
  }

  void _stopRecording() {
    setState(() => _isRecording = false);
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(widget.isNepali ? 'भिडियो सेभ गरियो!' : 'Video recorded successfully!'),
        backgroundColor: AppColors.primary,
        behavior: SnackBarBehavior.floating,
      )
    );
  }

  Future<void> _uploadVideo() async {
    final XFile? video = await _picker.pickVideo(source: ImageSource.gallery);
    if (!mounted) return;
    if (video != null) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(widget.isNepali ? 'भिडियो चयन गरियो' : 'Video selected: ${video.name}'),
          backgroundColor: AppColors.primary,
        )
      );
    }
  }

  void _showFilters() {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      isScrollControlled: true,
      builder: (context) => _buildFiltersBottomSheet(),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      body: SafeArea(
        child: Stack(
          fit: StackFit.expand,
          children: [
            // ── CAMERA PREVIEW PLACEHOLDER ───────────────────────────────────────
            Container(
              margin: const EdgeInsets.only(bottom: 90),
              decoration: BoxDecoration(
                color: Colors.grey[900],
                borderRadius: BorderRadius.circular(20),
                border: Border.all(color: Colors.white12, width: 1),
              ),
              clipBehavior: Clip.hardEdge,
              child: const Center(
                child: Text('Camera Preview Not Available\n(DeepAR Removed)', textAlign: TextAlign.center, style: TextStyle(color: Colors.white54)),
              ),
            ),

            // ── COUNTDOWN OVERLAY ───────────────────────────────────────────
            if (_countdown > 0)
              Center(
                child: TweenAnimationBuilder<double>(
                  tween: Tween(begin: 0.5, end: 1.0),
                  duration: const Duration(milliseconds: 500),
                  builder: (context, val, child) {
                    return Transform.scale(
                      scale: val,
                      child: Text(
                        '$_countdown',
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 120,
                          fontWeight: FontWeight.bold,
                          shadows: [Shadow(color: AppColors.primary, blurRadius: 20)],
                        ),
                      ),
                    );
                  },
                ),
              ),

            // ── TOP CONTROLS ────────────────────────────────────────────────
            Positioned(
              top: 16,
              left: 16,
              right: 16,
              child: Row(
                children: [
                  IconButton(
                    icon: const Icon(Icons.close_rounded, color: Colors.white, size: 28),
                    onPressed: () => Navigator.of(context).pop(),
                  ),
                  const Spacer(),
                  // Add Sound Button
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
                    decoration: BoxDecoration(
                      color: Colors.black45,
                      borderRadius: BorderRadius.circular(24),
                      border: Border.all(color: Colors.white24),
                    ),
                    child: Row(
                      children: [
                        const Icon(Icons.music_note_rounded, color: Colors.white, size: 18),
                        const SizedBox(width: 8),
                        Text(
                          widget.isNepali ? 'संगीत थप्नुहोस्' : 'Add Sound',
                          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14),
                        ),
                      ],
                    ),
                  ),
                  const Spacer(),
                  const SizedBox(width: 48), // Balance for the close button
                ],
              ),
            ),

            // ── SPEED OPTIONS (Horizontal bar) ──────────────────────────────
            if (_showSpeedOptions)
              Positioned(
                top: 80,
                right: 70,
                child: Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  decoration: BoxDecoration(
                    color: Colors.black54,
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(color: Colors.white24),
                  ),
                  child: Row(
                    children: [0.3, 0.5, 1.0, 2.0, 3.0].map((speed) {
                      final isSel = _selectedSpeed == speed;
                      return GestureDetector(
                        onTap: () {
                          setState(() {
                            _selectedSpeed = speed;
                            _showSpeedOptions = false;
                          });
                        },
                        child: Container(
                          margin: const EdgeInsets.symmetric(horizontal: 6),
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: isSel ? Colors.white : Colors.transparent,
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Text(
                            '${speed}x',
                            style: TextStyle(
                              color: isSel ? Colors.black : Colors.white,
                              fontWeight: FontWeight.bold,
                              fontSize: 12,
                            ),
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                ),
              ),

            // ── RIGHT SIDE TOOLBAR ──────────────────────────────────────────
            Positioned(
              top: 80,
              right: 12,
              child: Column(
                children: [
                  _buildToolIcon(Icons.flip_camera_ios_rounded, widget.isNepali ? 'फ्लिप' : 'Flip', onTap: () {}),
                  const SizedBox(height: 16),
                  _buildToolIcon(Icons.speed_rounded, widget.isNepali ? 'गति' : 'Speed', isActive: _selectedSpeed != 1.0, onTap: () {
                    setState(() => _showSpeedOptions = !_showSpeedOptions);
                  }),
                  const SizedBox(height: 16),
                  _buildToolIcon(Icons.face_retouching_natural_rounded, widget.isNepali ? 'फिल्टर' : 'Filter', onTap: _showFilters),
                  const SizedBox(height: 16),
                  _buildToolIcon(Icons.timer_rounded, widget.isNepali ? 'टाइमर' : 'Timer', onTap: _startTimer),
                  const SizedBox(height: 16),
                  _buildToolIcon(_isFlashOn ? Icons.flash_on_rounded : Icons.flash_off_rounded, widget.isNepali ? 'फ्ल्यास' : 'Flash', isActive: _isFlashOn, onTap: _toggleFlash),
                ],
              ),
            ),

            // ── BOTTOM CONTROLS ─────────────────────────────────────────────
            Positioned(
              bottom: 0,
              left: 0,
              right: 0,
              child: Column(
                children: [
                  // Duration Selector
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      _buildDurationOption(15, '15s'),
                      const SizedBox(width: 24),
                      _buildDurationOption(60, '60s'),
                      const SizedBox(width: 24),
                      _buildDurationOption(180, '3m'),
                    ],
                  ),
                  const SizedBox(height: 20),
                  // Record Buttons
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        // Effects Button
                        GestureDetector(
                          onTap: _showFilters,
                          child: Column(
                            children: [
                              Container(
                                padding: const EdgeInsets.all(12),
                                decoration: BoxDecoration(
                                  color: Colors.white24,
                                  borderRadius: BorderRadius.circular(12),
                                ),
                                child: const Icon(Icons.auto_awesome_rounded, color: Colors.white, size: 28),
                              ),
                              const SizedBox(height: 6),
                              Text(widget.isNepali ? 'प्रभावहरू' : 'Effects', style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w600)),
                            ],
                          ),
                        ),

                        // Record Button
                        GestureDetector(
                          onTapDown: (_) {
                            if (_countdown > 0) return;
                            setState(() => _isRecording = true);
                          },
                          onTapUp: (_) {
                            if (_countdown > 0) return;
                            _stopRecording();
                          },
                          onTapCancel: () {
                            if (_countdown > 0) return;
                            _stopRecording();
                          },
                          child: AnimatedContainer(
                            duration: const Duration(milliseconds: 200),
                            width: _isRecording ? 90 : 80,
                            height: _isRecording ? 90 : 80,
                            padding: const EdgeInsets.all(4),
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              border: Border.all(color: _isRecording ? Colors.redAccent : AppColors.primary, width: 4),
                            ),
                            child: AnimatedContainer(
                              duration: const Duration(milliseconds: 200),
                              decoration: BoxDecoration(
                                color: _isRecording ? Colors.redAccent : AppColors.primary,
                                borderRadius: _isRecording ? BorderRadius.circular(8) : BorderRadius.circular(40),
                              ),
                            ),
                          ),
                        ),

                        // Upload Button
                        GestureDetector(
                          onTap: _uploadVideo,
                          child: Column(
                            children: [
                              Container(
                                padding: const EdgeInsets.all(12),
                                decoration: BoxDecoration(
                                  color: Colors.white24,
                                  borderRadius: BorderRadius.circular(12),
                                ),
                                child: const Icon(Icons.photo_library_rounded, color: Colors.white, size: 28),
                              ),
                              const SizedBox(height: 6),
                              Text(widget.isNepali ? 'अपलोड' : 'Upload', style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w600)),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildToolIcon(IconData icon, String label, {VoidCallback? onTap, bool isActive = false}) {
    return GestureDetector(
      onTap: onTap,
      child: Column(
        children: [
          Icon(icon, color: isActive ? AppColors.primary : Colors.white, size: 28),
          const SizedBox(height: 4),
          Text(
            label,
            style: TextStyle(
              color: isActive ? AppColors.primary : Colors.white,
              fontSize: 11,
              fontWeight: FontWeight.bold,
              shadows: const [Shadow(color: Colors.black54, blurRadius: 4)],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDurationOption(int seconds, String label) {
    final isSelected = _selectedDuration == seconds;
    return GestureDetector(
      onTap: () => setState(() => _selectedDuration = seconds),
      child: Text(
        label,
        style: TextStyle(
          color: isSelected ? Colors.white : Colors.white54,
          fontWeight: isSelected ? FontWeight.bold : FontWeight.w600,
          fontSize: 15,
          shadows: isSelected ? const [Shadow(color: Colors.black54, blurRadius: 4)] : null,
        ),
      ),
    );
  }

  Widget _buildTimerBottomSheet() {
    return Container(
      decoration: const BoxDecoration(
        color: Color(0xFF1E293B),
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      padding: const EdgeInsets.all(24),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 40,
            height: 4,
            decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(2)),
          ),
          const SizedBox(height: 24),
          Text(
            widget.isNepali ? 'टाइमर छनौट गर्नुहोस्' : 'Select Timer',
            style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 24),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
            children: [
              _timerOption(3, '3s'),
              _timerOption(10, '10s'),
            ],
          ),
          const SizedBox(height: 32),
        ],
      ),
    );
  }

  Widget _timerOption(int seconds, String label) {
    return GestureDetector(
      onTap: () => _beginCountdown(seconds),
      child: Container(
        width: 80,
        height: 80,
        decoration: BoxDecoration(
          color: Colors.white12,
          shape: BoxShape.circle,
          border: Border.all(color: AppColors.primary, width: 2),
        ),
        child: Center(
          child: Text(
            label,
            style: const TextStyle(color: Colors.white, fontSize: 24, fontWeight: FontWeight.bold),
          ),
        ),
      ),
    );
  }

  Widget _buildFiltersBottomSheet() {
    return DraggableScrollableSheet(
      initialChildSize: 0.5,
      minChildSize: 0.3,
      maxChildSize: 0.8,
      builder: (_, controller) {
        return Container(
          decoration: const BoxDecoration(
            color: Color(0xFF0F172A),
            borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
          ),
          child: Column(
            children: [
              const SizedBox(height: 12),
              Container(width: 40, height: 4, decoration: BoxDecoration(color: Colors.white24, borderRadius: BorderRadius.circular(2))),
              const SizedBox(height: 16),
              Text(
                widget.isNepali ? 'प्रभावहरू र फिल्टरहरू' : 'Effects & Filters',
                style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),
              Expanded(
                child: GridView.builder(
                  controller: controller,
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                    crossAxisCount: 4,
                    crossAxisSpacing: 12,
                    mainAxisSpacing: 16,
                    childAspectRatio: 0.8,
                  ),
                  itemCount: 16,
                  itemBuilder: (context, index) {
                    return Column(
                      children: [
                        Expanded(
                          child: Container(
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: Colors.primaries[index % Colors.primaries.length].withValues(alpha: 0.5),
                              border: Border.all(color: Colors.white24),
                            ),
                            child: const Center(
                              child: Icon(Icons.face_retouching_natural, color: Colors.white, size: 24),
                            ),
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'Filter ${index + 1}',
                          style: const TextStyle(color: Colors.white70, fontSize: 10),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    );
                  },
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}
