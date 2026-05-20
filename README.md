# Assist Laravel Integration

Laravel integration pack for **Assist AI Editor**: API licensing, usage tracking, marketing site (Blade), session auth, admin tools, and a **web setup wizard**.

> **Important:** This repo is **not** a runnable Laravel app. There is no `composer.json` here.
> `composer install` in this folder will **not** create `.env` or start a site.
> You must use a **separate Laravel project** and copy these files into it (see below).

## Quick start (host Laravel app)

1. Copy this folder’s contents into your Laravel project (see [INTEGRATION.md](INTEGRATION.md)).
2. Register middleware `assist.setup`, `assist.key`, and `assist.admin`.
3. In `routes/web.php`:
   ```php
   require base_path('routes/assist-setup.php');
   require base_path('routes/assist-web.php');
   ```
4. Deploy, then open **`/assist/setup`** to enter database credentials and run migrations.
5. Use **`/admin/assist/system`** (authenticated admin) to run migrations again later.

## Repository

```bash
git clone <your-remote> assist-integration
```

See [INTEGRATION.md](INTEGRATION.md) for full install, API, Vite, and mail configuration.

## `.env` not created after `composer`?

| Command | Creates `.env`? |
|---------|------------------|
| `composer install` | **No** (never, in any Laravel project) |
| `cp .env.example .env` | **Yes** (run in your **Laravel** project root) |
| `php artisan key:generate` | Fills `APP_KEY` (needs `.env` to exist first) |
| Visit `/assist/setup` | **Yes** — writes DB + Assist vars (needs full Laravel + routes wired) |

### If you only cloned this repo

```bash
# 1. Create a real Laravel app
composer create-project laravel/laravel assist-site
cd assist-site

# 2. Copy this integration pack INTO assist-site (merge app/, routes/, config/, etc.)

# 3. Create .env in assist-site (not in the clone folder alone)
cp .env.example .env
php artisan key:generate

# 4. Install Sanctum + wire routes (see INTEGRATION.md)
composer require laravel/sanctum
php artisan migrate

# 5. Either edit DB_* in .env, OR open http://localhost:8000/assist/setup
```
