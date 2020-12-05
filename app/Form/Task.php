<?php

namespace BeeJee\Form;

use BeeJee\Form;
use Symfony\Component\Validator\Constraints as Assert;

class Task extends Form
{
    public function getConstraints(): array
    {
        return [
            'username' => [
                new Assert\NotBlank([]),
                new Assert\Length(['max' => 200])
            ],
            'email' => new Assert\Email([]),
            'description' => [
                new Assert\NotBlank([]),
                new Assert\Length(['max' => 500])
            ]
        ];
    }
}
