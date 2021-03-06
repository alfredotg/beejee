<?php

namespace BeeJee\Web;

use BeeJee\Injectable;
use BeeJee\ViewInterface;
use BeeJee\UrlResolverInterface;
use BeeJee\Web\RequestAttributes;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Controller extends Injectable
{
    protected function viewResponse(string $template, array $context): ResponseInterface
    {
        $context = array_merge([
            'di' => $this->di,
            'request' => $this->di->get(ServerRequestInterface::class)
        ], $context);
        $html = $this->di->get(ViewInterface::class)->render($template, $context);
        $response = new \Laminas\Diactoros\Response;
        $response->getBody()->write($html);
        return $response;
    }

    protected function redirect(string $url): ResponseInterface
    {
        $url = $this->di->get(UrlResolverInterface::class)->resolve($url);
        $response = new \Laminas\Diactoros\Response;
        return $response->withHeader('Location', $url)->withStatus(302);
    }
}
