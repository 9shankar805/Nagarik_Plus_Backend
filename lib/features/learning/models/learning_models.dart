import 'road_sign.dart'; // QuizQuestion lives here

class LearningCategory {
  final String slug;
  final String title;
  final String? titleNp;
  final String? description;
  final int chapterCount;
  final int mockTestCount;
  final String? iconUrl;
  final String? icon;
  final String? colorCode;

  LearningCategory({
    required this.slug,
    required this.title,
    this.titleNp,
    this.description,
    this.chapterCount = 0,
    this.mockTestCount = 0,
    this.iconUrl,
    this.icon,
    this.colorCode,
  });

  factory LearningCategory.fromJson(Map<String, dynamic> json) {
    return LearningCategory(
      slug: json['slug']?.toString() ?? '',
      title: json['name_en']?.toString() ?? json['title_en']?.toString() ?? json['title']?.toString() ?? json['name']?.toString() ?? '',
      titleNp: json['name_np']?.toString() ?? json['title_np']?.toString(),
      description: json['description']?.toString(),
      chapterCount: json['chapter_count'] is int ? json['chapter_count'] as int : (int.tryParse(json['chapter_count']?.toString() ?? '0') ?? 0),
      mockTestCount: json['mock_test_count'] is int ? json['mock_test_count'] as int : (int.tryParse(json['mock_test_count']?.toString() ?? '0') ?? 0),
      iconUrl: json['icon_url']?.toString(),
      icon: json['icon']?.toString(),
      colorCode: json['color_code']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'slug': slug,
      'title': title,
      'title_np': titleNp,
      'description': description,
      'chapter_count': chapterCount,
      'mock_test_count': mockTestCount,
      'icon_url': iconUrl,
      'icon': icon,
      'color_code': colorCode,
    };
  }
}

class LearningChapter {
  final String id;
  final String? categorySlug;
  final String title;
  final String? titleNp;
  final String? content;
  final String? contentNp;
  final bool isRead;
  final int readTimeMinutes;

  LearningChapter({
    required this.id,
    this.categorySlug,
    required this.title,
    this.titleNp,
    this.content,
    this.contentNp,
    this.isRead = false,
    this.readTimeMinutes = 0,
  });

  factory LearningChapter.fromJson(Map<String, dynamic> json) {
    return LearningChapter(
      id: json['id']?.toString() ?? '',
      categorySlug: json['category_slug']?.toString(),
      title: json['title_en']?.toString() ?? json['name_en']?.toString() ?? json['title']?.toString() ?? json['name']?.toString() ?? '',
      titleNp: json['title_np']?.toString() ?? json['name_np']?.toString(),
      content: json['content']?.toString(),
      contentNp: json['content_np']?.toString(),
      isRead: json['is_read'] == true || json['is_read'] == 1 || json['is_read'] == '1',
      readTimeMinutes: json['read_time_minutes'] is int ? json['read_time_minutes'] as int : (int.tryParse(json['read_time_minutes']?.toString() ?? '0') ?? 0),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'category_slug': categorySlug,
      'title': title,
      'title_np': titleNp,
      'content': content,
      'content_np': contentNp,
      'is_read': isRead,
      'read_time_minutes': readTimeMinutes,
    };
  }
}

class MockTest {
  final String id;
  final String? categorySlug;
  final String title;
  final String? titleNp;
  final int durationMinutes;
  final int totalQuestions;
  final bool isFeatured;

  MockTest({
    required this.id,
    this.categorySlug,
    required this.title,
    this.titleNp,
    this.durationMinutes = 0,
    this.totalQuestions = 0,
    this.isFeatured = false,
  });

