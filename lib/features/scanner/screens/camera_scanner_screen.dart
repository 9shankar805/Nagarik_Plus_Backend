import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:cunning_document_scanner/cunning_document_scanner.dart';
import 'package:permission_handler/permission_handler.dart';
import '../../../core/constants/app_colors.dart';
import '../controllers/scanner_controller.dart';
import 'review_screen.dart';

// Teal accent matching CamScanner
const _teal = Color(0xFF26A69A);

/// Launches the native document scanner (Google ML Kit / VisionKit),
/// feeds the auto-cropped images into ScannerController, then opens ReviewScreen.
class CameraScannerScreen extends StatefulWidget {
  const CameraScannerScreen({super.key});
  @override
  State<CameraScannerScreen> createState() => _CameraScannerScreenState();
}

class _CameraScannerScreenState extends State<CameraScannerScreen> {
  bool    _launching = false;
  String? _error;

  @override
  void initState() {
    super.initState();
    // Auto-launch the native scanner as soon as the screen opens
    WidgetsBinding.instance.addPostFrameCallback((_) => _launchScanner());
  }

  Future<void> _launchScanner() async {
    if (_launching) return;
    setState(() { _launching = true; _error = null; });

    // ── 1. Request camera permission ────────────────────────────────────────
    final status = await Permission.camera.request();
    if (!mounted) return;

    if (!status.isGranted) {
      setState(() {
        _launching = false;
        _error = status.isPermanentlyDenied
            ? 'Camera access required.\nEnable it in device Settings.'
            : 'Camera permission denied.';
      });
      return;
    }

    try {
      // ── 2. Launch native document scanner ──────────────────────────────────
      // getPictures() has no named params in all package versions — use
      // the basic call which works across v1.x and v2.x.
      final pictures = await CunningDocumentScanner.getPictures();

      if (!mounted) return;

      if (pictures == null || pictures.isEmpty) {
        // User cancelled — go back
        Navigator.of(context).pop();
        return;
      }

      // ── 3. Feed images into controller ─────────────────────────────────────
      final ctrl = context.read<ScannerController>();
      ctrl.reset(); // clear any previous session

      for (final path in pictures) {
        await ctrl.addPreCroppedPage(path);
        if (!mounted) return;
      }

      // ── 4. Navigate to review screen ───────────────────────────────────────
      if (!mounted) return;
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(
          builder: (_) => ChangeNotifierProvider.value(
            value: ctrl,
            child: const ReviewScreen(),
          ),
        ),
      );
    } catch (e) {
      if (!mounted) return;

      final msg = e.toString();
      // User cancelled (different packages surface cancel as exception or empty list)
      if (msg.contains('CANCELLED') ||
          msg.contains('cancel') ||
          msg.contains('Cancel')) {
        Navigator.of(context).pop();
        return;
      }

      setState(() {
        _launching = false;
        _error = 'Could not launch scanner.\n$msg';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_error == null) {
      // ── Loading / launching state ───────────────────────────────────────────
      return Scaffold(
        backgroundColor: Colors.black,
        body: SafeArea(
          child: Column(children: [
            // Top bar
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
              child: Row(children: [
                GestureDetector(
                  onTap: () => Navigator.of(context).pop(),
                  child: const Icon(Icons.close_rounded, color: Colors.white, size: 26),
                ),
                const Spacer(),
                const Text('Nagarik Scan',
                    style: TextStyle(color: Colors.white, fontSize: 17, fontWeight: FontWeight.w700)),
                const Spacer(),
                const SizedBox(width: 26),
              ]),
            ),

            const Expanded(
              child: Center(
                child: Column(mainAxisSize: MainAxisSize.min, children: [
                  _ScannerAnimation(),
                  SizedBox(height: 32),
                  Text('Launching document scanner…',
                      style: TextStyle(color: Colors.white70, fontSize: 15, fontWeight: FontWeight.w500)),
                  SizedBox(height: 10),
                  Text(
                    'Point the camera at any document.\nEdges will be detected automatically.',
                    textAlign: TextAlign.center,
                    style: TextStyle(color: Colors.white38, fontSize: 13, height: 1.5),
                  ),
                ]),
              ),
            ),

            // Bottom tips
            Padding(
              padding: const EdgeInsets.fromLTRB(24, 0, 24, 32),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceAround,
                children: const [
                  _Tip(Icons.wb_sunny_outlined, 'Good lighting'),
                  _Tip(Icons.crop_free_rounded, 'Auto edge detect'),
                  _Tip(Icons.layers_rounded, 'Multi-page'),
                ],
              ),
            ),
          ]),
        ),
      );
    }

