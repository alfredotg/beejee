<?php

namespace BeeJee;

interface ViewInterface
{
    /*
    * @param string $name The template name
    */
    public function render($name, array $context = []): string;
}
