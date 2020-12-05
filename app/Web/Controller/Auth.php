<?php

namespace BeeJee\Web\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use BeeJee\Web\Controller;
use BeeJee\Model\User;
use Symfony\Component\Validator\Validation;
use BeeJee\Form;
use BeeJee\AuthInterface;

class Auth extends Controller
{
    public function login(ServerRequestInterface $request): ResponseInterface
    {
        $errors = [];
        $login = null;
        if ($request->getMethod() == 'POST') {
            $user = new User;
            $form = new Form\Auth;
            $errors = $form ->bind($user, $request->getParsedBody());

            if (!count($errors)) {
                $login = $user->login;
                $user = $this->findUser($user->login, $user->password);
                if (!$user) {
                    $errors[] = "Пользователь не найден";
                } else {
                    $this->di->get(AuthInterface::class)->setUser($user);
                    return $this->redirect('/');
                }
            }
        }
        return $this->viewResponse('auth/login.html', [
            'login' => $login,
            'errors' => $errors
        ]);
    }

    private function findUser(string $login, string $password): ?User
    {
        $user = User::firstWhere('login', $login);
        if (!$user) {
            return null;
        }
        if (!$user->checkPassword($password)) {
            return null;
        }
        return $user;
    }
    
    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        $this->di->get(AuthInterface::class)->setUser(null);
        return $this->redirect('/');
    }
}
