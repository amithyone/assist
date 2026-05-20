#!/bin/bash
# Hostinger — Laravel app in domain folder, web root = public_html
# Run:  cd ~ && bash assist-pack/deploy/hostinger-install.sh
#
# Layout (amithyone.com):
#   ~/domains/amithyone.com/          ← Laravel app (app/, vendor/, .env, artisan)
#   ~/domains/amithyone.com/public_html/  ← Web root (index.php, assist/, assets)

set -e

PACK_DIR="${PACK_DIR:-$HOME/assist-pack}"
REPO="${REPO:-https://github.com/amithyone/assist.git}"

# Resolve public_html (symlink on Hostinger → domains/…/public_html)
PUBLIC_HTML="${PUBLIC_HTML:-$HOME/public_html}"
PUBLIC_HTML=$(readlink -f "$PUBLIC_HTML" 2>/dev/null || echo "$PUBLIC_HTML")
APP_DIR="${APP_DIR:-$(dirname "$PUBLIC_HTML")}"

find_best_php() {
  local candidates=()
  for p in \
    "$(command -v php84 2>/dev/null)" \
    "$(command -v php83 2>/dev/null)" \
    "$(command -v php82 2>/dev/null)" \
    /opt/alt/php84/usr/bin/php \
    /opt/alt/php83/usr/bin/php \
    /opt/alt/php82/usr/bin/php \
    /usr/local/bin/php8.3 \
    /usr/local/bin/php8.2 \
    "$(command -v php 2>/dev/null)"; do
    [ -x "$p" ] && candidates+=("$p")
  done
  local best="" best_score=0
  for p in "${candidates[@]}"; do
    score=$("$p" -r 'echo PHP_MAJOR_VERSION * 100 + PHP_MINOR_VERSION;' 2>/dev/null || echo 0)
    if [ "$score" -gt "$best_score" ]; then
      best_score=$score
      best=$p
    fi
  done
  echo "$best"
}

PHP_BIN=$(find_best_php)
[ -n "$PHP_BIN" ] || { echo "ERROR: No PHP found"; exit 1; }

PHP_MAJOR=$($PHP_BIN -r 'echo PHP_MAJOR_VERSION;')
PHP_MINOR=$($PHP_BIN -r 'echo PHP_MINOR_VERSION;')
LARAVEL_CONSTRAINT="laravel/laravel"
if [ "$PHP_MAJOR" -lt 8 ] || { [ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 2 ]; }; then
  echo "WARNING: PHP $PHP_MAJOR.$PHP_MINOR — using Laravel 10"
  LARAVEL_CONSTRAINT="laravel/laravel:^10.0"
fi

run_php() { "$PHP_BIN" "$@"; }
run_composer() {
  if [ -f /usr/local/bin/composer ]; then
    COMPOSER_ALLOW_SUPERUSER=1 "$PHP_BIN" /usr/local/bin/composer "$@"
  else
    COMPOSER_ALLOW_SUPERUSER=1 composer "$@"
  fi
}

echo "==> Assist Hostinger installer"
echo "    PHP:         $PHP_BIN ($($PHP_BIN -v | head -1))"
echo "    Laravel:     $LARAVEL_CONSTRAINT"
echo "    App (root):  $APP_DIR"
echo "    Web (public): $PUBLIC_HTML"
echo ""

command -v composer >/dev/null 2>&1 || [ -f /usr/local/bin/composer ] || { echo "ERROR: composer not found"; exit 1; }

# --- Assist pack ---
if [ ! -d "$PACK_DIR/.git" ]; then
  echo "==> Cloning Assist pack..."
  git clone "$REPO" "$PACK_DIR"
else
  echo "==> Updating Assist pack..."
  git -C "$PACK_DIR" pull
fi

