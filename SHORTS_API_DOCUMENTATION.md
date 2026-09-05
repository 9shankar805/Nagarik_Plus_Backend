# Nagarik Shorts API Documentation

## Overview
This document provides comprehensive documentation for the Nagarik Shorts API endpoints. These endpoints enable TikTok-style video creation, upload, and consumption in the Nagarik+ application.

## Base URL
```
https://nagarikplus.techprocod.com.np/api/v1
```

## Authentication
Most endpoints require authentication using Laravel Sanctum. Include the Bearer token in the Authorization header:
```
Authorization: Bearer {token}
```

---

## 1. Audio & Music Endpoints

### Get Trending Audio
Fetches the most popular songs for the "Add Sound" feature.

**Endpoint:** `GET /api/v1/audio/trending`

**Authentication:** Not required

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Nagarik Theme",
      "artist": "Nagarik Official",
      "duration_seconds": 60,
      "cover_image_url": "https://cdn.nagarik.com/audio/covers/123.jpg",
      "audio_url": "https://cdn.nagarik.com/audio/files/123.mp3",
      "usage_count": 14500
    }
  ]
}
```

---

### Get Audio Categories
Fetches available music genres (Pop, Classical, News Audio, Funny, etc.).

**Endpoint:** `GET /api/v1/audio/categories`

**Authentication:** Not required

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Pop",
      "slug": "pop",
      "icon": "🎵"
    },
    {
      "id": 2,
      "name": "Classical",
      "slug": "classical",
      "icon": "🎻"
    }
  ]
}
```

---

### Search Audio
Allows users to search for specific songs by title or artist.

**Endpoint:** `GET /api/v1/audio/search?q={query}`

**Authentication:** Not required

**Parameters:**
- `q` (required): Search query string (minimum 2 characters)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Nagarik Theme",
      "artist": "Nagarik Official",
      "duration_seconds": 60,
      "cover_image_url": "https://cdn.nagarik.com/audio/covers/123.jpg",
      "audio_url": "https://cdn.nagarik.com/audio/files/123.mp3",
      "usage_count": 14500
    }
  ]
}
```

---

### Get Audio by Category
Fetches audio tracks within a specific category.

**Endpoint:** `GET /api/v1/audio/category?category_id={id}`

**Authentication:** Not required

**Parameters:**
- `category_id` (required): ID of the audio category

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Nagarik Theme",
      "artist": "Nagarik Official",
      "duration_seconds": 60,
      "cover_image_url": "https://cdn.nagarik.com/audio/covers/123.jpg",
      "audio_url": "https://cdn.nagarik.com/audio/files/123.mp3",
      "usage_count": 14500
    }
  ]
}
```

---

## 2. Effects & AR Filters Endpoints

### Get Effect Categories
Returns filter categories like "Trending", "Beauty", "Funny", "Backgrounds".

**Endpoint:** `GET /api/v1/effects/categories`

**Authentication:** Not required

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Trending",
      "slug": "trending",
      "icon": "🔥"
    },
    {
      "id": 2,
      "name": "Beauty",
      "slug": "beauty",
      "icon": "✨"
    }
  ]
}
```

---

### Get Effects by Category
Returns the list of available filters for a specific category.

**Endpoint:** `GET /api/v1/effects/list?category_id={id}`

**Authentication:** Not required

**Parameters:**
- `category_id` (required): ID of the effect category

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Beauty Smooth",
      "thumbnail_url": "https://cdn.nagarik.com/effects/thumb_beauty.png",
      "deepar_file_url": "https://cdn.nagarik.com/effects/files/beauty.deepar",
      "file_size_kb": 1200
    }
  ]
}
```

**Note:** The Flutter app should download the `deepar_file_url`, cache it locally, and feed the file path to `DeepArController.switchEffect()`.

---

### Get Trending Effects
Fetches the most popular AR filters.

**Endpoint:** `GET /api/v1/effects/trending`

**Authentication:** Not required

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Beauty Smooth",
      "thumbnail_url": "https://cdn.nagarik.com/effects/thumb_beauty.png",
      "deepar_file_url": "https://cdn.nagarik.com/effects/files/beauty.deepar",
      "file_size_kb": 1200
    }
  ]
}
```

---

## 3. Video Upload & Processing

### Upload Video
Uploads the raw MP4 file to the server.

**Endpoint:** `POST /api/v1/shorts/upload`

**Authentication:** Required

**Headers:**
- `Content-Type: multipart/form-data`
- `Authorization: Bearer {token}`

**Body:**
- `video_file` (required): Binary MP4 file (max 100MB)
- `cover_image` (optional): Binary cover image file (max 10MB)

**Response:**
```json
{
  "success": true,
  "data": {
    "video_id": "vid_1722585600_1234",
    "video_url": "https://nagarikplus.techprocod.com.np/storage/shorts/raw/video_123.mp4",
    "cover_image_url": "https://nagarikplus.techprocod.com.np/storage/shorts/covers/cover_123.jpg"
  }
}
```

---

### Publish Short
Attaches the uploaded video to a user's post with metadata.

**Endpoint:** `POST /api/v1/shorts/publish`

**Authentication:** Required

**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer {token}`

