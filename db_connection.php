<?php
require 'vendor/autoload.php';

use MongoDB\Client;

function loadEnvFile(string $envPath): void
{
    if (!is_file($envPath)) {
        return;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
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

        $key = trim($parts[0]);
        $value = trim($parts[1]);

        if ($value !== '' && (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        )) {
            $value = substr($value, 1, -1);
        }

        if (getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

try {
    loadEnvFile(__DIR__ . '/.env');

    // Fallback to local MongoDB for local development when env vars are not exported.
    $mongoUri = getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1:27017';

    $mongoDbName = getenv('MONGODB_DB') ?: 'food_ordering';

    $client = new Client($mongoUri, [], ['serverSelectionTimeoutMS' => 3000]);
    $db = $client->selectDatabase($mongoDbName);
} catch (Throwable $e) {
    // Log detailed error server-side, but don't expose secrets in responses
    error_log('MongoDB connection error: ' . $e->getMessage());
    echo 'Database connection failed. Ensure MongoDB is running and .env has valid MONGODB_URI and MONGODB_DB values.';
    exit;
}
?>
