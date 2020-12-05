<?php

namespace BeeJee\Web;

use BeeJee\Di;
use BeeJee\ViewInterface;
use Psr\Http\Message\ServerRequestInterface;

class View extends \Twig\Environment implements ViewInterface
{
    protected $di;

    public function __construct(Di $di, $loader, $options)
    {
        $this->di = $di;
        parent::__construct($loader, $options);
    }
}
