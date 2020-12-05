<?php

namespace BeeJee\Form;

use BeeJee\Form;
use Symfony\Component\Validator\Constraints as Assert;

class Auth extends Form
{
    public function getConstraints(): array
    {
        return [
            'login' => [
                new Assert\NotBlank([]),
                new Assert\Length(['max' => 200])
            ],
            'password' => [
                new Assert\NotBlank([]),
            ]
        ];
    }
}
