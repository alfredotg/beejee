<?php

namespace BeeJee\Form;

use BeeJee\Form;
use Symfony\Component\Validator\Constraints as Assert;
use BeeJee\Model\Task;

class TaskEdit extends Form
{
    public function getConstraints(): array
    {
        return [
            'status' => new Assert\Optional([
                new Assert\Choice([strval(Task::STATUS_COMPLETED)]),
            ]),
            'description' => [
                new Assert\NotBlank([]),
                new Assert\Length(['max' => 500])
            ]
        ];
    }
}
