# Nagarik+ API — Laravel Backend

Secure RESTful API backend for the Nagarik+ Flutter application.

## Tech Stack
- **PHP** 8.2+ / **Laravel** 11
- **MySQL** 8.0+
- **Laravel Sanctum** (token authentication)
- **AES-256-CBC** encryption for documents

---

## Quick Setup

### 1. Install MySQL
Download from https://dev.mysql.com/downloads/installer/ (choose MySQL 8.0)

### 2. Create Database
```sql
CREATE DATABASE nagarik_plus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'nagarik_user'@'localhost' IDENTIFIED BY 'StrongPassword123!';
GRANT ALL PRIVILEGES ON nagarik_plus.* TO 'nagarik_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Configure .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nagarik_plus
DB_USERNAME=nagarik_user
DB_PASSWORD=StrongPassword123!

DOCUMENT_ENCRYPTION_KEY=YourStrongRandomKey32CharsLong!!
```

### 4. Run Migrations & Seed
```bash
php artisan migrate:fresh --seed
```

### 5. Start Server
```bash
php artisan serve
# API available at: http://localhost:8000/api/v1
```

---

## API Endpoints (33 total)

### 🔓 Public (no auth required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/auth/register` | Register new user |
| POST | `/api/v1/auth/login` | Login with email + password |
| POST | `/api/v1/auth/login/pin` | Login with PIN (device-bound) |
| GET  | `/api/v1/services` | List all citizen service guides |
| GET  | `/api/v1/services/{slug}` | Get service detail + guide |
| GET  | `/api/v1/services/{slug}/offices` | Get offices for a service |
| GET  | `/api/v1/offices` | All offices (filter by category/district/location) |
| GET  | `/api/v1/news` | Citizen news & public notices |
| GET  | `/api/v1/news/{id}` | News detail |
| GET  | `/api/v1/news/categories` | News categories |
| GET  | `/api/v1/emergency` | Emergency contacts |
| GET  | `/api/v1/ai/suggestions` | AI chat suggestions |
| GET  | `/api/v1/learning/questions` | Quiz questions |
| GET  | `/api/v1/learning/road-signs` | Road signs |

### 🔒 Authenticated (Bearer token required)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET  | `/api/v1/auth/profile` | Get user profile |
| PUT  | `/api/v1/auth/profile` | Update profile |
| POST | `/api/v1/auth/pin` | Set/update PIN |
| POST | `/api/v1/auth/logout` | Logout |
| GET  | `/api/v1/documents` | List documents |
| POST | `/api/v1/documents` | Upload/create document (multipart) |
| GET  | `/api/v1/documents/expiring` | Documents expiring soon |
| GET  | `/api/v1/documents/{id}` | Get document (decrypted) |
| PUT  | `/api/v1/documents/{id}` | Update document |
| DELETE | `/api/v1/documents/{id}` | Delete document |
| GET  | `/api/v1/documents/{id}/download` | Get temp download URL |
| GET  | `/api/v1/reminders` | List reminders |
| POST | `/api/v1/reminders` | Create reminder |
| PUT  | `/api/v1/reminders/{id}` | Update reminder |
| DELETE | `/api/v1/reminders/{id}` | Delete reminder |
| POST | `/api/v1/learning/submit` | Submit quiz answers |
| GET  | `/api/v1/learning/history` | User test history |
| POST | `/api/v1/ai/chat` | AI assistant chat |
| GET  | `/api/v1/ai/history` | Chat history |

---

## Security Features

- ✅ **AES-256-CBC** encryption for all document data and files
- ✅ **Laravel Sanctum** token authentication
- ✅ **Device binding** for PIN login
- ✅ **Rate limiting** via Laravel throttle middleware
- ✅ **Soft deletes** — data not permanently deleted
- ✅ **Input validation** on all endpoints
- ✅ **JSON error responses** for all API routes
- ✅ **User isolation** — users can only access their own documents

---

## Document Types Supported

`national_id` · `passport` · `driving_license` · `pan` · `citizenship` ·
`voter_id` · `birth_certificate` · `vehicle_bluebook` · `insurance` ·
`medical` · `property` · `academic` · `other`

---

## Seeded Data

After running `php artisan migrate:fresh --seed`:
- **6** Citizen service guides (Passport, PAN, National ID, Driving License, Voter ID, Company Reg)
- **10** Emergency contacts (Police, Ambulance, Fire, etc.)
- **17** Government offices across Nepal
- **6** Citizen news articles
- **20** Driving license quiz questions
- **20** Road signs (mandatory, warning, informatory)

---

## Flutter Integration

Add the base URL to your Flutter app:
```dart
const String baseUrl = 'http://10.0.2.2:8000/api/v1'; // Android emulator
// const String baseUrl = 'http://localhost:8000/api/v1'; // Web
```

> ⚠️ **Disclaimer:** Nagarik+ is an independent platform and is NOT an official Government of Nepal application.
