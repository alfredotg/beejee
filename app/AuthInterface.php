<?php

namespace BeeJee;

interface AuthInterface
{
    public function setUser(?Model\User $user): void;
    public function getUser(): ?Model\User;
    public function require(): Model\User;
}