**Body:**
```json
{
  "video_id": "vid_1722585600_1234",
  "video_url": "https://nagarikplus.techprocod.com.np/storage/shorts/raw/video_123.mp4",
  "audio_id": 1,
  "effect_id": 1,
  "caption": "Testing the new Nagarik Shorts feature! #NagarikApp",
  "privacy": "PUBLIC",
  "location": "Kathmandu, Nepal",
  "cover_image_url": "https://nagarikplus.techprocod.com.np/storage/shorts/covers/cover_123.jpg"
}
```

**Parameters:**
- `video_id` (required): Temporary video ID from upload endpoint
- `video_url` (required): URL of the uploaded video
- `audio_id` (optional): ID of the audio track used
- `effect_id` (optional): ID of the AR filter used
- `caption` (optional): Video caption (max 500 characters)
- `privacy` (required): Privacy setting - "PUBLIC", "PRIVATE", or "F" (Friends)
- `location` (optional): Location string (max 255 characters)
- `cover_image_url` (optional): URL of the cover image

**Response:**
```json
{
  "success": true,
  "data": {
    "short_id": 123,
    "video_url": "https://nagarikplus.techprocod.com.np/storage/shorts/raw/video_123.mp4",
    "published_at": "2026-08-01T18:00:00.000000Z"
  }
}
```

---

## 4. Shorts Feed (Consumption)

### Get Shorts Feed
Fetches a paginated list of public shorts for the TikTok-style feed.

**Endpoint:** `GET /api/v1/shorts/feed?page=1&limit=10`

**Authentication:** Not required

**Parameters:**
- `page` (optional): Page number (default: 1)
- `limit` (optional): Items per page (default: 10, max: 50)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "author": {
        "id": 1,
        "name": "Ramesh Sharma",
        "avatar_url": "https://cdn.nagarik.com/avatars/111.jpg",
        "is_verified": true
      },
      "video_url": "https://nagarikplus.techprocod.com.np/storage/shorts/raw/video_123.mp4",
      "cover_image_url": "https://nagarikplus.techprocod.com.np/storage/shorts/covers/cover_123.jpg",
      "caption": "Testing the new Nagarik Shorts feature! #NagarikApp",
      "location": "Kathmandu, Nepal",
      "likes_count": 1200,
      "comments_count": 45,
      "shares_count": 12,
      "views_count": 5000,
      "audio": {
        "id": 1,
        "title": "Nagarik Theme"
      },
      "effect": {
        "id": 1,
        "title": "Beauty Smooth"
      },
      "published_at": "2026-08-01T18:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 10,
    "per_page": 10,
    "total": 100
  }
}
```

**Performance Note:** For optimal performance on slow Nepali internet connections, consider implementing video compression and HLS streaming using FFmpeg or AWS Elemental MediaConvert.

---

### Get My Shorts
Fetches the authenticated user's own shorts.

**Endpoint:** `GET /api/v1/shorts/my-shorts?page=1&limit=10`

**Authentication:** Required

**Parameters:**
- `page` (optional): Page number (default: 1)
- `limit` (optional): Items per page (default: 10, max: 50)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "video_url": "https://nagarikplus.techprocod.com.np/storage/shorts/raw/video_123.mp4",
      "cover_image_url": "https://nagarikplus.techprocod.com.np/storage/shorts/covers/cover_123.jpg",
      "caption": "Testing the new Nagarik Shorts feature! #NagarikApp",
      "privacy": "PUBLIC",
      "likes_count": 1200,
      "comments_count": 45,
      "shares_count": 12,
      "views_count": 5000,
      "is_processed": true,
      "published_at": "2026-08-01T18:00:00.000000Z",
      "created_at": "2026-08-01T17:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "per_page": 10,
    "total": 50
  }
}
```

---

### Increment View Count
Tracks when a user views a short.

**Endpoint:** `POST /api/v1/shorts/view`

**Authentication:** Not required

**Headers:**
- `Content-Type: application/json`

**Body:**
```json
{
  "short_id": 123
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "views_count": 5001
  }
}
```

---

### Toggle Like
Likes or unlikes a short.

**Endpoint:** `POST /api/v1/shorts/like`

**Authentication:** Required

