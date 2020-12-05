<?php

namespace BeeJee\Web;

use BeeJee\Di;
use BeeJee\Injectable;
use BeeJee\ServiceProvider;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
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
            $emitter->emit($response);
        } catch (ResponseInterface $error_response) {
            $emitter->emit($error_response);
        } catch (\Throwable $e) {
            $response = new Response();
            $response->withStatus(500);
            $response->getBody()->write("500: " . $e->getMessage());
            $emitter->emit($response);
            exit(1);
        }
    }
}
