<?php

namespace BeeJee;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

abstract class Form
{
    /*
     * @return string[] Errors
     */
    public function bind(object $target, array $input): array
    {
        $filtered = [];
        foreach ($this->getConstraints() as $key => $_) {
            $filtered[$key] = $target->{$key} = $input[$key] ?? null;
        }
        $violations = $this->validate($filtered);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            return $errors;
        }
        return [];
    }

    public function validate(array $input)
    {
        $validator = Validation::createValidator();
        $constraint = new Assert\Collection($this->getConstraints());
        return $validator->validate($input, $constraint);
    }

    abstract public function getConstraints(): array;
}