**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer {token}`

**Body:**
```json
{
  "short_id": 123
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "likes_count": 1201,
    "is_liked": true
  }
}
```

**Note:** This is a simplified implementation. In production, use a dedicated likes table to track user-specific likes.

---

### Delete Short
Deletes a short (only by the owner).

**Endpoint:** `DELETE /api/v1/shorts/delete`

**Authentication:** Required

**Headers:**
- `Content-Type: application/json`
- `Authorization: Bearer {token}`

**Body:**
```json
{
  "short_id": 123
}
```

**Response:**
```json
{
  "success": true,
  "message": "Short deleted successfully"
}
```

---

## Database Schema

### user_shorts Table
```sql
- id (bigint, primary key)
- user_id (bigint, foreign key to users)
- audio_id (bigint, nullable, foreign key to audio)
- effect_id (bigint, nullable, foreign key to effects)
- video_url (varchar)
- cover_image_url (varchar, nullable)
- caption (text, nullable)
- privacy (enum: PUBLIC, PRIVATE, F)
- location (varchar, nullable)
- likes_count (int, default: 0)
- comments_count (int, default: 0)
- shares_count (int, default: 0)
- views_count (int, default: 0)
- is_processed (boolean, default: false)
- published_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### audio Table
```sql
- id (bigint, primary key)
- title (varchar)
- artist (varchar, nullable)
- duration_seconds (int)
- cover_image_url (varchar, nullable)
- audio_url (varchar)
- category_id (bigint, nullable)
- usage_count (int, default: 0)
- is_trending (boolean, default: false)
- created_at (timestamp)
- updated_at (timestamp)
```

### audio_categories Table
```sql
- id (bigint, primary key)
- name (varchar)
- slug (varchar, unique)
- icon (varchar, nullable)
- sort_order (int, default: 0)
- created_at (timestamp)
- updated_at (timestamp)
```

### effects Table
```sql
- id (bigint, primary key)
- title (varchar)
- category_id (bigint, nullable)
- thumbnail_url (varchar)
- deepar_file_url (varchar)
- file_size_kb (int)
- is_trending (boolean, default: false)
- usage_count (int, default: 0)
- created_at (timestamp)
- updated_at (timestamp)
```

### effect_categories Table
```sql
- id (bigint, primary key)
- name (varchar)
- slug (varchar, unique)
- icon (varchar, nullable)
- sort_order (int, default: 0)
- created_at (timestamp)
- updated_at (timestamp)
```

---

## File Upload Configuration

### Storage
- Video files are stored in `storage/app/public/shorts/raw/`
- Cover images are stored in `storage/app/public/shorts/covers/`
- Public storage is accessible via `/storage/` symlink

### File Size Limits
- Video files: Maximum 100MB
- Cover images: Maximum 10MB

### Supported Formats
- Video: MP4, MOV, AVI
- Cover images: JPG, JPEG, PNG

---

## Error Responses

All endpoints return error responses in the following format:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### Common HTTP Status Codes
- `200 OK`: Successful request
- `201 Created`: Resource created successfully
- `400 Bad Request`: Invalid request parameters
- `401 Unauthorized`: Authentication required or invalid
- `403 Forbidden`: User lacks permission
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation error
- `500 Internal Server Error`: Server error

---

## Production Recommendations

### Video Processing
For optimal performance on slow Nepali internet connections:
1. Implement video compression using FFmpeg
2. Generate HLS (`.m3u8`) streams for adaptive bitrate streaming
3. Use AWS Elemental MediaConvert or similar service for cloud processing
4. Implement CDN caching for video files

### Caching
- Cache audio and effects lists using Redis
- Implement CDN caching for static assets
- Use Laravel's cache for frequently accessed data

### Security
- Implement rate limiting on upload endpoints
- Validate file types and scan for malware
- Use signed URLs for direct file access when needed
- Implement content moderation for published shorts

### Scalability
- Move file storage to AWS S3 or similar cloud storage
- Implement queue-based video processing
- Use database read replicas for feed queries
- Consider implementing a dedicated media server

---

## Flutter Integration Guide

### Audio Selection Flow
1. Call `GET /api/v1/audio/trending` to show trending audio
2. Call `GET /api/v1/audio/categories` to show category tabs
3. When user selects category, call `GET /api/v1/audio/category?category_id={id}`
4. When user searches, call `GET /api/v1/audio/search?q={query}`
5. Cache selected audio ID for upload

### Effects Selection Flow
1. Call `GET /api/v1/effects/categories` to show effect tabs
2. When user selects category, call `GET /api/v1/effects/list?category_id={id}`
3. Download `deepar_file_url` and cache locally
4. Feed cached file path to `DeepArController.switchEffect()`

### Video Upload Flow
1. Record video in Flutter
2. Generate cover image thumbnail
3. Call `POST /api/v1/shorts/upload` with video file and cover image
4. Receive `video_id` and `video_url` in response
5. Call `POST /api/v1/shorts/publish` with metadata and received URLs
6. Handle success/error responses appropriately

### Feed Consumption Flow
1. Call `GET /api/v1/shorts/feed?page=1&limit=10` on initial load
2. Implement infinite scroll by incrementing page number
3. Cache video URLs for offline viewing
4. Call `POST /api/v1/shorts/view` when user watches a video
5. Call `POST /api/v1/shorts/like` when user taps like button

---

## Support & Contact

For API support or questions, contact the development team at:
- Email: dev@nagarikplus.com
- Documentation: https://docs.nagarikplus.com

---

**Last Updated:** August 1, 2026
**API Version:** v1
**Laravel Version:** 12.64.0
