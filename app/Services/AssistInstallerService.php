<?php

namespace App\Services;

use Database\Seeders\AssistAdminSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PDO;
use RuntimeException;
use Throwable;

class AssistInstallerService
{
    public function __construct(
        protected EnvWriter $envWriter
    ) {}

    public function lockFilePath(): string
    {
        return storage_path('app/'.config('assist.install_lock_file', '.assist-installed'));
    }

    public function isInstalled(): bool
    {
        if (is_file($this->lockFilePath())) {
            return true;
        }

        try {
            return DB::connection()->getSchemaBuilder()->hasTable('plans');
        } catch (Throwable) {
            return false;
        }
    }

    public function vendorPresent(): bool
    {
        return is_file(base_path('vendor/autoload.php'));
    }

    /**
     * @param  array{host?:string,port?:string,database?:string,username?:string,password?:string|null}  $config
     */
    public function testDatabaseConnection(array $config): array
    {
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? '3306';
        $database = $config['database'] ?? '';
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';

        if ($database === '' || $username === '') {
            return ['ok' => false, 'message' => 'Database name and username are required.'];
        }

        try {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $pdo->query('SELECT 1');

            return ['ok' => true, 'message' => 'Database connection successful.'];
        } catch (Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param  array<string, string|null>  $db
     * @param  array<string, string|null>  $app
     */
    public function saveEnvironment(array $db, array $app = []): void
    {
        $this->envWriter->setMany([
            'DB_CONNECTION' => $db['connection'] ?? 'mysql',
            'DB_HOST' => $db['host'] ?? '127.0.0.1',
            'DB_PORT' => $db['port'] ?? '3306',
            'DB_DATABASE' => $db['database'] ?? '',
            'DB_USERNAME' => $db['username'] ?? '',
            'DB_PASSWORD' => $db['password'] ?? '',
            'APP_URL' => $app['url'] ?? config('app.url'),
            'ASSIST_APP_KEY' => $app['assist_app_key'] ?? config('assist.app_key'),
            'ASSIST_DOWNLOAD_URL' => $app['download_url'] ?? config('assist.download_url'),
            'ASSIST_SUPPORT_EMAIL' => $app['support_email'] ?? config('assist.support_email'),
            'ASSIST_UPGRADE_URL' => $app['upgrade_url'] ?? (rtrim($app['url'] ?? config('app.url'), '/').'/pricing'),
        ]);
    }

    /**
     * @param  array<string, string|null>  $mail
     */
    public function saveMailEnvironment(array $mail): void
    {
        $this->envWriter->setMany([
            'MAIL_MAILER' => $mail['mailer'] ?? 'smtp',
            'MAIL_HOST' => $mail['host'] ?? '',
            'MAIL_PORT' => $mail['port'] ?? '587',
            'MAIL_USERNAME' => $mail['username'] ?? '',
            'MAIL_PASSWORD' => $mail['password'] ?? '',
            'MAIL_ENCRYPTION' => $mail['encryption'] ?? 'tls',
            'MAIL_FROM_ADDRESS' => $mail['from_address'] ?? 'noreply@example.com',
            'MAIL_FROM_NAME' => $mail['from_name'] ?? 'Assist',
        ]);
    }

    /**
     * @param  array<string, string|null>  $checkout
     */
    public function saveCheckoutEnvironment(array $checkout): void
    {
        $this->envWriter->setMany([
            'CHECKOUT_BASE_URL' => $checkout['base_url'] ?? 'https://check-outpay.com/api/v1',
            'CHECKOUT_API_KEY' => $checkout['api_key'] ?? '',
            'CHECKOUT_WEBHOOK_URL' => $checkout['webhook_url'] ?? '',
            'CHECKOUT_DEV_PROGRAM_PARTNER_ID' => $checkout['dev_program_partner_id'] ?? '',
        ]);
    }

    public function refreshConfig(): void
    {
        Artisan::call('config:clear');
    }

    public function runComposerInstall(): array
    {
        if ($this->vendorPresent()) {
            return ['ok' => true, 'message' => 'Composer dependencies already installed.', 'output' => ''];
        }

        if (! function_exists('proc_open')) {
            return [
                'ok' => false,
                'message' => 'proc_open is disabled. Run composer via SSH (deploy/hostinger-install.sh) or upload a pre-built vendor/ folder.',
                'output' => '',
            ];
        }

        $composer = $this->findComposerBinary();
        if (! $composer) {
            return [
                'ok' => false,
                'message' => 'Composer binary not found on server.',
                'output' => '',
            ];
        }

        $cmd = escapeshellcmd($composer).' install --no-dev --optimize-autoloader --no-interaction 2>&1';
        $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = proc_open($cmd, $descriptors, $pipes, base_path());
        if (! is_resource($process)) {
            return ['ok' => false, 'message' => 'Failed to start composer process.', 'output' => ''];
        }

        $output = stream_get_contents($pipes[1]).stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $code = proc_close($process);

        if ($code !== 0 || ! $this->vendorPresent()) {
            return [
                'ok' => false,
                'message' => 'Composer install failed. See output or use SSH installer.',
                'output' => $output,
            ];
        }

        return ['ok' => true, 'message' => 'Composer install completed.', 'output' => $output];
    }

    protected function findComposerBinary(): ?string
    {
        $candidates = ['composer', '/usr/local/bin/composer', '/usr/bin/composer'];
        foreach ($candidates as $bin) {
            $which = trim((string) shell_exec('command -v '.escapeshellarg($bin).' 2>/dev/null'));
            if ($which !== '' && is_executable($which)) {
                return $which;
            }
        }

        return is_file('/usr/local/bin/composer') ? '/usr/local/bin/composer' : null;
    }

    public function runMigrations(): array
    {
        $this->refreshConfig();
        Artisan::call('migrate', ['--force' => true]);

        return [
            'ok' => true,
            'output' => trim(Artisan::output()),
        ];
    }

    public function runSeeders(bool $includeTestUser = false): array
    {
        Artisan::call('db:seed', [
            '--class' => 'AssistPlanSeeder',
            '--force' => true,
        ]);
        $output = trim(Artisan::output());

        if ($includeTestUser) {
            Artisan::call('db:seed', [
                '--class' => 'AssistTestUserSeeder',
                '--force' => true,
            ]);
            $output .= "\n".trim(Artisan::output());
        }

        return ['ok' => true, 'output' => $output];
    }

    /**
     * @param  array{name: string, email: string, password: string}  $admin
     */
    public function createAdminUser(array $admin): void
    {
        (new AssistAdminSeeder)->run($admin);
    }

    public function ensureAppKey(): void
    {
        if (config('app.key')) {
            return;
        }

        Artisan::call('key:generate', ['--force' => true]);
    }

    public function markInstalled(): void
    {
        $path = $this->lockFilePath();
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($path, json_encode([
            'installed_at' => now()->toIso8601String(),
            'version' => config('assist.app_version', '1.0.0'),
        ], JSON_PRETTY_PRINT));
    }

    public function migrationStatus(): array
    {
        try {
            $this->refreshConfig();
            Artisan::call('migrate:status');
            $output = Artisan::output();

            return [
                'ok' => true,
                'output' => $output,
                'pending' => str_contains($output, 'Pending'),
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'output' => $e->getMessage(),
                'pending' => true,
            ];
        }
    }

    public function requirements(): array
    {
        $reqs = [
            ['label' => 'PHP 8.2+', 'ok' => version_compare(PHP_VERSION, '8.2.0', '>=')],
            ['label' => 'PDO MySQL extension', 'ok' => extension_loaded('pdo_mysql')],
            ['label' => 'OpenSSL extension', 'ok' => extension_loaded('openssl')],
            ['label' => 'Mbstring extension', 'ok' => extension_loaded('mbstring')],
            ['label' => 'storage/ writable', 'ok' => is_writable(storage_path())],
            ['label' => 'bootstrap/cache/ writable', 'ok' => is_writable(base_path('bootstrap/cache'))],
            ['label' => '.env writable (or creatable)', 'ok' => is_writable(base_path()) || (is_file(base_path('.env')) && is_writable(base_path('.env')))],
            ['label' => 'vendor/ installed (composer)', 'ok' => $this->vendorPresent()],
        ];

        return $reqs;
    }

    public function requirementsMet(): bool
    {
        foreach ($this->requirements() as $req) {
            if ($req['label'] === 'vendor/ installed (composer)') {
                continue;
            }
            if (! $req['ok']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Full first-time install.
     *
     * @param  array<string, mixed>  $db
     * @param  array<string, mixed>  $app
     * @param  array<string, mixed>  $mail
     * @param  array<string, mixed>  $checkout
     * @param  array{name: string, email: string, password: string}  $admin
     */
    public function install(
        array $db,
        array $app = [],
        array $mail = [],
        array $checkout = [],
        array $admin = [],
        bool $seedTestUser = false,
    ): array {
        if (! $this->requirementsMet()) {
            throw new RuntimeException('Server requirements are not met.');
        }

        if (! $this->vendorPresent()) {
            throw new RuntimeException('Composer vendor/ is missing. Run Composer from setup or SSH first.');
        }

        $test = $this->testDatabaseConnection($db);
        if (! $test['ok']) {
            throw new RuntimeException($test['message']);
        }

        $this->saveEnvironment($db, $app);
        if (! empty($mail)) {
            $this->saveMailEnvironment($mail);
        }
        if (! empty($checkout)) {
            $this->saveCheckoutEnvironment($checkout);
        }
        $this->refreshConfig();
        $this->ensureAppKey();

        $migrate = $this->runMigrations();
        $seed = $this->runSeeders($seedTestUser);

        if (! empty($admin['email']) && ! empty($admin['password'])) {
            $this->createAdminUser($admin);
        }

        $this->markInstalled();

        return [
            'migrate' => $migrate,
            'seed' => $seed,
        ];
    }
}
