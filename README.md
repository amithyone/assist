# Assist Laravel Integration

Laravel integration pack for **Assist AI Editor**: API licensing, usage tracking, marketing site (Blade), session auth, admin tools, and a **web setup wizard**.

## Quick start (host Laravel app)

1. Copy this folder’s contents into your Laravel project (see [INTEGRATION.md](INTEGRATION.md)).
2. Register middleware `assist.setup` and `assist.key`.
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
