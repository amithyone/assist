<?php

namespace App\Services;

use RuntimeException;

class EnvWriter
{
    public function __construct(
        protected ?string $path = null
    ) {
        $this->path = $path ?? base_path('.env');
    }

    /**
     * @param  array<string, string|null>  $values
     */
    public function setMany(array $values): void
    {
        if (! is_file($this->path)) {
            if (is_file(base_path('.env.example'))) {
                copy(base_path('.env.example'), $this->path);
            } else {
                file_put_contents($this->path, "APP_NAME=Assist\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=http://localhost\n\n");
            }
        }

        $contents = file_get_contents($this->path);
        if ($contents === false) {
            throw new RuntimeException('Unable to read .env file.');
        }

        foreach ($values as $key => $value) {
            $contents = $this->setInContents($contents, $key, $value);
        }

        if (file_put_contents($this->path, $contents) === false) {
            throw new RuntimeException('Unable to write .env file.');
        }
    }

    protected function setInContents(string $contents, string $key, ?string $value): string
    {
        $escaped = $this->escape($value);
        $pattern = '/^'.preg_quote($key, '/').'=.*$/m';

        if (preg_match($pattern, $contents)) {
            return (string) preg_replace($pattern, $key.'='.$escaped, $contents);
        }

        return rtrim($contents)."\n".$key.'='.$escaped."\n";
    }

    protected function escape(?string $value): string
    {
        if ($value === null || $value === '') {
            return '""';
        }

        if (preg_match('/[\s#="\']/', $value)) {
            return '"'.str_replace(['\\', '"'], ['\\\\', '\\"'], $value).'"';
        }

        return $value;
    }
}
