<?php

namespace BeeJee;

use Illuminate\Database\Capsule\Manager as Capsule;
use BeeJee\ConnectionInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

class ServiceProvider extends Injectable
{
    public function registreDefault(): void
    {
        $di = $this->getDi();
        $url = parse_url(require_env('DBURL'));
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => $url['scheme'],
            'host'      => $url['host'] ?? '',
            'database'  => $url['scheme'] == 'sqlite' ? $url['path'] : trim($url['path'], '/'),
            'username'  => $url['user'] ?? null,
            'password'  => $url['pass'] ?? null,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $di->registre(Capsule::class, function () use ($capsule): Capsule {
            return $capsule;
        });
    }

    public function registreWeb(): void
    {
        $di = $this->getDi();
        $di->registre(ServerRequestInterface::class, function (): ServerRequestInterface {
            return ServerRequestFactory::fromGlobals(
                $_SERVER,
                $_GET,
                $_POST,
                $_COOKIE,
                $_FILES
            );
        });

        $di->registre(Web\Router::class, function () use ($di): RequestHandlerInterface {
            $router = new Web\Router($di);
            return $router;
        });

        $di->registre(EmitterInterface::class, function (): EmitterInterface {
            return new SapiEmitter;
        });
        
        $di->registre(ViewInterface::class, function () use ($di): ViewInterface {
            $loader = new \Twig\Loader\FilesystemLoader(require_env('APP_ROOT') . '/views');
            return new Web\View($di, $loader, []);
        });

        $di->registre(AuthInterface::class, function () use ($di): AuthInterface {
            return new Web\Auth($di);
        }, 'auth');
    }
}
