<?php
/**
 * Простая загрузка переменных окружения из .env файла
 */

$envFile = dirname(__DIR__) . '/.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Игнорируем комментарии
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        // Парсим формат KEY=VALUE
        if (str_contains($line, '=')) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Устанавливаем переменную окружения, если она еще не установлена
            if (!getenv($key)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}
