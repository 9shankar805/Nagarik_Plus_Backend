class AppAssets {
  // Icons
  static const String appIcon  = 'assets/icons/app_icon.png';
  static const String splash   = 'assets/icons/splash.png';
  static const String scanIcon = 'assets/icons/scan.png';
  static const String shareitIcon = 'assets/icons/shareit.png';

  // Document images (used in document cards & home grid)
  static const String nationalId    = 'assets/images/nid1752476653129_15.webp';
  static const String passport      = 'assets/images/passport1752476337775_17.webp';
  static const String drivingLicense= 'assets/images/license1752476621950_11.webp';
  static const String cit           = 'assets/images/cit1759940267390_7.webp';
  static const String ssf           = 'assets/images/SSF1752476396810_21.webp';
  static const String nea           = 'assets/images/nea1752476414169_14.webp';
  static const String pcr           = 'assets/images/pcr1752476863055_19.webp';
  static const String cims          = 'assets/images/cims1752476325868_6.webp';
  static const String dolma         = 'assets/images/dolma1752476593369_9.webp';
  static const String gunaso        = 'assets/images/gunaso1752476491251_10.webp';
  static const String patrakar      = 'assets/images/patrakar1752477622244_18.webp';
  static const String slc           = 'assets/images/slc1631011325238_20.webp';
  static const String pan           = 'assets/images/pan_16.webp';
  static const String voterId       = 'assets/images/voterid_23.webp';

  // Vital event certificates
  static const String birthCert     = 'assets/images/birthcertificate_5.webp';
  static const String marriageCert  = 'assets/images/marriagecertificate_12.webp';
  static const String deathCert     = 'assets/images/deathcertificate_8.webp';
  static const String migrationCert = 'assets/images/migrationcertificate_13.webp';

  // Banner images (home screen hero carousel)
  static const String banner1 = 'assets/Banner/first.webp';
  static const String banner2 = 'assets/Banner/second.webp';
  static const String banner3 = 'assets/Banner/third.webp';
  static const String banner4 = 'assets/Banner/fourth.webp';
  static const String banner5 = 'assets/Banner/fifth.webp';
  static const String templeBanner = 'assets/Banner/temple_24.webp';

  // Background images
  static const String loginBg       = 'assets/bgimages/loginbg_3.webp';
  static const String onboardingBg1 = 'assets/bgimages/first_1.webp';
  static const String onboardingBg2 = 'assets/bgimages/second_4.webp';
  static const String onboardingBg3 = 'assets/bgimages/third_3.webp';
  static const String onboardingBg4 = 'assets/bgimages/fourth_2.webp';

  // Map document type to image
  static String? forDocType(String type) {
    switch (type) {
      case 'national_id':      return nationalId;
      case 'passport':         return passport;
      case 'driving_license':  return drivingLicense;
      case 'pan':              return pan;
      case 'citizenship':      return cims;
      case 'voter_id':         return voterId;
      case 'cit':              return cit;
      case 'ssf':              return ssf;
      default:                 return null;
    }
  }
}