  factory MockTest.fromJson(Map<String, dynamic> json) {
    return MockTest(
      id: json['id']?.toString() ?? '',
      categorySlug: json['category_slug']?.toString(),
      title: json['title']?.toString() ?? json['title_en']?.toString() ?? json['name_en']?.toString() ?? json['name']?.toString() ?? '',
      titleNp: json['title_np']?.toString() ?? json['name_np']?.toString(),
      durationMinutes: json['duration_minutes'] is int ? json['duration_minutes'] as int : (int.tryParse(json['duration_minutes']?.toString() ?? '0') ?? 0),
      totalQuestions: json['total_questions'] is int ? json['total_questions'] as int : (int.tryParse(json['total_questions']?.toString() ?? '0') ?? 0),
      isFeatured: json['is_featured'] == true || json['is_featured'] == 1 || json['is_featured'] == '1',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'category_slug': categorySlug,
      'title': title,
      'title_np': titleNp,
      'duration_minutes': durationMinutes,
      'total_questions': totalQuestions,
      'is_featured': isFeatured,
    };
  }
}

class Competition {
  final String id;
  final String title;
  final String? titleNp;
  final String? status;
  final String? startTime;
  final String? endTime;
  final int participantsCount;

  Competition({
    required this.id,
    required this.title,
    this.titleNp,
    this.status,
    this.startTime,
    this.endTime,
    this.participantsCount = 0,
  });

  factory Competition.fromJson(Map<String, dynamic> json) {
    return Competition(
      id: json['id']?.toString() ?? '',
      title: json['title']?.toString() ?? json['title_en']?.toString() ?? json['name_en']?.toString() ?? json['name']?.toString() ?? '',
      titleNp: json['title_np']?.toString() ?? json['name_np']?.toString(),
      status: json['status']?.toString(),
      startTime: json['start_time']?.toString(),
      endTime: json['end_time']?.toString(),
      participantsCount: json['participants_count'] is int ? json['participants_count'] as int : (int.tryParse(json['participants_count']?.toString() ?? '0') ?? 0),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'title_np': titleNp,
      'status': status,
      'start_time': startTime,
      'end_time': endTime,
      'participants_count': participantsCount,
    };
  }
}

class LearningShort {
  final String id;
  final String title;
  final String? videoUrl;
  final String? thumbnailUrl;
  final int durationSeconds;
  final String? categorySlug;

  LearningShort({
    required this.id,
    required this.title,
    this.videoUrl,
    this.thumbnailUrl,
    this.durationSeconds = 0,
    this.categorySlug,
  });

  factory LearningShort.fromJson(Map<String, dynamic> json) {
    return LearningShort(
      id: json['id']?.toString() ?? '',
      title: json['title']?.toString() ?? json['title_en']?.toString() ?? json['name_en']?.toString() ?? json['name']?.toString() ?? '',
      videoUrl: json['video_url']?.toString(),
      thumbnailUrl: json['thumbnail_url']?.toString(),
      durationSeconds: json['duration_seconds'] is int ? json['duration_seconds'] as int : (int.tryParse(json['duration_seconds']?.toString() ?? '0') ?? 0),
      categorySlug: json['category_slug']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'video_url': videoUrl,
      'thumbnail_url': thumbnailUrl,
      'duration_seconds': durationSeconds,
      'category_slug': categorySlug,
    };
  }
}

class CompetitionResult {
  final int rank;
  final double score;
  final int totalQuestions;
  final int correctAnswers;
  final int timeTakenSeconds;

  CompetitionResult({
    this.rank = 0,
    this.score = 0.0,
    this.totalQuestions = 0,
    this.correctAnswers = 0,
    this.timeTakenSeconds = 0,
  });

