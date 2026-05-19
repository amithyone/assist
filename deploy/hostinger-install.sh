#!/bin/bash
# Hostinger shared hosting — install Laravel + Assist integration
# Run from SSH home:  cd ~ && bash assist-pack/deploy/hostinger-install.sh

set -e

APP_DIR="${APP_DIR:-$HOME/assist-laravel}"
PACK_DIR="${PACK_DIR:-$HOME/assist-pack}"
PUBLIC_HTML="${PUBLIC_HTML:-$HOME/public_html}"
REPO="${REPO:-https://github.com/amithyone/assist.git}"

# --- pick best PHP on Hostinger (CLI default is often 8.1) ---
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
if [ -z "$PHP_BIN" ]; then
  echo "ERROR: No PHP binary found"
  exit 1
fi

PHP_MAJOR=$($PHP_BIN -r 'echo PHP_MAJOR_VERSION;')
PHP_MINOR=$($PHP_BIN -r 'echo PHP_MINOR_VERSION;')
LARAVEL_CONSTRAINT="laravel/laravel"
if [ "$PHP_MAJOR" -lt 8 ] || { [ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 2 ]; }; then
  echo "WARNING: PHP $PHP_MAJOR.$PHP_MINOR — using Laravel 10 (upgrade to PHP 8.2+ in hPanel when possible)"
  LARAVEL_CONSTRAINT="laravel/laravel:^10.0"
fi

export PHP_BIN
run_php() { "$PHP_BIN" "$@"; }
run_composer() {
  if [ -f /usr/local/bin/composer ]; then
    COMPOSER_ALLOW_SUPERUSER=1 "$PHP_BIN" /usr/local/bin/composer "$@"
  else
    COMPOSER_ALLOW_SUPERUSER=1 composer "$@"
  fi
}

echo "==> Assist Hostinger installer"
echo "    PHP:        $PHP_BIN ($($PHP_BIN -v | head -1))"
echo "    Laravel:    $LARAVEL_CONSTRAINT"
echo "    App dir:    $APP_DIR"
echo "    Public:     $PUBLIC_HTML"
echo ""

if ! command -v composer >/dev/null 2>&1 && [ ! -f /usr/local/bin/composer ]; then
  echo "ERROR: composer not found"
  exit 1
fi

# --- clone integration pack ---
if [ ! -d "$PACK_DIR/.git" ]; then
  echo "==> Cloning Assist pack..."
  git clone "$REPO" "$PACK_DIR"
else
  echo "==> Updating Assist pack..."
  cd "$PACK_DIR" && git pull && cd - >/dev/null
fi

# --- Laravel skeleton ---
if [ ! -f "$APP_DIR/artisan" ]; then
  echo "==> Creating Laravel (several minutes)..."
  run_composer create-project "$LARAVEL_CONSTRAINT" "$APP_DIR" --no-interaction --prefer-dist
else
  echo "==> Laravel already at $APP_DIR"
fi

cd "$APP_DIR"

echo "==> Merging Assist files..."
for dir in app config database routes resources; do
  [ -d "$PACK_DIR/$dir" ] && cp -R "$PACK_DIR/$dir/." "$APP_DIR/$dir/"
done
mkdir -p "$APP_DIR/public/assist"
cp -R "$PACK_DIR/public/assist/." "$APP_DIR/public/assist/" 2>/dev/null || true

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
EOF

# --- routes ---
if ! grep -q 'assist-setup.php' routes/web.php 2>/dev/null; then
  echo "==> Wiring routes..."
  cat >> routes/web.php <<'EOF'

// Assist integration
require base_path('routes/assist-setup.php');
require base_path('routes/assist-web.php');
EOF
fi
if ! grep -q 'assist-api.php' routes/api.php 2>/dev/null; then
  printf '\nrequire base_path(\'routes/assist-api.php\');\n' >> routes/api.php
fi

