<?php

namespace BeeJee\Web;

use BeeJee\Di;
use BeeJee\Injectable;
use BeeJee\ServiceProvider;
use BeeJee\UrlResolverInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use League\Route\Http\Exception\HttpExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response;

class App extends Injectable
{
    public function __construct(Di $di)
    {
        parent::__construct($di);
        $provider = new ServiceProvider($di);
        $provider->registreDefault();
        $provider->registreWeb();
    }

    public function run(): void
    {
        $request = $this->di->get(ServerRequestInterface::class);
        $handler = $this->di->get(RequestHandlerInterface::class);
        $emitter = $this->di->get(EmitterInterface::class);

        try {
            $response = $handler->handle($request);
        } catch (ResponseInterface $error_response) {
            $response = $error_response;
        } catch (HttpExceptionInterface $error) {
            $response = new Response();
            $response = $response->withStatus($error->getStatusCode());
            $response->getBody()->write($error->getStatusCode().": " . $error->getMessage());
            if ($response->getStatusCode() == 403) {
                $url = $this->di->get(UrlResolverInterface::class)->resolve('/login');
                $response = $response->withHeader('Location', $url)->withStatus(302);
            }
        } catch (\Throwable $e) {
            $response = new Response();
            $response = $response->withStatus(500);
            $response->getBody()->write("500: " . $e->getMessage());
        }
        $emitter->emit($response);
    }
}