    // ── Error / permission denied state ──────────────────────────────────────
    return Scaffold(
      backgroundColor: Colors.black,
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                    color: AppColors.danger.withValues(alpha: 0.15),
                    shape: BoxShape.circle),
                child: const Icon(Icons.camera_alt_outlined,
                    size: 56, color: AppColors.danger),
              ),
              const SizedBox(height: 24),
              const Text('Scanner Error',
                  style: TextStyle(color: Colors.white, fontSize: 20,
                      fontWeight: FontWeight.w800),
                  textAlign: TextAlign.center),
              const SizedBox(height: 12),
              Text(_error!,
                  style: const TextStyle(color: Colors.white54, fontSize: 14, height: 1.6),
                  textAlign: TextAlign.center),
              const SizedBox(height: 32),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  onPressed: () async {
                    if (_error!.contains('Settings')) {
                      await openAppSettings();
                    } else {
                      _launchScanner();
                    }
                  },
                  icon: Icon(_error!.contains('Settings')
                      ? Icons.settings_rounded
                      : Icons.refresh_rounded),
                  label: Text(_error!.contains('Settings')
                      ? 'Open Settings'
                      : 'Try Again'),
                  style: ElevatedButton.styleFrom(
                      backgroundColor: _teal,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12))),
                ),
              ),
              const SizedBox(height: 12),
              TextButton(
                onPressed: () => Navigator.of(context).pop(),
                child: const Text('Go Back', style: TextStyle(color: Colors.white54)),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Animated scanner icon while native UI loads
// ─────────────────────────────────────────────────────────────────────────────
class _ScannerAnimation extends StatefulWidget {
  const _ScannerAnimation();
  @override
  State<_ScannerAnimation> createState() => _ScannerAnimationState();
}

class _ScannerAnimationState extends State<_ScannerAnimation>
    with SingleTickerProviderStateMixin {
  late final AnimationController _ctrl;
  late final Animation<double>   _anim;

  @override
  void initState() {
    super.initState();
    _ctrl = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 1400))
      ..repeat(reverse: true);
    _anim = CurvedAnimation(parent: _ctrl, curve: Curves.easeInOut);
  }

  @override
  void dispose() { _ctrl.dispose(); super.dispose(); }

  @override
  Widget build(BuildContext context) => AnimatedBuilder(
    animation: _anim,
    builder: (_, __) => SizedBox(
      width: 160, height: 120,
      child: CustomPaint(painter: _ScanFramePainter(_anim.value)),
    ),
  );
}

class _ScanFramePainter extends CustomPainter {
  final double progress;
  _ScanFramePainter(this.progress);

  @override
  void paint(Canvas canvas, Size size) {
    const cornerLen = 22.0;
    final paint = Paint()
      ..color = _teal
      ..strokeWidth = 3
      ..style = PaintingStyle.stroke
      ..strokeCap = StrokeCap.round;

    final corners = [
      Offset.zero,
      Offset(size.width, 0),
      Offset(size.width, size.height),
      Offset(0, size.height),
    ];
    final dirs = [
      [const Offset(cornerLen, 0), const Offset(0, cornerLen)],
      [const Offset(-cornerLen, 0), const Offset(0, cornerLen)],
      [const Offset(-cornerLen, 0), const Offset(0, -cornerLen)],
      [const Offset(cornerLen, 0), const Offset(0, -cornerLen)],
    ];
    for (int i = 0; i < 4; i++) {
      final c = corners[i];
      canvas.drawLine(c, c + dirs[i][0], paint);
      canvas.drawLine(c, c + dirs[i][1], paint);
    }

    // Animated scan line
    final scanY = size.height * progress;
    final linePaint = Paint()
      ..shader = LinearGradient(colors: [
        _teal.withValues(alpha: 0.0),
        _teal.withValues(alpha: 0.85),
        _teal.withValues(alpha: 0.0),
      ]).createShader(Rect.fromLTWH(0, scanY - 1, size.width, 2))
      ..strokeWidth = 2;
    canvas.drawLine(Offset(8, scanY), Offset(size.width - 8, scanY), linePaint);
  }

  @override
  bool shouldRepaint(_ScanFramePainter old) => old.progress != progress;
}

class _Tip extends StatelessWidget {
  final IconData icon;
  final String   label;
  const _Tip(this.icon, this.label);
  @override
  Widget build(BuildContext context) => Column(mainAxisSize: MainAxisSize.min, children: [
    Icon(icon, color: Colors.white38, size: 22),
    const SizedBox(height: 6),
    Text(label, style: const TextStyle(
        color: Colors.white38, fontSize: 11, fontWeight: FontWeight.w500)),
  ]);
}