  factory CompetitionResult.fromJson(Map<String, dynamic> json) {
    return CompetitionResult(
      rank: json['rank'] is int ? json['rank'] as int : (int.tryParse(json['rank']?.toString() ?? '0') ?? 0),
      score: json['score'] is num ? (json['score'] as num).toDouble() : (double.tryParse(json['score']?.toString() ?? '0') ?? 0.0),
      totalQuestions: json['total_questions'] is int ? json['total_questions'] as int : (int.tryParse(json['total_questions']?.toString() ?? '0') ?? 0),
      correctAnswers: json['correct_answers'] is int ? json['correct_answers'] as int : (int.tryParse(json['correct_answers']?.toString() ?? '0') ?? 0),
      timeTakenSeconds: json['time_taken_seconds'] is int ? json['time_taken_seconds'] as int : (int.tryParse(json['time_taken_seconds']?.toString() ?? '0') ?? 0),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'rank': rank,
      'score': score,
      'total_questions': totalQuestions,
      'correct_answers': correctAnswers,
      'time_taken_seconds': timeTakenSeconds,
    };
  }
}

class LearningStat {
  final double passRate;
  final double avgScore;
  final int testsTaken;
  final int competitionsJoined;

  LearningStat({
    this.passRate = 0.0,
    this.avgScore = 0.0,
    this.testsTaken = 0,
    this.competitionsJoined = 0,
  });

