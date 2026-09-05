import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_colors.dart';
import '../providers/learning_provider.dart';
import '../models/learning_models.dart';

// ─────────────────────────────────────────────────────────────────────────────
// DiscussScreen — Community doubts · My doubts · Ask a question
// ─────────────────────────────────────────────────────────────────────────────
class DiscussScreen extends StatefulWidget {
  const DiscussScreen({super.key});

  @override
  State<DiscussScreen> createState() => _DiscussScreenState();
}

class _DiscussScreenState extends State<DiscussScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tab;

  @override
  void initState() {
    super.initState();
    _tab = TabController(length: 2, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) => _load());
  }

  @override
  void dispose() {
    _tab.dispose();
    super.dispose();
  }

  void _load() {
    final p = context.read<LearningProvider>();
    p.loadDoubts();
    p.loadMyDoubts();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FA),
      body: Column(children: [
        // Header
        Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [Color(0xFF302B63), Color(0xFF24243E)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.only(
                bottomLeft: Radius.circular(28),
                bottomRight: Radius.circular(28)),
          ),
          child: SafeArea(
            bottom: false,
            child: Column(children: [
              Padding(
                padding: const EdgeInsets.fromLTRB(20, 16, 20, 0),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      Text('Discussion',
                          style: TextStyle(
                              color: Colors.white,
                              fontSize: 20,
                              fontWeight: FontWeight.bold)),
                      Text('Ask, answer & learn together',
                          style:
                              TextStyle(color: Colors.white60, fontSize: 12)),
                    ]),
                    // Ask FAB in header
                    GestureDetector(
                      onTap: () => _showAskSheet(context),
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 14, vertical: 8),
                        decoration: BoxDecoration(
                          color: Colors.amber,
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: const Row(children: [
                          Icon(Icons.add_rounded,
                              color: Color(0xFF302B63), size: 18),
                          SizedBox(width: 4),
                          Text('Ask',
                              style: TextStyle(
                                  color: Color(0xFF302B63),
                                  fontWeight: FontWeight.bold,
                                  fontSize: 13)),
                        ]),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 12),
              TabBar(
                controller: _tab,
                indicatorColor: Colors.amber,
                indicatorWeight: 3,
                labelColor: Colors.amber,
                unselectedLabelColor: Colors.white60,
                labelStyle: const TextStyle(
                    fontWeight: FontWeight.bold, fontSize: 14),
                tabs: const [
                  Tab(text: 'Community'),
                  Tab(text: 'My Questions'),
                ],
              ),
            ]),
          ),
        ),

        Expanded(
          child: TabBarView(
            controller: _tab,
            children: const [
              _CommunityTab(),
              _MyDoubtsTab(),
            ],
          ),
        ),
      ]),
    );
  }

  void _showAskSheet(BuildContext context) {
    final subjectCtrl = TextEditingController();
    final bodyCtrl = TextEditingController();

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (_) => Padding(
        padding: EdgeInsets.fromLTRB(
            20, 20, 20, MediaQuery.of(context).viewInsets.bottom + 20),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Container(
              width: 40,
              height: 4,
              margin: const EdgeInsets.only(bottom: 16),
              decoration: BoxDecoration(
                  color: AppColors.divider,
                  borderRadius: BorderRadius.circular(2))),
          const Text('Ask a Question',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
          const SizedBox(height: 20),
          TextField(
            controller: subjectCtrl,
            decoration: InputDecoration(
              labelText: 'Subject / Topic',
              prefixIcon: const Icon(Icons.topic_rounded),
              border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12)),
            ),
          ),
          const SizedBox(height: 12),
          TextField(
            controller: bodyCtrl,
            maxLines: 4,
            decoration: InputDecoration(
              labelText: 'Describe your question in detail',
              alignLabelWithHint: true,
              border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12)),
            ),
          ),
          const SizedBox(height: 16),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: () async {
                if (subjectCtrl.text.trim().isEmpty ||
                    bodyCtrl.text.trim().isEmpty) return;
                final p = context.read<LearningProvider>();
                Navigator.pop(context);
                await p.createDoubt({
                  'subject': subjectCtrl.text.trim(),
                  'question_text': bodyCtrl.text.trim(),
                });
                if (context.mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
                      content: Text('Question posted!')));
                }
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF302B63),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12)),
              ),
              child: const Text('Submit Question',
                  style: TextStyle(fontWeight: FontWeight.bold)),
            ),
          ),
        ]),
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Community Tab — public answered doubts
// ─────────────────────────────────────────────────────────────────────────────
class _CommunityTab extends StatelessWidget {
  const _CommunityTab();

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (_, p, __) {
      if (p.isLoadingDoubts) {
        return const Center(child: CircularProgressIndicator());
      }
      return RefreshIndicator(
        onRefresh: () => p.loadDoubts(),
        child: p.doubts.isEmpty
            ? const Center(
                child: Padding(
                  padding: EdgeInsets.all(40),
                  child: Column(mainAxisSize: MainAxisSize.min, children: [
                    Icon(Icons.forum_outlined,
                        size: 56, color: AppColors.textLight),
                    SizedBox(height: 12),
                    Text('No community questions yet',
                        style: TextStyle(color: AppColors.textMedium),
                        textAlign: TextAlign.center),
                  ]),
                ),
              )
            : ListView.builder(
                physics: const BouncingScrollPhysics(),
                padding: const EdgeInsets.fromLTRB(16, 12, 16, 100),
                itemCount: p.doubts.length,
                itemBuilder: (_, i) =>
                    _DoubtCard(doubt: p.doubts[i]),
              ),
      );
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// My Doubts Tab
// ─────────────────────────────────────────────────────────────────────────────
class _MyDoubtsTab extends StatelessWidget {
  const _MyDoubtsTab();

  @override
  Widget build(BuildContext context) {
    return Consumer<LearningProvider>(builder: (_, p, __) {
      return RefreshIndicator(
        onRefresh: () => p.loadMyDoubts(),
        child: p.myDoubts.isEmpty
            ? Center(
                child: Padding(
                  padding: const EdgeInsets.all(40),
                  child: Column(mainAxisSize: MainAxisSize.min, children: [
                    const Icon(Icons.live_help_outlined,
                        size: 56, color: AppColors.textLight),
                    const SizedBox(height: 12),
                    const Text("You haven't asked anything yet",
                        style: TextStyle(color: AppColors.textMedium),
                        textAlign: TextAlign.center),
                    const SizedBox(height: 16),
                    ElevatedButton.icon(
                      onPressed: () {},
                      icon: const Icon(Icons.add_rounded),
                      label: const Text('Ask a Question'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF302B63),
                        foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12)),
                      ),
                    ),
                  ]),
                ),
              )
            : ListView.builder(
                physics: const BouncingScrollPhysics(),
                padding: const EdgeInsets.fromLTRB(16, 12, 16, 100),
                itemCount: p.myDoubts.length,
                itemBuilder: (_, i) => _DoubtCard(
                    doubt: p.myDoubts[i], showDelete: true),
              ),
      );
    });
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Doubt Card
// ─────────────────────────────────────────────────────────────────────────────
class _DoubtCard extends StatelessWidget {
  final Doubt doubt;
  final bool showDelete;
  const _DoubtCard({required this.doubt, this.showDelete = false});

