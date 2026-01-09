# ActionTrack - Activity Management System

## Quick Start (Local Development)

```bash
cd /home/lategan/manycents/actions

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
php artisan serve --host=0.0.0.0 --port=8000
```

**Local URL:** http://localhost:8000

## Overview

ActionTrack is a Laravel-based activity and task management system with:
- Multi-user support (each user has their own data)
- Activity tracking with due dates and urgency levels
- Contact (People) management
- Email notifications (daily summaries, broadcasts)
- Dashboard with overdue/due-soon alerts

## Tech Stack

- **Backend:** PHP 8.1+, Laravel 10
- **Database:** MySQL (production), SQLite (dev optional)
- **Frontend:** Blade templates, vanilla CSS, vanilla JavaScript
- **Email:** SMTP (Afrihost: mail.manycents.co.za)

## Project Structure

```
actions/
├── app/
│   ├── Console/Commands/       # Artisan commands
│   ├── Http/
│   │   ├── Controllers/        # Request handlers
│   │   └── Requests/           # Form validation
│   ├── Mail/                   # Email classes
│   └── Models/                 # Eloquent models
├── config/                     # Configuration files
├── database/migrations/        # Database schema
├── public/                     # Web root
│   ├── css/app.css
│   └── js/app.js
├── resources/views/            # Blade templates
│   ├── activities/
│   ├── auth/
│   ├── emails/
│   ├── layouts/
│   ├── people/
│   └── settings/
└── routes/
    └── web.php                 # Application routes
```

## Key Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/` | Dashboard |
| GET | `/activities` | List activities |
| GET | `/activities/create` | New activity form |
| POST | `/activities` | Create activity |
| GET | `/activities/{id}/edit` | Edit activity form |
| PUT | `/activities/{id}` | Update activity |
| DELETE | `/activities/{id}` | Delete activity |
| GET | `/people` | List contacts |
| POST | `/people/broadcast` | Send broadcast email |
| GET | `/settings` | User settings |

## Database Models

### Activity
- `name`, `logic`, `next_step`, `start_date`, `due_date`
- `status`: in_progress, completed, cancelled
- `lead_id` → Person (lead)
- `parties` → Many-to-many with Person

### Person
- `first_name`, `last_name`, `email_primary`, `email_secondary`
- `phone_primary`, `phone_secondary`, `company`

### User
- Standard Laravel auth fields
- Has many Activities and People
- Has one UserSetting

## Artisan Commands

```bash
# Send daily summaries (admin + users + people)
php artisan activities:send-summaries

# Dry run (see what would be sent)
php artisan activities:send-summaries --dry-run

# Send to specific user only
php artisan activities:send-summaries --user=1

# Skip specific recipients
php artisan activities:send-summaries --skip-admin    # Skip admin master summary
php artisan activities:send-summaries --skip-users    # Skip user summaries
php artisan activities:send-summaries --skip-people   # Skip people/contact summaries
```

### Daily Summary Recipients
The daily summary command sends to three groups:
1. **Admin** (ADMIN_EMAIL) - Master summary of all activities across all users
2. **Users** - Each user with `daily_summary_enabled=true` gets their own activities
3. **People** - Each contact involved in activities (as lead or party) gets notified

## Environment Variables

```env
# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=actiontrack
DB_USERNAME=root
DB_PASSWORD=

# Email (Afrihost)
MAIL_HOST=mail.manycents.co.za
MAIL_PORT=465
MAIL_USERNAME=office@manycents.co.za
MAIL_PASSWORD=
MAIL_ENCRYPTION=ssl
```

## Multi-User Architecture

- All models use global scopes to filter by `user_id`
- Activities and People are automatically scoped to the authenticated user
- Users cannot see or modify other users' data
- Each user has their own settings for email preferences

## Deployment

See `DEPLOYMENT.md` for full Afrihost deployment instructions.

**Production URL:** https://actions.manycents.co.za
