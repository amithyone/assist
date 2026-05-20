#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PHP=/opt/alt/php84/usr/bin/php
cd "$APP"
tar xzf ~/site-copy-deploy.tar.gz -C "$APP"
$PHP artisan view:clear
$PHP artisan optimize:clear
echo SITE_COPY_OK
