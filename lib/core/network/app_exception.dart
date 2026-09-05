
class AppException implements Exception {
  final String message;
  final int? statusCode;

  AppException(this.message, {this.statusCode});

  @override
  String toString() => message;
}

class NetworkException extends AppException {
  NetworkException(super.message) : super();
}

class AuthException extends AppException {
  AuthException(super.message, {super.statusCode});
}

class ValidationException extends AppException {
  final Map<String, dynamic>? errors;

  ValidationException(super.message, {super.statusCode, this.errors});

  @override
  String toString() {
    if (errors != null && errors!.isNotEmpty) {
      final firstKey = errors!.keys.first;
      final firstError = errors![firstKey];
      if (firstError is List && firstError.isNotEmpty) {
        return firstError.first.toString();
      } else if (firstError is String) {
        return firstError;
      }
    }
    return message;
  }
}

class ServerException extends AppException {
  ServerException(super.message, {super.statusCode});
}

class TimeoutException extends AppException {
  TimeoutException(super.message) : super();
}

class RateLimitException extends AppException {
  final int? retryAfter;

  RateLimitException(super.message, {super.statusCode, this.retryAfter});
}