# --- middleware (Laravel 11+) ---
if [ -f bootstrap/app.php ] && ! grep -q 'AssistSetupGate' bootstrap/app.php 2>/dev/null; then
  echo "==> Registering middleware in bootstrap/app.php..."
  if grep -q '->withMiddleware' bootstrap/app.php; then
  run_php -r '
$f = "bootstrap/app.php";
$c = file_get_contents($f);
$snippet = "\n    \$middleware->alias([\n        \"assist.key\" => \\App\\Http\\Middleware\\AssistApiKey::class,\n        \"assist.setup\" => \\App\\Http\\Middleware\\AssistSetupGate::class,\n    ]);\n";
if (strpos($c, "AssistSetupGate") !== false) exit(0);
if (preg_match("/->withMiddleware\\(\\s*function\\s*\\(\\s*\\\\?Illuminate\\\\Foundation\\\\Configuration\\\\Middleware\\s*\\\$middleware\\s*\\)\\s*\\{/", $c)) {
  $c = preg_replace(
    "/(->withMiddleware\\(\\s*function\\s*\\(\\s*\\\\?Illuminate\\\\Foundation\\\\Configuration\\\\Middleware\\s*\\\$middleware\\s*\\)\\s*\\{)/",
    "$1".$snippet,
    $c,
    1
  );
  file_put_contents($f, $c);
  echo "middleware registered\n";
}
'
  fi
fi

# --- User model helpers ---
USER_MODEL="app/Models/User.php"
if [ -f "$USER_MODEL" ] && ! grep -q 'HasAssistPlan' "$USER_MODEL"; then
  echo "==> Patching User model (add traits manually if this step prints a warning)..."
  run_php -r '
$f = "app/Models/User.php";
$c = file_get_contents($f);
if (strpos($c, "HasAssistPlan") !== false) exit(0);
$c = str_replace("use Illuminate\\Foundation\\Auth\\User as Authenticatable;",
  "use Illuminate\\Foundation\\Auth\\User as Authenticatable;\nuse Laravel\\Sanctum\\HasApiTokens;\nuse App\\Models\\Concerns\\HasAssistPlan;", $c);
$c = preg_replace("/(class User extends Authenticatable\s*\{)\s*(use [^;]+;)?/", "$1\n    use HasApiTokens, HasAssistPlan;", $c, 1);
file_put_contents($f, $c);
' 2>/dev/null || echo "    Edit app/Models/User.php — add HasApiTokens, HasAssistPlan"
fi

chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "==> Linking public_html..."
REAL_PUBLIC=$(readlink -f "$PUBLIC_HTML" 2>/dev/null || echo "$PUBLIC_HTML")
if [ -d "$REAL_PUBLIC" ]; then
  [ -f "$REAL_PUBLIC/index.php" ] && ! grep -q 'assist-laravel' "$REAL_PUBLIC/index.php" 2>/dev/null && \
    mv "$REAL_PUBLIC/index.php" "$REAL_PUBLIC/index.php.bak.$(date +%s)" 2>/dev/null || true
  rm -f "$REAL_PUBLIC/index.php" "$REAL_PUBLIC/.htaccess" 2>/dev/null || true
  ln -sf "$APP_DIR/public/index.php" "$REAL_PUBLIC/index.php"
  ln -sf "$APP_DIR/public/.htaccess" "$REAL_PUBLIC/.htaccess" 2>/dev/null || true
fi

echo ""
echo "=============================================="
echo " DONE — next steps"
echo "=============================================="
echo "1. hPanel → PHP → set site to PHP 8.2 or 8.3 (recommended)"
echo "2. hPanel → Databases → create MySQL DB"
echo "3. Browser: https://amithyone.com/assist/setup"
echo "   OR: cd $APP_DIR && $PHP_BIN artisan migrate --force"
echo ""
echo "PHP for artisan: $PHP_BIN"
echo "=============================================="
