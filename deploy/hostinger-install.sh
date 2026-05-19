#!/bin/bash
# Hostinger shared hosting — install Laravel + Assist integration
# Run from SSH home directory (NOT inside public_html):
#   cd ~
#   bash hostinger-install.sh
#
# Prereqs: PHP 8.2+, Composer (enable in hPanel → Advanced → PHP → Composer)

set -e

APP_DIR="${APP_DIR:-$HOME/assist-laravel}"
PACK_DIR="${PACK_DIR:-$HOME/assist-pack}"
PUBLIC_HTML="${PUBLIC_HTML:-$HOME/public_html}"
REPO="${REPO:-https://github.com/amithyone/assist.git}"

echo "==> Assist Hostinger installer"
echo "    App dir:    $APP_DIR"
echo "    Public:     $PUBLIC_HTML"
echo ""

# --- checks ---
php -v || { echo "ERROR: php not found"; exit 1; }
PHP_MAJOR=$(php -r 'echo PHP_MAJOR_VERSION;')
PHP_MINOR=$(php -r 'echo PHP_MINOR_VERSION;')
if [ "$PHP_MAJOR" -lt 8 ] || { [ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 2 ]; }; then
  echo "ERROR: PHP 8.2+ required (hPanel → PHP Configuration)"
  exit 1
fi

if ! command -v composer >/dev/null 2>&1; then
  echo "ERROR: composer not found. Enable Composer in Hostinger hPanel or run:"
  echo "  curl -sS https://getcomposer.org/installer | php"
  echo "  mv composer.phar ~/bin/composer && chmod +x ~/bin/composer"
  exit 1
fi

# --- clone integration pack ---
if [ ! -d "$PACK_DIR/.git" ]; then
  echo "==> Cloning Assist pack from GitHub..."
  rm -rf "$PACK_DIR"
  git clone "$REPO" "$PACK_DIR"
else
  echo "==> Updating Assist pack..."
  cd "$PACK_DIR" && git pull && cd - >/dev/null
fi

# --- Laravel skeleton ---
if [ ! -f "$APP_DIR/artisan" ]; then
  echo "==> Creating Laravel app (this takes a few minutes)..."
  composer create-project laravel/laravel "$APP_DIR" --no-interaction --prefer-dist
else
  echo "==> Laravel app already exists at $APP_DIR"
fi

cd "$APP_DIR"

# --- merge Assist files ---
echo "==> Merging Assist integration files..."
for dir in app config database routes resources; do
  if [ -d "$PACK_DIR/$dir" ]; then
    cp -R "$PACK_DIR/$dir/." "$APP_DIR/$dir/"
  fi
done
mkdir -p "$APP_DIR/public/assist"
cp -R "$PACK_DIR/public/assist/." "$APP_DIR/public/assist/" 2>/dev/null || true

# --- Sanctum ---
if ! composer show laravel/sanctum >/dev/null 2>&1; then
  echo "==> Installing Sanctum..."
  composer require laravel/sanctum --no-interaction
  php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force 2>/dev/null || true
fi

# --- .env ---
if [ ! -f .env ]; then
  echo "==> Creating .env from Laravel example..."
  cp .env.example .env
  php artisan key:generate --force
fi

# Append Assist vars if missing
grep -q '^ASSIST_APP_KEY=' .env 2>/dev/null || cat >> .env <<'EOF'

# Assist
ASSIST_APP_KEY=
ASSIST_DEFAULT_PLAN=free
ASSIST_DOWNLOAD_URL=#download
ASSIST_SUPPORT_EMAIL=support@assist.app
ASSIST_SETUP_ENABLED=true
EOF

# --- routes (idempotent) ---
if ! grep -q 'assist-setup.php' routes/web.php 2>/dev/null; then
  echo "==> Wiring web routes..."
  cat >> routes/web.php <<'EOF'

// Assist integration
require base_path('routes/assist-setup.php');
require base_path('routes/assist-web.php');
EOF
fi

if ! grep -q 'assist-api.php' routes/api.php 2>/dev/null; then
  cat >> routes/api.php <<'EOF'

require base_path('routes/assist-api.php');
EOF
fi

# --- middleware hint ---
echo ""
echo "==> MANUAL STEP: Register middleware in bootstrap/app.php"
echo '    assist.key  => \App\Http\Middleware\AssistApiKey::class'
echo '    assist.setup => \App\Http\Middleware\AssistSetupGate::class'
echo "    (See INTEGRATION.md or deploy/HOSTINGER.md)"
echo ""

# --- permissions ---
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

# --- link public_html to Laravel public ---
echo "==> Linking public_html → Laravel public folder..."
if [ -d "$PUBLIC_HTML" ]; then
  # backup existing index if any
  if [ -f "$PUBLIC_HTML/index.php" ] && ! grep -q 'assist-laravel' "$PUBLIC_HTML/index.php" 2>/dev/null; then
    mv "$PUBLIC_HTML/index.php" "$PUBLIC_HTML/index.php.bak.$(date +%s)" 2>/dev/null || true
  fi
  rm -f "$PUBLIC_HTML/index.php" "$PUBLIC_HTML/.htaccess" 2>/dev/null || true
  ln -sf "$APP_DIR/public/index.php" "$PUBLIC_HTML/index.php"
  ln -sf "$APP_DIR/public/.htaccess" "$PUBLIC_HTML/.htaccess" 2>/dev/null || true
  ln -sf "$APP_DIR/storage/app/public" "$PUBLIC_HTML/storage" 2>/dev/null || true
fi

echo ""
echo "=============================================="
echo " INSTALLATION FILES READY"
echo "=============================================="
echo ""
echo "1. In Hostinger hPanel → Databases → create MySQL DB + user"
echo "2. Open in browser: https://YOUR-DOMAIN/assist/setup"
echo "   (enters DB credentials + runs migrations)"
echo ""
echo "   OR edit $APP_DIR/.env DB_* then run:"
echo "   cd $APP_DIR && php artisan migrate --force"
echo "   php artisan db:seed --class=AssistPlanSeeder --force"
echo ""
echo "3. hPanel → Advanced → PHP → set version 8.2+ for your domain"
echo "4. Optional: hPanel → Domains → Document root → $APP_DIR/public"
echo "    (instead of symlinks in public_html)"
echo ""
echo "App path: $APP_DIR"
echo "=============================================="
