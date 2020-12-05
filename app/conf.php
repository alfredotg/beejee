<?php

use Symfony\Component\Dotenv\Dotenv;

function require_env(string $name): string
{
    if (!isset($_ENV[$name])) {
        throw new \Exception("ENV {$name} not found");
    }
    return $_ENV[$name];
}

$_ENV['APP_ROOT'] = dirname(__DIR__);

$dotenv = new Dotenv();
$dotenv->load($_ENV['APP_ROOT'].'/.env');