# --- Laravel into domain root (sibling of public_html) ---
if [ ! -f "$APP_DIR/artisan" ]; then
  echo "==> Creating Laravel in domain folder (several minutes)..."
  STAGING="$APP_DIR/.laravel-staging-$$"
  rm -rf "$STAGING"
  run_composer create-project "$LARAVEL_CONSTRAINT" "$STAGING" --no-interaction --prefer-dist

  echo "==> Placing app files in $APP_DIR and web files in public_html..."
  shopt -s dotglob nullglob
  for item in "$STAGING"/*; do
    base=$(basename "$item")
    if [ "$base" = "public" ]; then
      mkdir -p "$PUBLIC_HTML"
      cp -R "$item/." "$PUBLIC_HTML/"
    else
      rm -rf "$APP_DIR/$base"
      cp -R "$item" "$APP_DIR/$base"
    fi
  done
  shopt -u dotglob nullglob 2>/dev/null || true
  rm -rf "$STAGING"
else
  echo "==> Laravel already installed at $APP_DIR"
  if [ -d "$APP_DIR/public" ]; then
    echo "==> Refreshing public_html from Laravel public/..."
    cp -R "$APP_DIR/public/." "$PUBLIC_HTML/"
  fi
fi

cd "$APP_DIR"

echo "==> Merging Assist integration..."
for dir in app config database routes resources; do
  [ -d "$PACK_DIR/$dir" ] && cp -R "$PACK_DIR/$dir/." "$APP_DIR/$dir/"
done
mkdir -p "$PUBLIC_HTML/assist"
cp -R "$PACK_DIR/public/assist/." "$PUBLIC_HTML/assist/" 2>/dev/null || true

if ! run_composer show laravel/sanctum >/dev/null 2>&1; then
  echo "==> Installing Sanctum..."
  run_composer require laravel/sanctum --no-interaction
  run_php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --force 2>/dev/null || true
fi

if [ ! -f .env ]; then
  echo "==> Creating .env..."
  cp .env.example .env
  run_php artisan key:generate --force
fi

grep -q '^ASSIST_APP_KEY=' .env 2>/dev/null || cat >> .env <<'EOF'

# Assist
ASSIST_APP_KEY=
ASSIST_DEFAULT_PLAN=free
ASSIST_DOWNLOAD_URL=#download
ASSIST_SUPPORT_EMAIL=support@assist.app
ASSIST_SETUP_ENABLED=true
CHECKOUT_BASE_URL=https://check-outpay.com/api/v1
CHECKOUT_API_KEY=
CHECKOUT_WEBHOOK_URL=
CHECKOUT_DEV_PROGRAM_PARTNER_ID=
EOF

if ! grep -q 'assist-setup.php' routes/web.php 2>/dev/null; then
  echo "==> Wiring routes..."
  cat >> routes/web.php <<'EOF'

// Assist integration
require base_path('routes/assist-setup.php');
require base_path('routes/assist-web.php');
Route::middleware(['auth', 'assist.admin'])->prefix('admin/assist')->group(function () {
    require base_path('routes/assist-admin.php');
});
EOF
fi
if ! grep -q 'assist-api.php' routes/api.php 2>/dev/null; then
  printf '\nrequire base_path(\'routes/assist-api.php\');\n' >> routes/api.php
fi

if [ -f bootstrap/app.php ] && ! grep -q 'AssistSetupGate' bootstrap/app.php 2>/dev/null; then
  echo "==> Registering middleware..."
  run_php -r '
$f = "bootstrap/app.php";
$c = file_get_contents($f);
  $snippet = "\n    \$middleware->alias([\n        \"assist.key\" => \\App\\Http\\Middleware\\AssistApiKey::class,\n        \"assist.setup\" => \\App\\Http\\Middleware\\AssistSetupGate::class,\n        \"assist.admin\" => \\App\\Http\\Middleware\\EnsureAssistAdmin::class,\n    ]);\n";
if (strpos($c, "AssistSetupGate") !== false) exit(0);
if (preg_match("/->withMiddleware\\(\\s*function\\s*\\(\\s*\\\\?Illuminate\\\\Foundation\\\\Configuration\\\\Middleware\\s*\\\$middleware\\s*\\)\\s*\\{/", $c)) {
  $c = preg_replace(
    "/(->withMiddleware\\(\\s*function\\s*\\(\\s*\\\\?Illuminate\\\\Foundation\\\\Configuration\\\\Middleware\\s*\\\$middleware\\s*\\)\\s*\\{)/",
    "$1".$snippet,
    $c,
    1
  );
  file_put_contents($f, $c);
}
' 2>/dev/null || echo "    Add middleware aliases manually in bootstrap/app.php"
fi

if [ -f app/Models/User.php ] && ! grep -q 'HasAssistPlan' app/Models/User.php; then
  run_php -r '
$f = "app/Models/User.php";
$c = file_get_contents($f);
if (strpos($c, "HasAssistPlan") !== false) exit(0);
$c = str_replace("use Illuminate\\Foundation\\Auth\\User as Authenticatable;",
  "use Illuminate\\Foundation\\Auth\\User as Authenticatable;\nuse Laravel\\Sanctum\\HasApiTokens;\nuse App\\Models\\Concerns\\HasAssistPlan;", $c);
$c = preg_replace("/(class User extends Authenticatable\s*\{)\s*(use [^;]+;)?/", "$1\n    use HasApiTokens, HasAssistPlan;", $c, 1);
file_put_contents($f, $c);
' 2>/dev/null || true
fi

chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

# Block web access to sensitive dirs if .htaccess exists in domain root
if [ ! -f "$APP_DIR/.htaccess" ]; then
  cat > "$APP_DIR/.htaccess" <<'EOF'
# Deny web access to Laravel files outside public_html
Require all denied
EOF
fi

echo ""
echo "=============================================="
echo " DONE"
echo "=============================================="
echo "  Laravel app:  $APP_DIR"
echo "  Website root: $PUBLIC_HTML  (public_html)"
echo ""
echo "1. hPanel → PHP 8.2+ for amithyone.com"
echo "2. hPanel → Databases → create MySQL"
echo "3. https://amithyone.com/assist/setup"
echo ""
echo "  cd $APP_DIR"
echo "  $PHP_BIN artisan migrate --force"
echo "=============================================="
