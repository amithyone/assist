#!/bin/bash
# Run on server after uploading assist-integration admin files
set -e
APP=~/domains/assist.amithyone.com
PACK=~/assist-pack
cd "$PACK" && git pull 2>/dev/null || true
for d in app config database routes resources public; do
  [ -d "$PACK/$d" ] && cp -R "$PACK/$d/." "$APP/$d/" 2>/dev/null || true
done
cd "$APP"
/opt/alt/php84/usr/bin/php artisan route:clear
/opt/alt/php84/usr/bin/php artisan view:clear
echo "Admin UI deployed."
