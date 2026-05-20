# Assist Laravel Integration

Copy files from this folder into your Laravel project.

## 1. Install Sanctum

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

Add to `app/Models/User.php`:

```php
use Laravel\Sanctum\HasApiTokens;
use App\Models\Concerns\HasAssistPlan;

class User extends Authenticatable
{
    use HasApiTokens, HasAssistPlan;
    // add fillable: youtube, instagram, marketing_opt_in
}
```

## 2. Copy files

| From | To |
|------|-----|
| `config/assist.php` | `config/assist.php` |
| `database/migrations/*` | `database/migrations/` |
| `database/seeders/AssistPlanSeeder.php` | `database/seeders/` |
| `app/Models/Plan.php` etc. | `app/Models/` |
| `app/Services/*` | `app/Services/` |
| `app/Http/Controllers/Api/Assist/*` | same path |
| `app/Http/Controllers/Web/*` | same path |
| `app/Http/Controllers/Admin/*` | same path |
| `app/Http/Middleware/AssistApiKey.php` | same path |
| `resources/views/admin/assist-activity/` | same path |

Register middleware in `bootstrap/app.php` or `app/Http/Kernel.php`:

```php
'assist.key' => \App\Http\Middleware\AssistApiKey::class,
'assist.setup' => \App\Http\Middleware\AssistSetupGate::class,
'assist.admin' => \App\Http\Middleware\EnsureAssistAdmin::class,
```

Copy installer services:

| From | To |
|------|-----|
| `app/Services/EnvWriter.php` | same path |
| `app/Services/AssistInstallerService.php` | same path |
| `app/Http/Middleware/AssistSetupGate.php` | same path |
| `app/Http/Controllers/Setup/*` | same path |
| `resources/views/setup/` | same path |

## 3. First-time setup wizard

In `routes/web.php` (load **before** other assist routes):

```php
require base_path('routes/assist-setup.php');
```

Visit **`/assist/setup`** on a fresh server to:

1. Check PHP requirements (+ optional **Composer install** if `vendor/` missing)  
2. Enter MySQL credentials (test connection)  
3. Configure SMTP mail settings  
4. Configure CheckoutPay (`CHECKOUT_API_KEY`, webhook URL)  
5. Create the first **admin** account  
6. Save `.env`, generate `APP_KEY`, run migrations + `AssistPlanSeeder`, lock install

Disable the public wizard after go-live:

```env
ASSIST_SETUP_ENABLED=false
```

**Prerequisite:** run Laravel’s default migrations first (`users`, `password_reset_tokens`, Sanctum) — Assist migrations add plan/usage tables and alter `users`.

## 4. Routes

In `routes/api.php`:

```php
require base_path('routes/assist-api.php');
```

Or copy the `Route::prefix('assist')` block from `routes/assist-api.php`.

In `routes/web.php` (admin):

```php
Route::middleware(['auth', 'assist.admin'])->prefix('admin/assist')->group(function () {
    require base_path('routes/assist-admin.php');
});
```

Admin pages: **Overview** (`/admin/assist`), **Users** (`/admin/assist/users`), **App download** (`/admin/assist/downloads` — upload `.dmg`/`.zip`), **Activity**, **System** (migrations/seed without SSH).

Public download URL after upload: `GET /download/assist` (also copied to `public/assist/downloads/` for static serving).

## Plans (seeded)

| Slug | Price | Usage period | Highlights |
|------|-------|--------------|------------|
| `free` | ₦0 / $0 | weekly | 1× reel clone, beat edit, music video cut, AI edit per week |
| `pro` | ₦5,000 / $5 mo | monthly | Unlimited preproduction; 10 clones, 10 beat edits, 2 music video, 5 AI edits |
| `unlimited` | ₦30,000 / $30 mo | monthly | Unlimited everything |

## CheckoutPay billing

Env vars: `CHECKOUT_BASE_URL`, `CHECKOUT_API_KEY`, `CHECKOUT_WEBHOOK_URL`, optional `CHECKOUT_DEV_PROGRAM_PARTNER_ID`.

- User upgrades from `/pricing` → `GET /billing/upgrade/{plan}?currency=ngn|usd`
- Webhook: `POST /webhooks/checkoutpay` (exempt from CSRF in host app, e.g. `$middleware->validateCsrfTokens(except: ['webhooks/checkoutpay'])`)
- Approve your webhook URL domain in the CheckoutPay dashboard before going live

