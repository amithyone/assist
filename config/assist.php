<?php

return [
    'app_key' => env('ASSIST_APP_KEY', ''),
    'upgrade_url' => env('ASSIST_UPGRADE_URL', 'https://example.com/pricing'),
    'default_plan_slug' => env('ASSIST_DEFAULT_PLAN', 'free'),
    'excerpt_max_length' => (int) env('ASSIST_EXCERPT_MAX', 500),
    'activity_sync_max_batch' => 50,
    'app_version' => env('ASSIST_APP_VERSION', '1.0.0'),
    'download_url' => env('ASSIST_DOWNLOAD_URL', '#download'),
    'support_email' => env('ASSIST_SUPPORT_EMAIL', 'support@assist.app'),
    'setup_enabled' => env('ASSIST_SETUP_ENABLED', true),
    'install_lock_file' => '.assist-installed',
];
