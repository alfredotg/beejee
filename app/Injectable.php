<?php

namespace BeeJee;

class Injectable
{
    // @var Di
    protected $di;

    public function __construct(Di $di)
    {
        $this->di = $di;
    }

    public function getDi(): Di
    {
        return $this->di;
    }
}
