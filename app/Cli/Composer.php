<?php

namespace BeeJee\Cli;

use Illuminate\Database\Capsule\Manager as Capsule;

class Composer
{
    public static function postInstall(): void
    {
        require_once(__DIR__ . '/../../vendor/autoload.php');
        
        $url = parse_url(require_env('DBURL'));
        if ($url['scheme'] == 'sqlite') {
            touch($url['path']); // create db
        }

        $app = new App(new \BeeJee\Di);
        $app->postInstall();
    }
}
