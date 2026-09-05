import '../../../core/config/env_config.dart';

class SocialServiceModel {
  final String id;
  final String? title;
  final String? titleNp;
  final String? subtitle;
  final String? subtitleNp;
  final String? icon;
  final String? color;
  final String? imageUrl;

  SocialServiceModel({
    required this.id,
    this.title,
    this.titleNp,
    this.subtitle,
    this.subtitleNp,
    this.icon,
    this.color,
    this.imageUrl,
  });

  factory SocialServiceModel.fromJson(Map<String, dynamic> json) {
    String? imgUrl = json['image_url'];
    if (imgUrl != null && imgUrl.startsWith('/')) {
      imgUrl = '${EnvConfig.domain}$imgUrl';
    }

    return SocialServiceModel(
      id: json['id'],
      title: json['title'],
      titleNp: json['title_np'],
      subtitle: json['subtitle'],
      subtitleNp: json['subtitle_np'],
      icon: json['icon'],
      color: json['color'],
      imageUrl: imgUrl,
    );
  }
}

