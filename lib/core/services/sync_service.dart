import 'dart:async';
import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:hive_flutter/hive_flutter.dart';
import '../network/api_client.dart';
import '../network/offline_queue.dart';

class SyncService {
  static final SyncService _instance = SyncService._internal();
  factory SyncService() => _instance;
  SyncService._internal();

  Timer? _syncTimer;
  bool _isSyncing = false;

  bool get isSyncing => _isSyncing;

  void startPeriodicSync({Duration interval = const Duration(minutes: 5)}) {
    _syncTimer?.cancel();
    _syncTimer = Timer.periodic(interval, (_) => triggerSync());
  }

  void stopPeriodicSync() {
    _syncTimer?.cancel();
    _syncTimer = null;
  }

  Future<void> triggerSync() async {
    if (_isSyncing) return;
    _isSyncing = true;
    try {
      await drainOfflineQueue();
    } catch (e) {
      debugPrint('SyncService.triggerSync error: $e');
    } finally {
      _isSyncing = false;
    }
  }

  Future<int> drainOfflineQueue() async {
    if (!Hive.isBoxOpen('offline_queue')) return 0;
    final box = Hive.box<QueuedRequest>('offline_queue');
    if (box.isEmpty) return 0;

    final requests = box.values.toList()
      ..sort((a, b) => a.timestamp.compareTo(b.timestamp));

    int processedCount = 0;
    final apiClient = ApiClient();

    for (final req in requests) {
      try {
        // Use the typed ApiClient wrappers so error handling and auth
        // interceptors apply consistently (avoids bypassing via .dio.put/.dio.delete).
        switch (req.method.toUpperCase()) {
          case 'POST':
            await apiClient.post(req.path, data: req.data);
          case 'PUT':
            await apiClient.put(req.path, data: req.data);
          case 'DELETE':
            await apiClient.delete(req.path);
        }
        await req.delete();
        processedCount++;
      } on DioException catch (e) {
        // 4xx client errors: conflict or validation failure.
        // Discard via Last-Write-Wins — retrying won't help.
        final status = e.response?.statusCode;
        if (status != null && status >= 400 && status < 500) {
          debugPrint('SyncService: discarding stale mutation '
              '${req.method} ${req.path} (HTTP $status)');
          await req.delete();
        } else {
          // Network error or 5xx — stop and retry on next sync cycle.
          break;
        }
      } catch (e) {
        // Unexpected error — stop safely.
        debugPrint('SyncService.drainOfflineQueue unexpected error: $e');
        break;
      }
    }
    return processedCount;
  }
}
