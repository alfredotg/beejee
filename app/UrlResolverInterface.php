<?php

namespace BeeJee;

interface UrlResolverInterface
{
    public function resolve(string $url): string;
}
