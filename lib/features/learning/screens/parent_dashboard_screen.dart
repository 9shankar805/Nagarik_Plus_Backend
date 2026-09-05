import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';

class ParentDashboardScreen extends StatefulWidget {
  const ParentDashboardScreen({super.key});

  @override
  State<ParentDashboardScreen> createState() => _ParentDashboardScreenState();
}

class _ParentDashboardScreenState extends State<ParentDashboardScreen> {
  bool _isLinked = false;
  final TextEditingController _codeController = TextEditingController();

  void _linkAccount() {
    if (_codeController.text.length == 6) {
      setState(() => _isLinked = true);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Please enter a valid 6-digit code.')),
      );
    }
  }

  @override
  void dispose() {
    _codeController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Parental Dashboard'),
        backgroundColor: AppColors.primary,
        foregroundColor: Colors.white,
      ),
      body: _isLinked ? _buildDashboard() : _buildLinkScreen(),
    );
  }

  Widget _buildLinkScreen() {
    return Padding(
      padding: const EdgeInsets.all(24),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.family_restroom_rounded, size: 80, color: AppColors.primary),
          const SizedBox(height: 24),
          const Text(
            'Link Student Account',
            style: TextStyle(fontSize: 22, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 8),
          const Text(
            'Enter the 6-digit pairing code from your child\'s app to monitor their progress.',
            textAlign: TextAlign.center,
            style: TextStyle(color: AppColors.textMedium, height: 1.5),
          ),
          const SizedBox(height: 32),
          TextField(
            controller: _codeController,
            textAlign: TextAlign.center,
            keyboardType: TextInputType.number,
            maxLength: 6,
            style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, letterSpacing: 8),
            decoration: InputDecoration(
              hintText: '000000',
              filled: true,
              fillColor: Colors.white,
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(16),
                borderSide: BorderSide.none,
              ),
            ),
          ),
          const SizedBox(height: 24),
          ElevatedButton(
            onPressed: _linkAccount,
            style: ElevatedButton.styleFrom(
              minimumSize: const Size(double.infinity, 56),
              backgroundColor: AppColors.primary,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            ),
            child: const Text('Link Account', style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  Widget _buildDashboard() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Student Profile
          Row(
            children: [
              const CircleAvatar(
                radius: 30,
                backgroundColor: AppColors.info,
                child: Text('AS', style: TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold)),
              ),
              const SizedBox(width: 16),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Aarav Sharma', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
                  Text('Linked Student', style: TextStyle(color: AppColors.primary.withValues(alpha: 0.8), fontWeight: FontWeight.w600)),
                ],
              ),
              const Spacer(),
              IconButton(
                icon: const Icon(Icons.link_off_rounded, color: AppColors.danger),
                onPressed: () => setState(() => _isLinked = false),
                tooltip: 'Unlink',
              ),
            ],
          ),
          const SizedBox(height: 32),

          // Overview Cards
          Row(
            children: [
              _buildOverviewCard('Avg Score', '78%', Icons.analytics_outlined, AppColors.success),
              const SizedBox(width: 12),
              _buildOverviewCard('Tests Taken', '14', Icons.assignment_outlined, AppColors.primary),
            ],
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              _buildOverviewCard('Video Watch Time', '12h 45m', Icons.ondemand_video_rounded, Colors.purple),
              const SizedBox(width: 12),
              _buildOverviewCard('Study Streak', '4 Days', Icons.local_fire_department_outlined, Colors.orange),
            ],
          ),
          
          const SizedBox(height: 32),
          const Text('Recent Mock Tests', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 16),
          
          // Test History List
          _buildTestHistoryItem('Traffic Rules Quiz', 'Yesterday', '42/50', true),
          const SizedBox(height: 12),
          _buildTestHistoryItem('Mechanics (Hard)', '3 days ago', '28/50', false),
          const SizedBox(height: 12),
          _buildTestHistoryItem('Road Signs Test', 'Last week', '48/50', true),
        ],
      ),
    );
  }

  Widget _buildOverviewCard(String title, String value, IconData icon, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: const [AppColors.thinShadow],
          border: Border(left: BorderSide(color: color, width: 4)),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: color, size: 24),
            const SizedBox(height: 12),
            Text(value, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900)),
            const SizedBox(height: 4),
            Text(title, style: const TextStyle(color: AppColors.textMedium, fontSize: 12)),
          ],
        ),
      ),
    );
  }

  Widget _buildTestHistoryItem(String title, String date, String score, bool passed) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: const [AppColors.thinShadow],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: passed ? AppColors.success.withValues(alpha: 0.1) : AppColors.danger.withValues(alpha: 0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(
              passed ? Icons.check_circle_rounded : Icons.cancel_rounded,
              color: passed ? AppColors.success : AppColors.danger,
              size: 20,
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                const SizedBox(height: 4),
                Text(date, style: const TextStyle(color: AppColors.textLight, fontSize: 12)),
              ],
            ),
          ),
          Text(score, style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 16)),
        ],
      ),
    );
  }
}
