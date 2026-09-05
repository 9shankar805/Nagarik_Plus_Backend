import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../providers/auth_provider.dart';

// ─────────────────────────────────────────────────────────────────────────────
// ForgotPasswordScreen — 2-step flow
//   Step 1: Enter email  → calls POST /auth/forgot-password
//   Step 2: Enter OTP + new password + confirm → calls POST /auth/reset-password
// ─────────────────────────────────────────────────────────────────────────────
class ForgotPasswordScreen extends StatefulWidget {
  const ForgotPasswordScreen({super.key});

  @override
  State<ForgotPasswordScreen> createState() => _ForgotPasswordScreenState();
}

class _ForgotPasswordScreenState extends State<ForgotPasswordScreen> {
  int _step = 0; // 0=email, 1=otp, 2=new password

  // Step 1
  final _emailCtrl = TextEditingController();

  // Step 2
  final List<TextEditingController> _otpCtrls =
      List.generate(6, (_) => TextEditingController());
  final List<FocusNode> _otpFocuses = List.generate(6, (_) => FocusNode());

  // Step 3
  final _passCtrl    = TextEditingController();
  final _confirmCtrl = TextEditingController();
  bool _showPass     = false;
  bool _showConfirm  = false;

  bool _loading = false;
  String? _error;

  @override
  void dispose() {
    _emailCtrl.dispose();
    for (final c in _otpCtrls) c.dispose();
    for (final f in _otpFocuses) f.dispose();
    _passCtrl.dispose();
    _confirmCtrl.dispose();
    super.dispose();
  }

  String get _otp => _otpCtrls.map((c) => c.text).join();

  // ── Step 1: send OTP ──────────────────────────────────────────────────────
  Future<void> _sendOtp() async {
    final email = _emailCtrl.text.trim();
    if (email.isEmpty || !email.contains('@')) {
      setState(() => _error = 'Enter a valid email address.');
      return;
    }
    setState(() { _loading = true; _error = null; });
    try {
      final p = context.read<AuthProvider>();
      await p.forgotPassword(email: email);
      if (mounted) setState(() { _step = 1; _loading = false; });
    } catch (e) {
      if (mounted) setState(() {
        _error = 'Could not send OTP. Check your email and try again.';
        _loading = false;
      });
    }
  }

  // ── Step 2: verify OTP ────────────────────────────────────────────────────
  Future<void> _verifyOtp() async {
    if (_otp.length < 6) {
      setState(() => _error = 'Enter the complete 6-digit OTP.');
      return;
    }
    setState(() { _loading = true; _error = null; });
    try {
      final p = context.read<AuthProvider>();
      await p.verifyResetPasswordOtp(
        email: _emailCtrl.text.trim(),
        otp:   _otp,
      );
      if (mounted) setState(() { _step = 2; _loading = false; });
    } catch (e) {
      if (mounted) setState(() {
        _error = 'Invalid or expired OTP. Please try again.';
        _loading = false;
      });
    }
  }