  factory LearningStat.fromJson(Map<String, dynamic> json) {
    return LearningStat(
      passRate: json['pass_rate'] is num ? (json['pass_rate'] as num).toDouble() : (double.tryParse(json['pass_rate']?.toString() ?? '0') ?? 0.0),
      avgScore: json['avg_score'] is num ? (json['avg_score'] as num).toDouble() : (double.tryParse(json['avg_score']?.toString() ?? '0') ?? 0.0),
      testsTaken: json['tests_taken'] is int ? json['tests_taken'] as int : (int.tryParse(json['tests_taken']?.toString() ?? '0') ?? 0),
      competitionsJoined: json['competitions_joined'] is int ? json['competitions_joined'] as int : (int.tryParse(json['competitions_joined']?.toString() ?? '0') ?? 0),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'pass_rate': passRate,
      'avg_score': avgScore,
      'tests_taken': testsTaken,
      'competitions_joined': competitionsJoined,
    };
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Program (Course catalog)
// ─────────────────────────────────────────────────────────────────────────────
class Program {
  final String slug;
  final String title;
  final String? titleNp;
  final String? description;
  final String? thumbnailUrl;
  final String? status;
  final bool isEnrolled;

  Program({
    required this.slug,
    required this.title,
    this.titleNp,
    this.description,
    this.thumbnailUrl,
    this.status,
    this.isEnrolled = false,
  });

  factory Program.fromJson(Map<String, dynamic> json) => Program(
        slug: json['slug']?.toString() ?? '',
        title: json['title']?.toString() ?? json['name']?.toString() ?? '',
        titleNp: json['title_np']?.toString() ?? json['name_np']?.toString(),
        description: json['description']?.toString(),
        thumbnailUrl: json['thumbnail_url']?.toString() ?? json['thumbnail']?.toString(),
        status: json['status']?.toString(),
        isEnrolled: json['is_enrolled'] == true || json['is_enrolled'] == 1,
      );

  Map<String, dynamic> toJson() => {
        'slug': slug,
        'title': title,
        'title_np': titleNp,
        'description': description,
        'thumbnail_url': thumbnailUrl,
        'status': status,
        'is_enrolled': isEnrolled,
      };
}

// ─────────────────────────────────────────────────────────────────────────────
// VideoClass
// ─────────────────────────────────────────────────────────────────────────────
class VideoClass {
  final String id;
  final String title;
  final String? titleNp;
  final String? videoUrl;
  final String? thumbnailUrl;
  final String? instructor;
  final int durationSeconds;
  final String? categoryId;
  final String? status;
  final bool isLive;
  final bool isWatched;

  VideoClass({
    required this.id,
    required this.title,
    this.titleNp,
    this.videoUrl,
    this.thumbnailUrl,
    this.instructor,
    this.durationSeconds = 0,
    this.categoryId,
    this.status,
    this.isLive = false,
    this.isWatched = false,
  });

  factory VideoClass.fromJson(Map<String, dynamic> json) => VideoClass(
        id: json['id']?.toString() ?? '',
        title: json['title']?.toString() ?? '',
        titleNp: json['title_np']?.toString(),
        videoUrl: json['video_url']?.toString() ?? json['url']?.toString(),
        thumbnailUrl: json['thumbnail_url']?.toString() ?? json['thumbnail']?.toString(),
        instructor: json['instructor']?.toString(),
        durationSeconds: json['duration_seconds'] is int
            ? json['duration_seconds'] as int
            : (int.tryParse(json['duration_seconds']?.toString() ?? '0') ?? 0),
        categoryId: json['category_id']?.toString(),
        status: json['status']?.toString(),
        isLive: json['is_live'] == true || json['is_live'] == 1,
        isWatched: json['is_watched'] == true || json['is_watched'] == 1,
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'title': title,
        'title_np': titleNp,
        'video_url': videoUrl,
        'thumbnail_url': thumbnailUrl,
        'instructor': instructor,
        'duration_seconds': durationSeconds,
        'category_id': categoryId,
        'status': status,
        'is_live': isLive,
        'is_watched': isWatched,
      };
}

// ─────────────────────────────────────────────────────────────────────────────
// FlashcardSet + Flashcard
// ─────────────────────────────────────────────────────────────────────────────
class FlashcardSet {
  final String id;
  final String title;
  final String? titleNp;
  final String? categoryId;
  final int cardCount;

  FlashcardSet({
    required this.id,
    required this.title,
    this.titleNp,
    this.categoryId,
    this.cardCount = 0,
  });

  factory FlashcardSet.fromJson(Map<String, dynamic> json) => FlashcardSet(
        id: json['id']?.toString() ?? '',
        title: json['title']?.toString() ?? '',
        titleNp: json['title_np']?.toString(),
        categoryId: json['category_id']?.toString(),
        cardCount: json['card_count'] is int
            ? json['card_count'] as int
            : (int.tryParse(json['card_count']?.toString() ?? '0') ?? 0),
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'title': title,
        'title_np': titleNp,
        'category_id': categoryId,
        'card_count': cardCount,
      };
}

class Flashcard {
  final String id;
  final String setId;
  final String front;
  final String? frontNp;
  final String back;
  final String? backNp;
  final String? nextReviewDate;
  final int intervalDays;

  Flashcard({
    required this.id,
    required this.setId,
    required this.front,
    this.frontNp,
    required this.back,
    this.backNp,
    this.nextReviewDate,
    this.intervalDays = 1,
  });

  factory Flashcard.fromJson(Map<String, dynamic> json) => Flashcard(
        id: json['id']?.toString() ?? '',
        setId: json['set_id']?.toString() ?? '',
        front: json['front']?.toString() ?? '',
        frontNp: json['front_np']?.toString(),
        back: json['back']?.toString() ?? '',
        backNp: json['back_np']?.toString(),
        nextReviewDate: json['next_review_date']?.toString(),
        intervalDays: json['interval_days'] is int
            ? json['interval_days'] as int
            : (int.tryParse(json['interval_days']?.toString() ?? '1') ?? 1),
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'set_id': setId,
        'front': front,
        'front_np': frontNp,
        'back': back,
        'back_np': backNp,
        'next_review_date': nextReviewDate,
        'interval_days': intervalDays,
      };
}

// ─────────────────────────────────────────────────────────────────────────────
// DailyQuiz
// ─────────────────────────────────────────────────────────────────────────────
class DailyQuiz {
  final String id;
  final String question;
  final String? questionNp;
  final List<String> options;
  final List<String>? optionsNp;
  final int correctOptionIndex;
  final String? explanation;
  final String date;
  final bool isAnswered;
  final int? selectedIndex;

  DailyQuiz({
    required this.id,
    required this.question,
    this.questionNp,
    required this.options,
    this.optionsNp,
    required this.correctOptionIndex,
    this.explanation,
    required this.date,
    this.isAnswered = false,
    this.selectedIndex,
  });

  factory DailyQuiz.fromJson(Map<String, dynamic> json) {
    final opts = json['options'] is List
        ? (json['options'] as List).map((e) => e.toString()).toList()
        : <String>[];
    final optsNp = json['options_np'] is List
        ? (json['options_np'] as List).map((e) => e.toString()).toList()
        : null;
    return DailyQuiz(
      id: json['id']?.toString() ?? '',
      question: json['question']?.toString() ?? '',
      questionNp: json['question_np']?.toString(),
      options: opts,
      optionsNp: optsNp,
      correctOptionIndex: json['correct_option_index'] is int
          ? json['correct_option_index'] as int
          : (int.tryParse(json['correct_option_index']?.toString() ?? '0') ?? 0),
      explanation: json['explanation']?.toString(),
      date: json['date']?.toString() ?? '',
      isAnswered: json['is_answered'] == true || json['is_answered'] == 1,
      selectedIndex: json['selected_index'] is int ? json['selected_index'] as int : null,
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'question': question,
        'question_np': questionNp,
        'options': options,
        'options_np': optionsNp,
        'correct_option_index': correctOptionIndex,
        'explanation': explanation,
        'date': date,
        'is_answered': isAnswered,
        'selected_index': selectedIndex,
      };
}

// ─────────────────────────────────────────────────────────────────────────────
// Achievement / Badge
// ─────────────────────────────────────────────────────────────────────────────
class Achievement {
  final String id;
  final String title;
  final String? titleNp;
  final String? description;
  final String? iconUrl;
  final bool isEarned;
  final String? earnedAt;
  final double progress;     // 0.0 – 1.0

  Achievement({
    required this.id,
    required this.title,
    this.titleNp,
    this.description,
    this.iconUrl,
    this.isEarned = false,
    this.earnedAt,
    this.progress = 0.0,
  });

  factory Achievement.fromJson(Map<String, dynamic> json) => Achievement(
        id: json['id']?.toString() ?? '',
        title: json['title']?.toString() ?? '',
        titleNp: json['title_np']?.toString(),
        description: json['description']?.toString(),
        iconUrl: json['icon_url']?.toString() ?? json['icon']?.toString(),
        isEarned: json['is_earned'] == true || json['is_earned'] == 1,
        earnedAt: json['earned_at']?.toString(),
        progress: json['progress'] is num
            ? (json['progress'] as num).toDouble()
            : (double.tryParse(json['progress']?.toString() ?? '0') ?? 0.0),
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'title': title,
        'title_np': titleNp,
        'description': description,
        'icon_url': iconUrl,
        'is_earned': isEarned,
        'earned_at': earnedAt,
        'progress': progress,
      };
}

// ─────────────────────────────────────────────────────────────────────────────
// Doubt + DoubtAnswer
// ─────────────────────────────────────────────────────────────────────────────
class Doubt {
  final String id;
  final String title;
  final String body;
  final String? categoryId;
  final String? chapterId;
  final String? imageUrl;
  final String? status;
  final int upvotes;
  final int answersCount;
  final String? createdAt;
  final bool isOwn;

  Doubt({
    required this.id,
    required this.title,
    required this.body,
    this.categoryId,
    this.chapterId,
    this.imageUrl,
    this.status,
    this.upvotes = 0,
    this.answersCount = 0,
    this.createdAt,
    this.isOwn = false,
  });

  factory Doubt.fromJson(Map<String, dynamic> json) => Doubt(
        id: json['id']?.toString() ?? '',
        title: json['title']?.toString() ?? '',
        body: json['body']?.toString() ?? '',
        categoryId: json['learning_category_id']?.toString() ?? json['category_id']?.toString(),
        chapterId: json['learning_chapter_id']?.toString() ?? json['chapter_id']?.toString(),
        imageUrl: json['image']?.toString() ?? json['image_url']?.toString(),
        status: json['status']?.toString(),
        upvotes: json['upvotes'] is int
            ? json['upvotes'] as int
            : (int.tryParse(json['upvotes']?.toString() ?? '0') ?? 0),
        answersCount: json['answers_count'] is int
            ? json['answers_count'] as int
            : (int.tryParse(json['answers_count']?.toString() ?? '0') ?? 0),
        createdAt: json['created_at']?.toString(),
        isOwn: json['is_own'] == true || json['is_own'] == 1,
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'title': title,
        'body': body,
        'learning_category_id': categoryId,
        'learning_chapter_id': chapterId,
        'image_url': imageUrl,
        'status': status,
        'upvotes': upvotes,
        'answers_count': answersCount,
        'created_at': createdAt,
        'is_own': isOwn,
      };
}

class DoubtAnswer {
  final String id;
  final String doubtId;
  final String body;
  final String? imageUrl;
  final bool isAccepted;
  final String? createdAt;

  DoubtAnswer({
    required this.id,
    required this.doubtId,
    required this.body,
    this.imageUrl,
    this.isAccepted = false,
    this.createdAt,
  });

  factory DoubtAnswer.fromJson(Map<String, dynamic> json) => DoubtAnswer(
        id: json['id']?.toString() ?? '',
        doubtId: json['doubt_id']?.toString() ?? '',
        body: json['body']?.toString() ?? '',
        imageUrl: json['image']?.toString() ?? json['image_url']?.toString(),
        isAccepted: json['is_accepted'] == true || json['is_accepted'] == 1,
        createdAt: json['created_at']?.toString(),
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'doubt_id': doubtId,
        'body': body,
        'image_url': imageUrl,
        'is_accepted': isAccepted,
        'created_at': createdAt,
      };
}

// ─────────────────────────────────────────────────────────────────────────────
// PracticeSession
// ─────────────────────────────────────────────────────────────────────────────
class PracticeSession {
  final String sessionId;
  final List<QuizQuestion> questions;
  final int? correct;
  final int? wrong;
  final double? scorePct;
  final List<Map<String, dynamic>> perQuestionResults;

  PracticeSession({
    required this.sessionId,
    required this.questions,
    this.correct,
    this.wrong,
    this.scorePct,
    this.perQuestionResults = const [],
  });

  factory PracticeSession.fromJson(Map<String, dynamic> json) {
    final rawQs = json['questions'] is List ? json['questions'] as List : [];
    return PracticeSession(
      sessionId: json['session_id']?.toString() ?? '',
      questions: rawQs
          .map((q) => QuizQuestion.fromJson(q as Map<String, dynamic>))
          .toList(),
      correct: json['correct'] is int ? json['correct'] as int : null,
      wrong: json['wrong'] is int ? json['wrong'] as int : null,
      scorePct: json['score_pct'] is num
          ? (json['score_pct'] as num).toDouble()
          : null,
      perQuestionResults: json['per_question_results'] is List
          ? (json['per_question_results'] as List)
              .map((e) => e as Map<String, dynamic>)
              .toList()
          : [],
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// AdaptiveSession
// ─────────────────────────────────────────────────────────────────────────────
class AdaptiveSession {
  final String sessionToken;
  final int eloScore;
  final QuizQuestion? firstQuestion;
  final QuizQuestion? nextQuestion;
  final bool? isCorrect;
  final int? eloDelta;
  final int? newElo;
  final double? scorePct;
  final double? weightedScorePct;
  final double? percentileScore;
  final Map<String, dynamic> topicBreakdown;
  final List<String> weakAreas;

  AdaptiveSession({
    required this.sessionToken,
    this.eloScore = 1200,
    this.firstQuestion,
    this.nextQuestion,
    this.isCorrect,
    this.eloDelta,
    this.newElo,
    this.scorePct,
    this.weightedScorePct,
    this.percentileScore,
    this.topicBreakdown = const {},
    this.weakAreas = const [],
  });

  factory AdaptiveSession.fromJson(Map<String, dynamic> json) => AdaptiveSession(
        sessionToken: json['session_token']?.toString() ?? '',
        eloScore: json['elo_score'] is int
            ? json['elo_score'] as int
            : (int.tryParse(json['elo_score']?.toString() ?? '1200') ?? 1200),
        firstQuestion: json['first_question'] != null
            ? QuizQuestion.fromJson(json['first_question'] as Map<String, dynamic>)
            : null,
        nextQuestion: json['next_question'] != null
            ? QuizQuestion.fromJson(json['next_question'] as Map<String, dynamic>)
            : null,
        isCorrect: json['is_correct'] as bool?,
        eloDelta: json['elo_delta'] is int ? json['elo_delta'] as int : null,
        newElo: json['new_elo'] is int ? json['new_elo'] as int : null,
        scorePct: json['score_percentage'] is num
            ? (json['score_percentage'] as num).toDouble()
            : null,
        weightedScorePct: json['weighted_score_pct'] is num
            ? (json['weighted_score_pct'] as num).toDouble()
            : null,
        percentileScore: json['percentile_score'] is num
            ? (json['percentile_score'] as num).toDouble()
            : null,
        topicBreakdown: json['topic_breakdown'] is Map
            ? json['topic_breakdown'] as Map<String, dynamic>
            : {},
        weakAreas: json['weak_areas'] is List
            ? (json['weak_areas'] as List).map((e) => e.toString()).toList()
            : [],
      );
}

// ─────────────────────────────────────────────────────────────────────────────
// Subscription
// ─────────────────────────────────────────────────────────────────────────────
class SubscriptionPackage {
  final String id;
  final String name;
  final String? description;
  final double price;
  final String currency;
  final int durationDays;
  final String billingCycle; // monthly, yearly, free

  SubscriptionPackage({
    required this.id,
    required this.name,
    this.description,
    this.price = 0,
    this.currency = 'NPR',
    this.durationDays = 30,
    this.billingCycle = 'monthly',
  });

  factory SubscriptionPackage.fromJson(Map<String, dynamic> json) =>
      SubscriptionPackage(
        id: json['id']?.toString() ?? '',
        name: json['name']?.toString() ?? '',
        description: json['description']?.toString(),
        price: json['price'] is num
            ? (json['price'] as num).toDouble()
            : (double.tryParse(json['price']?.toString() ?? '0') ?? 0),
        currency: json['currency']?.toString() ?? 'NPR',
        durationDays: json['duration_days'] is int
            ? json['duration_days'] as int
            : (int.tryParse(json['duration_days']?.toString() ?? '30') ?? 30),
        billingCycle: json['billing_cycle']?.toString() ?? 'monthly',
      );

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'description': description,
        'price': price,
        'currency': currency,
        'duration_days': durationDays,
        'billing_cycle': billingCycle,
      };
}

class Subscription {
  final String id;
  final String packageId;
  final String packageName;
  final String status;
  final String? expiresAt;
  final int daysRemaining;

  Subscription({
    required this.id,
    required this.packageId,
    required this.packageName,
    required this.status,
    this.expiresAt,
    this.daysRemaining = 0,
  });

  factory Subscription.fromJson(Map<String, dynamic> json) {
    final pkg = json['package'];
    return Subscription(
      id: json['id']?.toString() ?? '',
      packageId: (pkg is Map ? pkg['id'] : json['package_id'])?.toString() ?? '',
      packageName: (pkg is Map ? pkg['name'] : json['package_name'])?.toString() ?? '',
      status: json['status']?.toString() ?? 'inactive',
      expiresAt: json['expires_at']?.toString(),
      daysRemaining: json['days_remaining'] is int
          ? json['days_remaining'] as int
          : (int.tryParse(json['days_remaining']?.toString() ?? '0') ?? 0),
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'package_id': packageId,
        'package_name': packageName,
        'status': status,
        'expires_at': expiresAt,
        'days_remaining': daysRemaining,
      };
}
