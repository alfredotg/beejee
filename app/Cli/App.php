<?php

namespace BeeJee\Cli;

use BeeJee\Di;
use BeeJee\Injectable;
use BeeJee\Migrations;
use BeeJee\ServiceProvider;

class App extends Injectable
{
    public function __construct(Di $di)
    {
        parent::__construct($di);
        (new ServiceProvider($di))->registreDefault();
    }

    public function postInstall(): void
    {
        // migrations
        (new Migrations($this->di))->run();
    }
}
