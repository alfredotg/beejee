<?php

namespace BeeJee\Model;

class User extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;

    public function setPassword(string $value): void
    {
        $this->password = password_hash($value, PASSWORD_DEFAULT);
    }

    public function checkPassword(string $value): bool
    {
        return password_verify($value, $this->password);
    }
}
