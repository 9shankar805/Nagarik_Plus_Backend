import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';

class AskDoubtScreen extends StatefulWidget {
  const AskDoubtScreen({super.key});

  @override
  State<AskDoubtScreen> createState() => _AskDoubtScreenState();
}

class _AskDoubtScreenState extends State<AskDoubtScreen> {
  final TextEditingController _doubtController = TextEditingController();

  final List<Map<String, dynamic>> _discussions = [
    {
      'student': 'Aarav Sharma',
      'question': 'I don\'t understand the difference between the "Give Way" and "Stop" signs in terms of penalty points.',
      'time': '2 hours ago',
      'isAnswered': true,
      'expert': 'Er. Rajesh Kumar',
      'answer': 'Great question. Failing to stop at a "Stop" sign carries a heavier penalty because it indicates a mandatory complete stop, whereas "Give Way" requires you to yield but a full stop is not always legally required if the road is clear.',
    },
    {
      'student': 'Kritika Shrestha',
      'question': 'Can we overtake on a zebra crossing if there are no pedestrians?',
      'time': '5 hours ago',
      'isAnswered': false,
    },
  ];

  @override
  void dispose() {
    _doubtController.dispose();
    super.dispose();
  }

  void _submitDoubt() {
    if (_doubtController.text.trim().isEmpty) return;
    
    setState(() {
      _discussions.insert(0, {
        'student': 'You',
        'question': _doubtController.text.trim(),
        'time': 'Just now',
        'isAnswered': false,
      });
      _doubtController.clear();
    });
    
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Your doubt has been submitted. An expert will answer soon!')),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Ask & Discuss'),
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
      ),
      body: Column(
        children: [
          // Ask Box
          Container(
            padding: const EdgeInsets.all(16),
            color: Colors.white,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text('Have a doubt?', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                const SizedBox(height: 12),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const CircleAvatar(
                      radius: 20,
                      backgroundColor: AppColors.primary,
                      child: Icon(Icons.person, color: Colors.white, size: 20),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: TextField(
                        controller: _doubtController,
                        maxLines: 3,
                        decoration: InputDecoration(
                          hintText: 'Type your question here...',
                          hintStyle: const TextStyle(color: Colors.black38),
                          filled: true,
                          fillColor: AppColors.background,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                            borderSide: BorderSide.none,
                          ),
                          contentPadding: const EdgeInsets.all(12),
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    TextButton.icon(
                      onPressed: () {},
                      icon: const Icon(Icons.image_outlined),
                      label: const Text('Attach Image'),
                      style: TextButton.styleFrom(foregroundColor: AppColors.textMedium),
                    ),
                    ElevatedButton(
                      onPressed: _submitDoubt,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.primary,
                        foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                        padding: const EdgeInsets.symmetric(horizontal: 24),
                      ),
                      child: const Text('Post Doubt'),
                    ),
                  ],
                ),
              ],
            ),
          ),
          
          const Divider(height: 1, thickness: 1),
          
          // Discussions List
          Expanded(
            child: ListView.separated(
              padding: const EdgeInsets.all(16),
              itemCount: _discussions.length,
              separatorBuilder: (context, index) => const SizedBox(height: 16),
              itemBuilder: (context, index) {
                final disc = _discussions[index];
                final isAnswered = disc['isAnswered'] as bool;
                
                return Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    boxShadow: const [AppColors.thinShadow],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Question Header
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Row(
                            children: [
                              const CircleAvatar(radius: 12, backgroundColor: Colors.blueGrey, child: Icon(Icons.person, size: 14, color: Colors.white)),
                              const SizedBox(width: 8),
                              Text(disc['student'], style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                            ],
                          ),
                          Text(disc['time'], style: const TextStyle(color: Colors.grey, fontSize: 11)),
                        ],
                      ),
                      const SizedBox(height: 12),
                      
                      // Question Body
                      Text(
                        disc['question'],
                        style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500, height: 1.4),
                      ),
                      const SizedBox(height: 16),
                      
                      // Answer Section
                      if (isAnswered)
                        Container(
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: AppColors.success.withValues(alpha: 0.05),
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: AppColors.success.withValues(alpha: 0.2)),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Row(
                                children: [
                                  const Icon(Icons.verified, color: AppColors.success, size: 16),
                                  const SizedBox(width: 6),
                                  Text('${disc['expert']} (Expert)', style: const TextStyle(fontWeight: FontWeight.w700, color: AppColors.success, fontSize: 12)),
                                ],
                              ),
                              const SizedBox(height: 6),
                              Text(
                                disc['answer'],
                                style: const TextStyle(color: AppColors.textDark, fontSize: 13, height: 1.4),
                              ),
                            ],
                          ),
                        )
                      else
                        Row(
                          children: [
                            const Icon(Icons.pending_actions, size: 16, color: Colors.orange),
                            const SizedBox(width: 6),
                            const Text('Waiting for expert answer...', style: TextStyle(color: Colors.orange, fontSize: 12, fontWeight: FontWeight.w600)),
                          ],
                        ),
                    ],
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}
