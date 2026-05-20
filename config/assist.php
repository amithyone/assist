<?php

return [
    'site_name' => env('ASSIST_SITE_NAME', 'Assist'),
    'site_twitter_handle' => env('ASSIST_TWITTER_HANDLE', ''),
    'default_og_image' => env('ASSIST_DEFAULT_OG_IMAGE', 'assist/assist-logo.png'),

    'site_media' => [
        'disk' => 'public',
        'path' => 'assist/site',
        'public_subdir' => 'assist/site',
        'max_upload_kb' => (int) env('ASSIST_SITE_IMAGE_MAX_KB', 5120),
    ],

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
        /** Shown on the site when mac_arm64 is the only published build. */
        'mac_availability_notice' => env(
            'ASSIST_MAC_AVAILABILITY_NOTICE',
            'Currently available for Apple Silicon Mac (arm64). Intel Mac, Windows, and Linux builds appear here when uploaded.'
        ),
        'platforms' => [
            'mac_arm64' => [
                'label' => 'Mac (Apple Silicon)',
                'short_label' => 'Apple Silicon Mac',
                'description' => 'Native build for M-series Macs. Requires DaVinci Resolve on macOS.',
                'extensions' => ['dmg', 'zip'],
                'sort' => 1,
            ],
            'mac_x86_64' => [
                'label' => 'Mac (Intel / Rosetta)',
                'short_label' => 'Intel Mac',
                'description' => 'For Intel Macs or Resolve running under Rosetta.',
                'extensions' => ['dmg', 'zip'],
                'sort' => 2,
            ],
            'windows' => [
                'label' => 'Windows',
                'short_label' => 'Windows',
                'description' => 'Windows installer (when available).',
                'extensions' => ['exe', 'msi', 'zip'],
                'sort' => 3,
            ],
            'linux' => [
                'label' => 'Linux',
                'short_label' => 'Linux',
                'description' => 'Linux package (when available).',
                'extensions' => ['AppImage', 'deb', 'tar.gz', 'zip'],
                'sort' => 4,
            ],
        ],
    ],
    'support_email' => env('ASSIST_SUPPORT_EMAIL', 'support@assist.app'),
    'legal_email' => env('ASSIST_LEGAL_EMAIL', 'legal@amithyone.com'),
    'company_name' => env('ASSIST_COMPANY_NAME', 'Amithyone Media'),
    'company_owner' => env('ASSIST_COMPANY_OWNER', 'Amithy Innocent'),
    'setup_enabled' => env('ASSIST_SETUP_ENABLED', true),
    'install_lock_file' => '.assist-installed',

    'payment' => [
        'default_gateway' => env('PAYMENT_GATEWAY', 'checkoutpay'),
        /** Comma-separated: checkoutpay,paystack — which gateways are offered at checkout */
        'enabled_gateways' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('PAYMENT_GATEWAYS_ENABLED', 'checkoutpay,paystack'))
        ))),
        'gateways' => [
            'checkoutpay' => [
                'label' => 'CheckoutPay',
                'description' => 'Bank transfer',
                'currencies' => ['ngn', 'usd'],
            ],
            'paystack' => [
                'label' => 'Paystack',
                'description' => 'Card, bank, USSD',
                'currencies' => ['ngn'],
            ],
        ],
    ],

    'checkout' => [
        'base_url' => env('CHECKOUT_BASE_URL', 'https://check-outpay.com/api/v1'),
        'api_key' => env('CHECKOUT_API_KEY', ''),
        'webhook_url' => env('CHECKOUT_WEBHOOK_URL', ''),
        'dev_program_partner_id' => env('CHECKOUT_DEV_PROGRAM_PARTNER_ID'),
    ],

    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY', ''),
        'secret_key' => env('PAYSTACK_SECRET_KEY', ''),
        'webhook_url' => env('PAYSTACK_WEBHOOK_URL', ''),
    ],
];
