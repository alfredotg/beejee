<?php

namespace BeeJee\Web;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use BeeJee\Di;
use BeeJee\UrlResolverInterface;

class Router extends \League\Route\Router implements UrlResolverInterface
{
    protected $di;

    public function __construct(Di $di)
    {
        parent::__construct();
        $this->di = $di;
        $this->map('GET', '/', [new Controller\Tasks($di), 'list']);
        
        $handler = [new Controller\Tasks($di), 'add'];
        $this->map('GET', '/tasks/add', $handler);
        $this->map('POST', '/tasks/add', $handler);
        
        $handler = [new Controller\Tasks($di), 'edit'];
        $this->map('GET', '/tasks/edit/{id}', $handler);
        $this->map('POST', '/tasks/edit/{id}', $handler);

        $this->map('POST', '/logout', [new Controller\Auth($di), 'logout']);

        $handler = [new Controller\Auth($di), 'login'];
        $this->map('GET', '/login', $handler);
        $this->map('POST', '/login', $handler);
    }

    private function rewrite(ServerRequestInterface $request): ServerRequestInterface
    {
        $uri = $request->getUri()->getPath();
        $script_folder = dirname($request->getServerParams()['SCRIPT_NAME'] ?? '');
        if ($script_folder && strpos($uri, $script_folder) == 0) {
            $uri = substr($uri, strlen($script_folder));
            $request = $request
                ->withAttribute(RequestAttributes::BASE_URI, $script_folder)
                ->withUri($request->getUri()->withPath($uri));
            $this->di->set(ServerRequestInterface::class, $request);
        }
        return $request;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return parent::handle($this->rewrite($request));
    }

    public function resolve(string $url): string
    {
        $request = $this->di->get(ServerRequestInterface::class);
        if ($base = $request->getAttribute(RequestAttributes::BASE_URI)) {
            $url = $base . '/' . ltrim($url, '/');
        }
        return $url;
    }
}
