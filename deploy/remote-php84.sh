#!/bin/bash
HT=~/domains/assist.amithyone.com/public_html/.htaccess
if ! grep -q alt-php84 "$HT"; then
  sed -i '1i# Assist: PHP 8.4 required for Laravel 13\nAddHandler application/x-httpd-alt-php84 .php .php8 .phtml\n' "$HT"
fi
cat > ~/domains/assist.amithyone.com/.htaccess << 'EOF'
Require all denied
EOF
curl -sI https://assist.amithyone.com/assist/setup | head -8
echo "---"
curl -s https://assist.amithyone.com/assist/setup 2>&1 | head -8
