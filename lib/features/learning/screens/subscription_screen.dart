import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';

class SubscriptionScreen extends StatelessWidget {
  const SubscriptionScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Premium Access'),
        backgroundColor: Colors.transparent,
        elevation: 0,
        foregroundColor: AppColors.textDark,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            const Icon(Icons.workspace_premium_rounded, size: 80, color: Colors.amber),
            const SizedBox(height: 16),
            const Text(
              'Unlock Your Full Potential',
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900),
            ),
            const SizedBox(height: 8),
            const Text(
              'Get unlimited access to video lectures, adaptive mock tests, and 24/7 expert doubt clearing.',
              textAlign: TextAlign.center,
              style: TextStyle(color: AppColors.textMedium, fontSize: 14, height: 1.5),
            ),
            const SizedBox(height: 32),
            
            // Feature List
            _buildFeatureRow(Icons.check_circle_rounded, 'Unlimited Adaptive Mock Tests'),
            const SizedBox(height: 12),
            _buildFeatureRow(Icons.check_circle_rounded, 'Full Access to Recorded & Live Video Classes'),
            const SizedBox(height: 12),
            _buildFeatureRow(Icons.check_circle_rounded, 'Priority Expert Answers within 2 hours'),
            const SizedBox(height: 12),
            _buildFeatureRow(Icons.check_circle_rounded, 'Advanced AI Analytics & Percentile Tracking'),
            
            const SizedBox(height: 40),
            
            // Pricing Cards
            Row(
              children: [
                Expanded(
                  child: _PricingCard(
                    title: '1 Month',
                    price: 'Rs. 499',
                    subtitle: 'Billed monthly',
                    isPopular: false,
                  ),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: _PricingCard(
                    title: '6 Months',
                    price: 'Rs. 1,999',
                    subtitle: 'Save 33%',
                    isPopular: true,
                  ),
                ),
              ],
            ),
            
            const SizedBox(height: 40),
            ElevatedButton(
              onPressed: () {
                // Handle payment integration (e.g. eSewa, Khalti)
              },
              style: ElevatedButton.styleFrom(
                minimumSize: const Size(double.infinity, 56),
                backgroundColor: AppColors.primary,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                elevation: 4,
                shadowColor: AppColors.primary.withValues(alpha: 0.4),
              ),
              child: const Text('Continue to Payment', style: TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
            ),
            const SizedBox(height: 16),
            const Text(
              'Secure payments processed via eSewa & Khalti',
              style: TextStyle(color: Colors.grey, fontSize: 12),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildFeatureRow(IconData icon, String text) {
    return Row(
      children: [
        Icon(icon, color: AppColors.success, size: 22),
        const SizedBox(width: 12),
        Expanded(child: Text(text, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w500))),
      ],
    );
  }
}

class _PricingCard extends StatelessWidget {
  final String title;
  final String price;
  final String subtitle;
  final bool isPopular;

  const _PricingCard({
    required this.title,
    required this.price,
    required this.subtitle,
    required this.isPopular,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
      decoration: BoxDecoration(
        color: isPopular ? AppColors.primary : Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: isPopular ? AppColors.primary : AppColors.divider, width: 2),
        boxShadow: isPopular ? [AppColors.cardShadow] : [],
      ),
      child: Column(
        children: [
          if (isPopular)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              margin: const EdgeInsets.only(bottom: 12),
              decoration: BoxDecoration(
                color: Colors.amber,
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Text('POPULAR', style: TextStyle(color: Colors.black87, fontSize: 10, fontWeight: FontWeight.w900)),
            ),
          Text(
            title,
            style: TextStyle(
              color: isPopular ? Colors.white70 : AppColors.textMedium,
              fontSize: 14,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            price,
            style: TextStyle(
              color: isPopular ? Colors.white : AppColors.textDark,
              fontSize: 22,
              fontWeight: FontWeight.w900,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            subtitle,
            style: TextStyle(
              color: isPopular ? Colors.white70 : AppColors.textLight,
              fontSize: 12,
            ),
          ),
        ],
      ),
    );
  }
}
