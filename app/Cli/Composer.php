<?php

namespace BeeJee\Cli;

use Illuminate\Database\Capsule\Manager as Capsule;

class Composer
{
    public static function postInstall(): void
    {
        require_once(__DIR__ . '/../../vendor/autoload.php');
        $app = new App(new \BeeJee\Di);
        $app->postInstall();
    }
}
