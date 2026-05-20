#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PHP=/opt/alt/php84/usr/bin/php
cd "$APP"
tar xzf ~/cms-seo-deploy.tar.gz -C "$APP"
$PHP artisan migrate --force
$PHP artisan db:seed --class=SitePageSeeder --force
$PHP artisan storage:link 2>/dev/null || true
$PHP artisan optimize:clear
echo CMS_SEO_OK
