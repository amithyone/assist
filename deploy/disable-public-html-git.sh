#!/bin/bash
# Remove git from public_html so Hostinger/GitHub auto-deploy stops breaking the site.
set -euo pipefail
APP="${ASSIST_APP:-$HOME/domains/assist.amithyone.com}"
PUBLIC_HTML="$APP/public_html"

if [ -d "$PUBLIC_HTML/.git" ]; then
  echo "Removing $PUBLIC_HTML/.git (was tracking github.com/amithyone/assist)"
  rm -rf "$PUBLIC_HTML/.git"
  echo "Done. Disable Git deploy to public_html in hPanel so it is not recreated."
else
  echo "No .git in public_html"
fi
