<?php

declare(strict_types=1);

$requiredKeys = [
    'APP_NAME',
    'APP_ENV',
    'APP_DEBUG',
    'APP_URL',
    'DB_CONNECTION',
    'DB_HOST',
    'DB_PORT',
    'DB_DATABASE',
    'DB_USERNAME',
    'DB_PASSWORD',
    'APP_KEY',
];

$files = [
    '.env.dev',
    '.env.uat',
    '.env.prod',
    '.env.ci',
];

$expectedAppEnv = [
    '.env.dev' => 'development',
    '.env.uat' => 'uat',
    '.env.prod' => 'production',
    '.env.ci' => 'ci',
];

$errors = [];

foreach ($files as $file) {
    if (! is_file($file)) {
        $errors[] = "{$file}: file is missing";

        continue;
    }

    $values = [];
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines === false) {
        $errors[] = "{$file}: cannot read file";

        continue;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        $parts = explode('=', $trimmed, 2);

        if (count($parts) !== 2) {
            continue;
        }

        $values[$parts[0]] = $parts[1];
    }

    foreach ($requiredKeys as $key) {
        if (! array_key_exists($key, $values)) {
            $errors[] = "{$file}: missing required key {$key}";
        }
    }

    if (($values['APP_ENV'] ?? null) !== $expectedAppEnv[$file]) {
        $actual = $values['APP_ENV'] ?? '(missing)';
        $errors[] = "{$file}: APP_ENV must be '{$expectedAppEnv[$file]}', got '{$actual}'";
    }

    if (in_array($file, ['.env.uat', '.env.prod', '.env.ci'], true) && ($values['APP_DEBUG'] ?? '') !== 'false') {
        $errors[] = "{$file}: APP_DEBUG must be false";
    }

    if ($file === '.env.dev' && ($values['APP_DEBUG'] ?? '') !== 'true') {
        $errors[] = "{$file}: APP_DEBUG must be true";
    }

    if ($file === '.env.ci') {
        if (($values['DB_CONNECTION'] ?? '') !== 'sqlite') {
            $errors[] = "{$file}: DB_CONNECTION must be sqlite";
        }

        if (($values['DB_DATABASE'] ?? '') !== ':memory:') {
            $errors[] = "{$file}: DB_DATABASE must be :memory:";
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Environment validation failed:\n");

    foreach ($errors as $error) {
        fwrite(STDERR, " - {$error}\n");
    }

    exit(1);
}

echo "Environment validation passed for .env.dev, .env.uat, .env.prod and .env.ci\n";
