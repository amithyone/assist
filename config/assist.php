<?php

return [
    'app_key' => env('ASSIST_APP_KEY', ''),
    'upgrade_url' => env('ASSIST_UPGRADE_URL', 'https://example.com/pricing'),
    'default_plan_slug' => env('ASSIST_DEFAULT_PLAN', 'free'),
    'excerpt_max_length' => (int) env('ASSIST_EXCERPT_MAX', 500),
    'activity_sync_max_batch' => 50,
    'app_version' => env('ASSIST_APP_VERSION', '1.0.0'),
    'download_url' => env('ASSIST_DOWNLOAD_URL', '#download'),

    'release' => [
        'storage_dir' => 'assist-releases',
        'public_subdir' => 'assist/downloads',
        'max_upload_mb' => (int) env('ASSIST_RELEASE_MAX_MB', 500),
        'max_upload_kb' => (int) env('ASSIST_RELEASE_MAX_MB', 500) * 1024,
    ],
    'support_email' => env('ASSIST_SUPPORT_EMAIL', 'support@assist.app'),
    'setup_enabled' => env('ASSIST_SETUP_ENABLED', true),
    'install_lock_file' => '.assist-installed',

    'checkout' => [
        'base_url' => env('CHECKOUT_BASE_URL', 'https://check-outpay.com/api/v1'),
        'api_key' => env('CHECKOUT_API_KEY', ''),
        'webhook_url' => env('CHECKOUT_WEBHOOK_URL', ''),
        'dev_program_partner_id' => env('CHECKOUT_DEV_PROGRAM_PARTNER_ID'),
    ],
];