  Color _statusColor(String? status) {
    switch (status) {
      case 'answered':
        return AppColors.success;
      case 'closed':
        return AppColors.textLight;
      default:
        return Colors.orange;
    }
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => _openDetail(context),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: const [AppColors.thinShadow],
        ),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            // Status dot
            Container(
              padding:
                  const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
              decoration: BoxDecoration(
                color: _statusColor(doubt.status).withOpacity(0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Text(
                doubt.status?.toUpperCase() ?? 'OPEN',
                style: TextStyle(
                    color: _statusColor(doubt.status),
                    fontSize: 9,
                    fontWeight: FontWeight.bold),
              ),
            ),
            const Spacer(),
            // Upvotes
            Row(children: [
              const Icon(Icons.thumb_up_alt_outlined,
                  size: 14, color: AppColors.textLight),
              const SizedBox(width: 3),
              Text('${doubt.upvotes}',
                  style: const TextStyle(
                      color: AppColors.textLight, fontSize: 12)),
            ]),
            if (showDelete) ...[
              const SizedBox(width: 8),
              GestureDetector(
                onTap: () async {
                  final p = context.read<LearningProvider>();
                  await p.deleteDoubt(doubt.id);
                },
                child: const Icon(Icons.delete_outline_rounded,
                    size: 18, color: AppColors.textLight),
              ),
            ],
          ]),
          const SizedBox(height: 10),
          Text(
            doubt.title,
            style: const TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 14,
                color: AppColors.textDark),
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
          const SizedBox(height: 6),
          Text(
            doubt.body,
            style: const TextStyle(
                color: AppColors.textMedium, fontSize: 12, height: 1.4),
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
          const SizedBox(height: 10),
          Row(children: [
            const Icon(Icons.chat_bubble_outline_rounded,
                size: 13, color: AppColors.textLight),
            const SizedBox(width: 4),
            Text('${doubt.answersCount} answers',
                style: const TextStyle(
                    color: AppColors.textLight, fontSize: 11)),
            const Spacer(),
            if (doubt.createdAt != null)
              Text(
                doubt.createdAt!.substring(0, 10),
                style: const TextStyle(
                    color: AppColors.textLight, fontSize: 11),
              ),
          ]),
        ]),
      ),
    );
  }

  void _openDetail(BuildContext context) {
    Navigator.push(
        context,
        MaterialPageRoute(
            builder: (_) => _DoubtDetailScreen(doubt: doubt)));
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Doubt Detail Screen — shows full question + answers + answer form
// ─────────────────────────────────────────────────────────────────────────────
class _DoubtDetailScreen extends StatefulWidget {
  final Doubt doubt;
  const _DoubtDetailScreen({required this.doubt});

  @override
  State<_DoubtDetailScreen> createState() => _DoubtDetailScreenState();
}

class _DoubtDetailScreenState extends State<_DoubtDetailScreen> {
  final _answerCtrl = TextEditingController();
  bool _submitting = false;

  @override
  void dispose() {
    _answerCtrl.dispose();
    super.dispose();
  }

  Future<void> _postAnswer() async {
    if (_answerCtrl.text.trim().isEmpty) return;
    setState(() => _submitting = true);
    final p = context.read<LearningProvider>();
    await p.answerDoubt(
        widget.doubt.id, {'body': _answerCtrl.text.trim()});
    _answerCtrl.clear();
    setState(() => _submitting = false);
    if (mounted) {
      ScaffoldMessenger.of(context)
          .showSnackBar(const SnackBar(content: Text('Answer posted!')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FA),
      appBar: AppBar(
        title: const Text('Discussion'),
        backgroundColor: const Color(0xFF302B63),
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.thumb_up_alt_outlined),
            onPressed: () =>
                context.read<LearningProvider>().upvoteDoubt(widget.doubt.id),
          ),
        ],
      ),
      body: Column(children: [
        Expanded(
          child: SingleChildScrollView(
            physics: const BouncingScrollPhysics(),
            padding: const EdgeInsets.all(16),
            child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Question
                  Container(
                    padding: const EdgeInsets.all(18),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: const [AppColors.thinShadow],
                    ),
                    child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(children: [
                            const Icon(Icons.help_outline_rounded,
                                color: Color(0xFF302B63), size: 20),
                            const SizedBox(width: 8),
                            const Text('Question',
                                style: TextStyle(
                                    fontWeight: FontWeight.bold,
                                    color: Color(0xFF302B63))),
                          ]),
                          const SizedBox(height: 12),
                          Text(widget.doubt.title,
                              style: const TextStyle(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 16)),
                          const SizedBox(height: 8),
                          Text(widget.doubt.body,
                              style: const TextStyle(
                                  color: AppColors.textMedium,
                                  fontSize: 13,
                                  height: 1.5)),
                        ]),
                  ),
                  const SizedBox(height: 20),

                  const Text('Answers',
                      style: TextStyle(
                          fontSize: 16, fontWeight: FontWeight.w800)),
                  const SizedBox(height: 10),

                  // Placeholder for answers (would be loaded from provider)
                  Container(
                    padding: const EdgeInsets.all(20),
                    decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(12)),
                    child: const Row(children: [
                      Icon(Icons.chat_bubble_outline_rounded,
                          color: AppColors.textLight, size: 24),
                      SizedBox(width: 12),
                      Text('Be the first to answer!',
                          style: TextStyle(color: AppColors.textMedium)),
                    ]),
                  ),
                ]),
          ),
        ),

        // Answer input bar
        Container(
          padding: EdgeInsets.fromLTRB(
              16, 12, 16, MediaQuery.of(context).viewInsets.bottom + 12),
          decoration: const BoxDecoration(
            color: Colors.white,
            boxShadow: [
              BoxShadow(
                  color: Color(0x1A000000),
                  blurRadius: 12,
                  offset: Offset(0, -3))
            ],
          ),
          child: Row(children: [
            Expanded(
              child: TextField(
                controller: _answerCtrl,
                decoration: InputDecoration(
                  hintText: 'Write your answer...',
                  contentPadding: const EdgeInsets.symmetric(
                      horizontal: 16, vertical: 10),
                  border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(24),
                      borderSide:
                          const BorderSide(color: AppColors.divider)),
                  filled: true,
                  fillColor: AppColors.background,
                ),
              ),
            ),
            const SizedBox(width: 10),
            GestureDetector(
              onTap: _submitting ? null : _postAnswer,
              child: Container(
                padding: const EdgeInsets.all(12),
                decoration: const BoxDecoration(
                    color: Color(0xFF302B63), shape: BoxShape.circle),
                child: _submitting
                    ? const SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(
                            strokeWidth: 2, color: Colors.white))
                    : const Icon(Icons.send_rounded,
                        color: Colors.white, size: 20),
              ),
            ),
          ]),
        ),
      ]),
    );
  }
}
