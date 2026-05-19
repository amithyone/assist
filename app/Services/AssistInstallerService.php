<?php

namespace App\Services;

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
        ]);
    }

    public function refreshConfig(): void
    {
        Artisan::call('config:clear');
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
        return [
            ['label' => 'PHP 8.2+', 'ok' => version_compare(PHP_VERSION, '8.2.0', '>=')],
            ['label' => 'PDO MySQL extension', 'ok' => extension_loaded('pdo_mysql')],
            ['label' => 'OpenSSL extension', 'ok' => extension_loaded('openssl')],
            ['label' => 'Mbstring extension', 'ok' => extension_loaded('mbstring')],
            ['label' => 'storage/ writable', 'ok' => is_writable(storage_path())],
            ['label' => 'bootstrap/cache/ writable', 'ok' => is_writable(base_path('bootstrap/cache'))],
            ['label' => '.env writable (or creatable)', 'ok' => is_writable(base_path()) || (is_file(base_path('.env')) && is_writable(base_path('.env')))],
        ];
    }

    public function requirementsMet(): bool
    {
        foreach ($this->requirements() as $req) {
            if (! $req['ok']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Full first-time install: env, key, migrate, seed, lock.
     *
     * @param  array<string, mixed>  $db
     * @param  array<string, mixed>  $app
     */
    public function install(array $db, array $app = [], bool $seedTestUser = false): array
    {
        if (! $this->requirementsMet()) {
            throw new RuntimeException('Server requirements are not met.');
        }

        $test = $this->testDatabaseConnection($db);
        if (! $test['ok']) {
            throw new RuntimeException($test['message']);
        }

        $this->saveEnvironment($db, $app);
        $this->refreshConfig();
        $this->ensureAppKey();

        $migrate = $this->runMigrations();
        $seed = $this->runSeeders($seedTestUser);
        $this->markInstalled();

        return [
            'migrate' => $migrate,
            'seed' => $seed,
        ];
    }
}
