import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_assets.dart';
import '../../../core/constants/app_colors.dart';
import '../../../core/constants/app_routes.dart';
import '../../../core/widgets/gradient_button.dart';
import 'package:nagarik_plus/core/l10n/l10n_extension.dart';
import '../providers/auth_provider.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  final _phoneController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  final _otpController = TextEditingController();
  bool _obscurePassword = true;
  bool _obscureConfirm = true;
  bool _isLoading = false;
  bool _acceptTerms = false;
  bool _isOtpStep = false;

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    _otpController.dispose();
    super.dispose();
  }

  void _register() async {
    if (!_formKey.currentState!.validate()) return;
    if (!_acceptTerms) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please accept the terms and conditions')),
      );
      return;
    }
    setState(() => _isLoading = true);

    final authProvider = context.read<AuthProvider>();
    await authProvider.register(
      name: _nameController.text.trim(),
      email: _emailController.text.trim(),
      phone: _phoneController.text.trim(),
      password: _passwordController.text,
    );

    if (!mounted) return;
    setState(() => _isLoading = false);

    if (authProvider.status == AuthStatus.initial) {
      setState(() => _isOtpStep = true);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('OTP sent to your email. Please verify to continue.'),
          backgroundColor: AppColors.secondary,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
              authProvider.errorMessage ?? 'Registration failed. Please try again.'),
          backgroundColor: AppColors.danger,
        ),
      );
    }
  }

  void _verifyOtp() async {
    if (_otpController.text.length < 6) return;
    setState(() => _isLoading = true);

    final authProvider = context.read<AuthProvider>();
    await authProvider.verifyRegistrationOtp(
      identifier: _emailController.text.trim(),
      otp: _otpController.text.trim(),
    );

    if (!mounted) return;
    setState(() => _isLoading = false);

    if (authProvider.status == AuthStatus.authenticated) {
      Navigator.of(context).pushReplacementNamed(AppRoutes.main);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
              authProvider.errorMessage ?? 'Invalid OTP. Please try again.'),
          backgroundColor: AppColors.danger,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: SingleChildScrollView(
          child: Column(
            children: [
              // Header
              Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(24, 40, 24, 40),
                decoration: const BoxDecoration(
                  gradient: AppColors.heroGradient,
                  borderRadius: BorderRadius.only(
                    bottomLeft: Radius.circular(32),
                    bottomRight: Radius.circular(32),
                  ),
                ),
                child: Column(
                  children: [
                    Container(
                      width: 72,
                      height: 72,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(18),
                        boxShadow: [
                          BoxShadow(
                              color: Colors.black.withValues(alpha: 0.15),
                              blurRadius: 12)
                        ],
                      ),
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(18),
                        child: Image.asset(AppAssets.appIcon, fit: BoxFit.cover),
                      ),
                    ),
                    const SizedBox(height: 16),
                    Text(
                      _isOtpStep ? 'Verify OTP' : context.l10n.createAccount,
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 26,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const SizedBox(height: 4),
                    const Text(
                      'Join Nagarik+ today',
                      style: TextStyle(color: Colors.white70, fontSize: 14),
                    ),
                  ],
                ),
              ),

              // Form
              Padding(
                padding: const EdgeInsets.all(24),
                child: Form(
                  key: _formKey,
                  child: Column(
                    children: [
                      const SizedBox(height: 8),
                      
                      if (!_isOtpStep) ...[
                        TextFormField(
                          controller: _nameController,
                          decoration: InputDecoration(
                            labelText: context.l10n.fullName,
                            hintText: context.l10n.enterYourFullName,
                            prefixIcon: const Icon(Icons.person_outline),
                          ),
                          validator: (v) => v == null || v.isEmpty
                              ? context.l10n.validationFieldRequired(context.l10n.fullName)
                              : null,
                        ),
                        const SizedBox(height: 14),
                        TextFormField(
                          controller: _emailController,
                          keyboardType: TextInputType.emailAddress,
                          decoration: InputDecoration(
                            labelText: context.l10n.emailAddress,
                            hintText: context.l10n.enterYourEmail,
                            prefixIcon: const Icon(Icons.email_outlined),
                          ),
                          validator: (v) {
                            if (v == null || v.isEmpty) {
                              return context.l10n.validationFieldRequired(context.l10n.emailAddress);
                            }
                            if (!v.contains('@')) return context.l10n.validationEmailInvalid;
                            return null;
                          },
                        ),
                        const SizedBox(height: 14),
                        TextFormField(
                          controller: _phoneController,
                          keyboardType: TextInputType.phone,
                          decoration: InputDecoration(
                            labelText: context.l10n.phoneNumber,
                            hintText: context.l10n.enterYourPhone,
                            prefixIcon: const Icon(Icons.phone_outlined),
                            prefixText: '+977 ',
                          ),
                          validator: (v) => v == null || v.isEmpty
                              ? context.l10n.validationPhoneInvalid
                              : null,
                        ),
                        const SizedBox(height: 14),
                        TextFormField(
                          controller: _passwordController,
                          obscureText: _obscurePassword,
                          decoration: InputDecoration(
                            labelText: context.l10n.password,
                            hintText: context.l10n.enterYourPassword,
                            prefixIcon: const Icon(Icons.lock_outline),
                            suffixIcon: IconButton(
                              icon: Icon(
                                _obscurePassword
                                    ? Icons.visibility_off_outlined
                                    : Icons.visibility_outlined,
                              ),
                              onPressed: () => setState(
                                  () => _obscurePassword = !_obscurePassword),
                            ),
                          ),
                          validator: (v) {
                            if (v == null || v.isEmpty) {
                              return context.l10n.validationFieldRequired(context.l10n.password);
                            }
                            if (v.length < 8) return context.l10n.validationPasswordTooShort;
                            return null;
                          },
                        ),
                        const SizedBox(height: 14),
                        TextFormField(
                          controller: _confirmPasswordController,
                          obscureText: _obscureConfirm,
                          decoration: InputDecoration(
                            labelText: context.l10n.confirmPassword,
                            prefixIcon: const Icon(Icons.lock_outline),
                            suffixIcon: IconButton(
                              icon: Icon(
                                _obscureConfirm
                                    ? Icons.visibility_off_outlined
                                    : Icons.visibility_outlined,
                              ),
                              onPressed: () => setState(
                                  () => _obscureConfirm = !_obscureConfirm),
                            ),
                          ),
                          validator: (v) {
                            if (v == null || v.isEmpty) {
                              return context.l10n.validationFieldRequired(context.l10n.confirmPassword);
                            }
                            if (v != _passwordController.text) {
                              return context.l10n.validationPasswordMismatch;
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),
  
                        // Terms
                        Row(
                          children: [
                            Checkbox(
                              value: _acceptTerms,
                              activeColor: AppColors.primary,
                              onChanged: (v) =>
                                  setState(() => _acceptTerms = v ?? false),
                            ),
                            Expanded(
                              child: GestureDetector(
                                onTap: () => setState(
                                    () => _acceptTerms = !_acceptTerms),
                                child: const Text(
                                  'I agree to the Terms of Service and Privacy Policy',
                                  style: TextStyle(fontSize: 13),
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
  
                        GradientButton(
                          label: context.l10n.createAccount,
                          onPressed: _register,
                          isLoading: _isLoading,
                          icon: Icons.person_add_rounded,
                        ),
                      ] else ...[
                        TextFormField(
                          controller: _otpController,
                          keyboardType: TextInputType.number,
                          maxLength: 6,
                          textAlign: TextAlign.center,
                          style: const TextStyle(fontSize: 24, letterSpacing: 8, fontWeight: FontWeight.bold),
                          decoration: const InputDecoration(
                            labelText: '6-digit OTP',
                            alignLabelWithHint: true,
                          ),
                        ),
                        const SizedBox(height: 16),
                        GradientButton(
                          label: 'Verify & Complete Registration',
                          onPressed: _verifyOtp,
                          isLoading: _isLoading,
                          icon: Icons.check_circle_outline,
                        ),
                        const SizedBox(height: 16),
                        TextButton(
                          onPressed: () => setState(() => _isOtpStep = false),
                          child: const Text('Back to Registration Form', style: TextStyle(color: AppColors.textMedium)),
                        ),
                      ],
                      
                      const SizedBox(height: 20),

                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(context.l10n.alreadyHaveAccount),
                          const SizedBox(width: 4),
                          GestureDetector(
                            onTap: () => Navigator.of(context).pop(),
                            child: Text(
                              context.l10n.signIn,
                              style: const TextStyle(
                                color: AppColors.primary,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