Add to `app/Models/User.php` `$fillable`: `is_admin`, `billing_currency`, `youtube`, `instagram`, `marketing_opt_in`.

## 5. Migrate and seed

```bash
php artisan migrate
php artisan db:seed --class=AssistPlanSeeder
```

**Local test account** (matches the desktop app dev login — remove before production):

```bash
php artisan db:seed --class=AssistTestUserSeeder
```

| Field | Value |
|-------|--------|
| Email | `test@assist.app` |
| Password | `assist123` |

Disable desktop test login in production builds: `ASSIST_DEV_AUTH=0`.

## 6. Public web site (Blade)

Copy marketing and auth views:

| From | To |
|------|-----|
| `routes/assist-web.php` | `routes/assist-web.php` |
| `app/Http/Controllers/Web/*` | same path |
| `resources/views/layouts/assist.blade.php` | same path |
| `resources/views/components/assist/` | same path |
| `resources/views/assist/` | same path |
| `resources/css/assist.css` | same path |
| `resources/js/assist.js` | same path |
| `public/assist/` | `public/assist/` |

In `routes/web.php`:

```php
require base_path('routes/assist-web.php');
```

Pages: `/` (home), `/pricing`, `/docs`, `/login`, `/register`, `/forgot-password`, `/reset-password/{token}`, `/dashboard` (auth), `POST /logout`.

Session auth is separate from Sanctum API tokens used by the desktop app.

### Vite (optional but recommended)

```bash
npm install -D tailwindcss @tailwindcss/vite laravel-vite-plugin vite
```

Merge `vite.config.example.js` into your `vite.config.js` (add `assist.css` and `assist.js` to `laravel-vite-plugin` input).

```bash
npm run build
```

Without a Vite build, the layout falls back to `public/assist/assist-site.css` (standalone, no npm required).

### Password reset email

Requires the default `password_reset_tokens` migration and mail configured:

```env
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=noreply@yoursite.com
MAIL_FROM_NAME="Assist"
```

Use `php artisan vendor:publish --tag=laravel-notifications` if you need to customize the reset notification view.

## 7. Environment

```env
ASSIST_APP_KEY=your-random-secret-shared-with-desktop-app
ASSIST_UPGRADE_URL=https://yoursite.com/pricing
ASSIST_DEFAULT_PLAN=free
ASSIST_DOWNLOAD_URL=https://yoursite.com/download   # or #download anchor
ASSIST_SUPPORT_EMAIL=support@yoursite.com
ASSIST_SETUP_ENABLED=true
```

## 8. Assist desktop app

Set on the Mac running Assist:

```env
ASSIST_API_BASE_URL=https://yoursite.com
ASSIST_APP_KEY=same-as-laravel
ASSIST_UPGRADE_URL=https://yoursite.com/pricing
```

API base URL must not include trailing slash. Endpoints live at `{BASE}/api/assist/...`.

## API summary

| Method | Path | Auth |
|--------|------|------|
| POST | `/api/assist/register` | No |
| POST | `/api/assist/login` | No |
| POST | `/api/assist/logout` | Sanctum |
| GET | `/api/assist/me` | Sanctum |
| GET | `/api/assist/limits` | Sanctum |
| POST | `/api/assist/usage/check` | Sanctum |
| POST | `/api/assist/usage/record` | Sanctum |
| GET | `/api/assist/activity` | Sanctum |
| POST | `/api/assist/activity/sync` | Sanctum |

Header: `X-Assist-App-Key: {ASSIST_APP_KEY}`  
Header: `Authorization: Bearer {token}`

## Web routes (after `require routes/assist-web.php`)

| Method | Path | Name |
|--------|------|------|
| GET | `/` | `assist.home` |
| GET | `/pricing` | `assist.pricing` |
| GET | `/docs` | `assist.docs` |
| GET/POST | `/login` | `assist.login` |
| GET/POST | `/register` | `assist.register` |
| GET/POST | `/forgot-password` | `assist.password.request` / `assist.password.email` |
| GET/POST | `/reset-password/{token}` | `assist.password.reset` / `assist.password.update` |
| GET | `/dashboard` | `assist.dashboard` |
| POST | `/logout` | `assist.logout` |
| GET | `/assist/setup` | `assist.setup.index` |
| POST | `/assist/setup/install` | `assist.setup.install` |
| GET | `/admin/assist/system` | `admin.assist.system` |
| POST | `/admin/assist/system/migrate` | `admin.assist.system.migrate` |

Verify: `php artisan route:list --name=assist`
