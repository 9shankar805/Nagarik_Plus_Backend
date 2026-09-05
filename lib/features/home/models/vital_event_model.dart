import '../../../core/config/env_config.dart';

class VitalEventModel {
  final String id;
  final String? title;
  final String? titleNp;
  final String? imageUrl;
  final String? bgColor;
  final String? type;

  VitalEventModel({
    required this.id,
    this.title,
    this.titleNp,
    this.imageUrl,
    this.bgColor,
    this.type,
  });

  factory VitalEventModel.fromJson(Map<String, dynamic> json) {
    String? imgUrl = json['image_url'];
    if (imgUrl != null && imgUrl.startsWith('/')) {
      imgUrl = '${EnvConfig.domain}$imgUrl';
    }

    return VitalEventModel(
      id: json['id'],
      title: json['title'],
      titleNp: json['title_np'],
      imageUrl: imgUrl,
      bgColor: json['bg_color'],
      type: json['type'],
    );
  }
}

