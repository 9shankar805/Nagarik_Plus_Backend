import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';

class VideoLecturesScreen extends StatefulWidget {
  const VideoLecturesScreen({super.key});

  @override
  State<VideoLecturesScreen> createState() => _VideoLecturesScreenState();
}

class _VideoLecturesScreenState extends State<VideoLecturesScreen> {
  final List<Map<String, dynamic>> _videos = [
    {
      'title': 'Traffic Rules & Regulations - Complete Guide',
      'duration': '45:20',
      'isLive': false,
      'thumbnail': 'traffic_rules_thumb',
      'subject': 'General Rules',
      'instructor': 'Er. Rajesh Kumar',
    },
    {
      'title': 'Understanding Road Signs (Part 1)',
      'duration': '32:15',
      'isLive': false,
      'thumbnail': 'road_signs_thumb',
      'subject': 'Traffic Signs',
      'instructor': 'Er. Rajesh Kumar',
    },
    {
      'title': 'Live Q&A Session - Mechanics & Maintenance',
      'duration': 'Starts at 7:00 PM',
      'isLive': true,
      'thumbnail': 'live_thumb',
      'subject': 'Mechanics',
      'instructor': 'Bikash Thapa',
    },
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Video Lectures'),
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
      ),
      body: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _videos.length,
        separatorBuilder: (context, index) => const SizedBox(height: 16),
        itemBuilder: (context, index) {
          final video = _videos[index];
          final isLive = video['isLive'] as bool;
          
          return GestureDetector(
            onTap: () {
              // Navigate to Video Player screen
            },
            child: Container(
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                boxShadow: const [AppColors.cardShadow],
              ),
              clipBehavior: Clip.antiAlias,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Mock Thumbnail
                  Stack(
                    alignment: Alignment.center,
                    children: [
                      Container(
                        height: 180,
                        width: double.infinity,
                        color: Colors.black87,
                        child: const Icon(
                          Icons.image_outlined,
                          size: 50,
                          color: Colors.white24,
                        ),
                      ),
                      
                      // Play Button
                      Container(
                        width: 56,
                        height: 56,
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.2),
                          shape: BoxShape.circle,
                        ),
                        child: const Icon(Icons.play_arrow_rounded, color: Colors.white, size: 36),
                      ),

                      // Live Badge or Duration Label
                      Positioned(
                        bottom: 12,
                        right: 12,
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: isLive ? AppColors.danger : Colors.black.withValues(alpha: 0.7),
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              if (isLive) ...[
                                Container(
                                  width: 6,
                                  height: 6,
                                  decoration: const BoxDecoration(color: Colors.white, shape: BoxShape.circle),
                                ),
                                const SizedBox(width: 4),
                                const Text('LIVE', style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold)),
                              ] else 
                                Text(video['duration'], style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold)),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                  
                  // Video Details
                  Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                              decoration: BoxDecoration(
                                color: AppColors.primary.withValues(alpha: 0.1),
                                borderRadius: BorderRadius.circular(4),
                              ),
                              child: Text(
                                video['subject'],
                                style: const TextStyle(color: AppColors.primary, fontSize: 11, fontWeight: FontWeight.w700),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Text(
                          video['title'],
                          style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, height: 1.3),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            const CircleAvatar(
                              radius: 12,
                              backgroundColor: AppColors.divider,
                              child: Icon(Icons.person, size: 16, color: Colors.grey),
                            ),
                            const SizedBox(width: 8),
                            Text(
                              video['instructor'],
                              style: const TextStyle(color: AppColors.textMedium, fontSize: 13),
                            ),
                          ],
                        ),
                      ],
                    ),
                  )
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
