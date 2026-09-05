import 'package:flutter/services.dart';
import 'package:local_auth/local_auth.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';

/// Handles biometric authentication (fingerprint / Face ID) with PIN fallback.
/// Keys are stored in Android Keystore / iOS Keychain via flutter_secure_storage.
class BiometricService {
  BiometricService._();
  static final BiometricService instance = BiometricService._();

  final _auth    = LocalAuthentication();
  static const _pinKey  = 'nagarik_vault_pin';
  static const _storage = FlutterSecureStorage(
    aOptions: AndroidOptions(encryptedSharedPreferences: true),
    iOptions: IOSOptions(accessibility: KeychainAccessibility.first_unlock),
  );

  static const _bioEnabledKey = 'login_biometric_enabled';
  static const _bioPromptedKey = 'login_biometric_prompted';

  Future<bool> isLoginBiometricEnabled() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getBool(_bioEnabledKey) ?? false;
  }

  Future<void> setLoginBiometricEnabled(bool enabled) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_bioEnabledKey, enabled);
  }

  Future<bool> hasPromptedForBiometric() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getBool(_bioPromptedKey) ?? false;
  }

  Future<void> setPromptedForBiometric() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_bioPromptedKey, true);
  }

  // ── Capability ────────────────────────────────────────────────────────────

  /// Returns true if the device supports biometrics AND has enrolled biometrics.
  Future<bool> isBiometricAvailable() async {
    try {
      final canCheck = await _auth.canCheckBiometrics;
      final isSupported = await _auth.isDeviceSupported();
      if (!canCheck || !isSupported) return false;
      final biometrics = await _auth.getAvailableBiometrics();
      return biometrics.isNotEmpty;
    } on PlatformException {
      return false;
    }
  }

  // ── Authenticate ──────────────────────────────────────────────────────────

  /// Attempt biometric auth first; falls back to device PIN/password.
  /// Returns true on success.
  Future<bool> authenticate({String reason = 'Authenticate to access your vault'}) async {
    try {
      return await _auth.authenticate(
        localizedReason: reason,
        options: const AuthenticationOptions(
          biometricOnly: false,  // allows PIN fallback
          stickyAuth: true,
          useErrorDialogs: true,
        ),
      );
    } on PlatformException {
      return false;
    }
  }

  // ── App-level PIN (4–6 digits, stored in Keychain/Keystore) ───────────────

  Future<bool> hasPin() async {
    final pin = await _storage.read(key: _pinKey);
    return pin != null && pin.isNotEmpty;
  }

  Future<void> setPin(String pin) async {
    await _storage.write(key: _pinKey, value: pin);
  }

  Future<bool> verifyPin(String input) async {
    final stored = await _storage.read(key: _pinKey);
    return stored == input;
  }

  Future<void> clearPin() async {
    await _storage.delete(key: _pinKey);
  }
}
