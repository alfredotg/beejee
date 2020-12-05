<?php

namespace BeeJee\Web;

use BeeJee\Injectable;
use BeeJee\AuthInterface;
use BeeJee\Model\User;
use League\Route\Http\Exception\ForbiddenException;

class Auth extends Injectable implements AuthInterface
{
    const USER_KEY = 'user_id';

    private $user;

    public function setUser(?User $user): void
    {
        $this->user = $user;
        $this->startSession();
        if ($user === null) {
            unset($_SESSION[self::USER_KEY]);
        } else {
            $_SESSION[self::USER_KEY] = $user->id;
        }
    }

    public function getUser(): ?User
    {
        if (!$this->user) {
            $this->startSession();
            $id = intval($_SESSION[self::USER_KEY] ?? 0);
            if (!$id) {
                return null;
            }
            $this->user = User::first($id) ?: null;
            if (!$this->user) {
                $this->setUser(null);
            }
        }
        return $this->user;
    }

    public function require(): User
    {
        $user = $this->getUser();
        if (!$user) {
            throw new ForbiddenException();
        }
        return $user;
    }

    private function startSession(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
}