  // ── Step 3: reset password ────────────────────────────────────────────────
  Future<void> _resetPassword() async {
    final pass    = _passCtrl.text;
    final confirm = _confirmCtrl.text;
    if (pass.length < 8) {
      setState(() => _error = 'Password must be at least 8 characters.');
      return;
    }
    if (pass != confirm) {
      setState(() => _error = 'Passwords do not match.');
      return;
    }
    setState(() { _loading = true; _error = null; });
    try {
      final p = context.read<AuthProvider>();
      await p.resetPassword(
        email:                _emailCtrl.text.trim(),
        otp:                  _otp,
        password:             pass,
        passwordConfirmation: confirm,
      );
      if (!mounted) return;
      // Success — go back to login
      Navigator.of(context).pop();
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Password reset successfully! Please log in.'),
          backgroundColor: AppColors.success,
        ),
      );
    } catch (e) {
      if (mounted) setState(() {
        _error = e.toString().contains('otp_invalid')
            ? 'Invalid or expired OTP. Please try again.'
            : 'Reset failed. Please try again.';
        _loading = false;
      });
    }
  }

  // ── Build ─────────────────────────────────────────────────────────────────
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
              // ── Progress dots ─────────────────────────────────────────
              Row(children: List.generate(3, (i) => Container(
                margin: const EdgeInsets.only(right: 6),
                width: i == _step ? 24 : 8,
                height: 8,
                decoration: BoxDecoration(
                  color: i <= _step ? AppColors.primary : AppColors.divider,
                  borderRadius: BorderRadius.circular(4),
                ),
              ))),
              const SizedBox(height: 32),

              // ── Icon ─────────────────────────────────────────────────
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: AppColors.primary.withOpacity(0.08),
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  _step == 0 ? Icons.email_outlined
                  : Icons.lock_reset_rounded,
                  color: AppColors.primary,
                  size: 32,
                ),
              ),
              const SizedBox(height: 20),

              // ── Title + subtitle ──────────────────────────────────────
              Text(
                _step == 0 ? 'Forgot Password?'
                : _step == 1 ? 'Verify OTP'
                : 'Reset Password',
                style: const TextStyle(
                    fontSize: 26, fontWeight: FontWeight.w800,
                    color: AppColors.textDark),
              ),
              const SizedBox(height: 8),
              Text(
                _step == 0
                    ? 'Enter your registered email. We\'ll send a 6-digit OTP.'
                    : _step == 1
                        ? 'Enter the 6-digit code sent to ${_emailCtrl.text.trim()}.'
                        : 'Choose a new password for your account.',
                style: const TextStyle(
                    fontSize: 14, color: AppColors.textMedium, height: 1.5),
              ),
              const SizedBox(height: 32),

              // ── Error ────────────────────────────────────────────────
              if (_error != null) ...[
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: AppColors.danger.withOpacity(0.08),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: AppColors.danger.withOpacity(0.3)),
                  ),
                  child: Row(children: [
                    const Icon(Icons.error_outline_rounded,
                        color: AppColors.danger, size: 18),
                    const SizedBox(width: 8),
                    Expanded(child: Text(_error!,
                        style: const TextStyle(
                            color: AppColors.danger, fontSize: 13))),
                  ]),
                ),
                const SizedBox(height: 20),
              ],

              // ── Step content ──────────────────────────────────────────
              if (_step == 0) _buildEmailStep(),
              if (_step == 1) _buildOtpStep(),
              if (_step == 2) _buildResetStep(),
            ],
          ),
        ),
      ),
    );
  }

  // ── Step 0: Email ─────────────────────────────────────────────────────────
  Widget _buildEmailStep() => Column(children: [
    TextField(
      controller: _emailCtrl,
      keyboardType: TextInputType.emailAddress,
      autofillHints: const [AutofillHints.email],
      decoration: InputDecoration(
        labelText: 'Email Address',
        hintText: 'you@example.com',
        prefixIcon: const Icon(Icons.email_outlined, color: AppColors.primary),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: AppColors.primary, width: 2),
        ),
      ),
      onSubmitted: (_) => _sendOtp(),
    ),
    const SizedBox(height: 24),
    _SubmitButton(
      label: 'Send OTP',
      icon: Icons.send_rounded,
      loading: _loading,
      onPressed: _sendOtp,
    ),
  ]);

  // ── Step 1: OTP ───────────────────────────────────────────────────────────
  Widget _buildOtpStep() => Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
    const Text('OTP Code', style: TextStyle(fontWeight: FontWeight.w700, color: AppColors.textDark, fontSize: 15)),
    const SizedBox(height: 12),
    Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: List.generate(6, (i) => SizedBox(
        width: 48,
        height: 56,
        child: TextField(
          controller: _otpCtrls[i],
          focusNode: _otpFocuses[i],
          textAlign: TextAlign.center,
          keyboardType: TextInputType.number,
          maxLength: 1,
          inputFormatters: [FilteringTextInputFormatter.digitsOnly],
          style: const TextStyle(
              fontSize: 22, fontWeight: FontWeight.w800),
          decoration: InputDecoration(
            counterText: '',
            contentPadding: EdgeInsets.zero,
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppColors.primary, width: 2),
            ),
            filled: true,
            fillColor: _otpCtrls[i].text.isNotEmpty
                ? AppColors.primary.withOpacity(0.06)
                : Colors.white,
          ),
          onChanged: (val) {
            if (val.isNotEmpty && i < 5) {
              _otpFocuses[i + 1].requestFocus();
            }
            if (val.isEmpty && i > 0) {
              _otpFocuses[i - 1].requestFocus();
            }
            setState(() {}); // refresh fill color
            if (_otp.length == 6) {
              _verifyOtp();
            }
          },
        ),
      )),
    ),
    const SizedBox(height: 16),
    // Resend
    Row(mainAxisAlignment: MainAxisAlignment.center, children: [
      const Text("Didn't get the code? ",
          style: TextStyle(color: AppColors.textMedium, fontSize: 13)),
      GestureDetector(
        onTap: _loading ? null : () {
          for (final c in _otpCtrls) c.clear();
          setState(() { _step = 0; _error = null; });
        },
        child: const Text('Resend',
            style: TextStyle(color: AppColors.primary,
                fontWeight: FontWeight.w700, fontSize: 13)),
      ),
    ]),
    const SizedBox(height: 24),
    _SubmitButton(
      label: 'Verify OTP',
      icon: Icons.check_circle_outline_rounded,
      loading: _loading,
      onPressed: _verifyOtp,
    ),
  ]);

  // ── Step 2: New Password ──────────────────────────────────────────────────
  Widget _buildResetStep() => Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
    TextField(
      controller: _passCtrl,
      obscureText: !_showPass,
      decoration: InputDecoration(
        labelText: 'New Password',
        prefixIcon: const Icon(Icons.lock_outline_rounded, color: AppColors.primary),
        suffixIcon: IconButton(
          icon: Icon(_showPass ? Icons.visibility_off_rounded : Icons.visibility_rounded,
              color: AppColors.textLight, size: 20),
          onPressed: () => setState(() => _showPass = !_showPass),
        ),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: AppColors.primary, width: 2),
        ),
        helperText: 'Minimum 8 characters',
      ),
    ),
    const SizedBox(height: 16),
    TextField(
      controller: _confirmCtrl,
      obscureText: !_showConfirm,
      decoration: InputDecoration(
        labelText: 'Confirm Password',
        prefixIcon: const Icon(Icons.lock_outline_rounded, color: AppColors.primary),
        suffixIcon: IconButton(
          icon: Icon(_showConfirm ? Icons.visibility_off_rounded : Icons.visibility_rounded,
              color: AppColors.textLight, size: 20),
          onPressed: () => setState(() => _showConfirm = !_showConfirm),
        ),
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(14)),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: AppColors.primary, width: 2),
        ),
      ),
      onSubmitted: (_) => _resetPassword(),
    ),
    const SizedBox(height: 24),
    _SubmitButton(
      label: 'Reset Password',
      icon: Icons.check_circle_outline_rounded,
      loading: _loading,
      onPressed: _resetPassword,
    ),
  ]);

}

// ─────────────────────────────────────────────────────────────────────────────
// Shared submit button
// ─────────────────────────────────────────────────────────────────────────────
class _SubmitButton extends StatelessWidget {
  final String label;
  final IconData icon;
  final bool loading;
  final VoidCallback onPressed;

  const _SubmitButton({
    required this.label,
    required this.icon,
    required this.loading,
    required this.onPressed,
  });

  @override
  Widget build(BuildContext context) => SizedBox(
    width: double.infinity,
    height: 52,
    child: ElevatedButton.icon(
      onPressed: loading ? null : onPressed,
      icon: loading
          ? const SizedBox(width: 20, height: 20,
              child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
          : Icon(icon),
      label: Text(label,
          style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w700)),
      style: ElevatedButton.styleFrom(
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
        disabledBackgroundColor: AppColors.primary.withOpacity(0.5),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        elevation: 0,
      ),
    ),
  );
}
