# Deploy Assist on Hostinger (SSH)

You are on: `u429468666@77.37.37.190` — home folder `~/public_html`.

The GitHub repo **is not a full website by itself**. This script builds Laravel + Assist on the server.

## Quick install (copy-paste in SSH)

```bash
cd ~
curl -sL https://raw.githubusercontent.com/amithyone/assist/main/deploy/hostinger-install.sh -o hostinger-install.sh
bash hostinger-install.sh
```

If `curl` fails, clone first:

```bash
cd ~
git clone https://github.com/amithyone/assist.git assist-pack
bash assist-pack/deploy/hostinger-install.sh
```

## After the script

### 1. Register middleware (one-time)

Edit `~/assist-laravel/bootstrap/app.php`. Inside `withMiddleware`, add:

```php
$middleware->alias([
    'assist.key' => \App\Http\Middleware\AssistApiKey::class,
    'assist.setup' => \App\Http\Middleware\AssistSetupGate::class,
]);
```

### 2. User model (one-time)

In `~/assist-laravel/app/Models/User.php`:

```php
use Laravel\Sanctum\HasApiTokens;
use App\Models\Concerns\HasAssistPlan;

class User extends Authenticatable
{
    use HasApiTokens, HasAssistPlan;

    protected $fillable = [
        'name', 'email', 'password',
        'youtube', 'instagram', 'marketing_opt_in',
    ];
}
```

### 3. Database

**hPanel → Databases →** create database, user, password. Note host (often `localhost`).

### 4. Finish setup in browser

Open:

`https://yourdomain.com/assist/setup`

Enter MySQL credentials → **Install & run migrations**.

### 5. Or via SSH

```bash
cd ~/assist-laravel
nano .env   # set DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan migrate --force
php artisan db:seed --class=AssistPlanSeeder --force
```

## Check what’s in public_html now

```bash
cd ~/public_html
ls -la
php -v
which composer
```

## Document root (recommended)

In **hPanel → Websites → Manage → Domains → Document root**, set:

`/home/u429468666/assist-laravel/public`

Then you don’t need symlinks in `public_html`.

## Troubleshooting

| Problem | Fix |
|---------|-----|
| 500 error | `chmod -R 775 storage bootstrap/cache` |
| Composer missing | hPanel → PHP → enable Composer, or install locally |
| `/assist/setup` 404 | Middleware + routes in `routes/web.php` |
| DB connection failed | Use hPanel DB host (often `localhost`), not `127.0.0.1` |
| White page | `APP_DEBUG=true` in `.env`, check `storage/logs/laravel.log` |

## API for desktop app

After install, API lives at:

`https://yourdomain.com/api/assist/...`

Set the same `ASSIST_APP_KEY` in Assist desktop `.env`.
