#!/bin/bash
# Install Assist on assist.amithyone.com
set -e

DOMAIN=assist.amithyone.com
APP_DIR="$HOME/domains/$DOMAIN"
PUBLIC_HTML="$APP_DIR/public_html"
PACK_DIR="${PACK_DIR:-$HOME/assist-pack}"

export APP_DIR PUBLIC_HTML PACK_DIR

echo "==> Domain: $DOMAIN"
echo "    APP_DIR:     $APP_DIR"
echo "    PUBLIC_HTML: $PUBLIC_HTML"

# public_html currently has the git pack (wrong) — back it up, start clean web root
if [ ! -f "$APP_DIR/artisan" ] && [ -d "$PUBLIC_HTML" ]; then
  BACKUP="$APP_DIR/public_html.pack-backup-$(date +%Y%m%d%H%M%S)"
  echo "==> Backing up old public_html to $(basename "$BACKUP")"
  mv "$PUBLIC_HTML" "$BACKUP"
  mkdir -p "$PUBLIC_HTML"
fi

# Update pack
if [ -d "$PACK_DIR/.git" ]; then
  git -C "$PACK_DIR" pull
else
  git clone https://github.com/amithyone/assist.git "$PACK_DIR"
fi

# Run official installer (uses APP_DIR / PUBLIC_HTML env)
bash "$PACK_DIR/deploy/hostinger-install.sh"

# Laravel 10: middleware in Kernel.php (not bootstrap/app.php)
KERNEL="$APP_DIR/app/Http/Kernel.php"
if [ -f "$KERNEL" ] && ! grep -q 'assist.key' "$KERNEL"; then
  sed -i.bak "/'verified' =>/a\\
        'assist.key' => \\\\App\\\\Http\\\\Middleware\\\\AssistApiKey::class,\\
        'assist.setup' => \\\\App\\\\Http\\\\Middleware\\\\AssistSetupGate::class,\\
        'assist.admin' => \\\\App\\\\Http\\\\Middleware\\\\EnsureAssistAdmin::class," "$KERNEL"
  echo "==> Kernel middleware registered"
fi

CSRF="$APP_DIR/app/Http/Middleware/VerifyCsrfToken.php"
if [ -f "$CSRF" ] && ! grep -q checkoutpay "$CSRF"; then
  sed -i.bak "s/protected \\\$except = \[\]/protected \\\$except = ['webhooks\/checkoutpay']/" "$CSRF" 2>/dev/null || true
fi

# Fix index.php paths if Laravel is in parent of public_html
INDEX="$PUBLIC_HTML/index.php"
if [ -f "$INDEX" ]; then
  sed -i.bak "s|__DIR__.'/../|__DIR__.'/../|g" "$INDEX" || true
  # Laravel public index uses ../ for bootstrap - correct when app is parent of public_html
fi

cd "$APP_DIR"
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo ""
echo "=============================================="
echo " assist.amithyone.com ready for setup"
echo "=============================================="
echo "  https://assist.amithyone.com/assist/setup"
echo "  App: $APP_DIR"
ls -la "$APP_DIR/artisan" "$APP_DIR/vendor/autoload.php" 2>/dev/null || echo "WARN: artisan/vendor missing"
ls -la "$PUBLIC_HTML/index.php" "$PUBLIC_HTML/assist" 2>/dev/null | head -5
