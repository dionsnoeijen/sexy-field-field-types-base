<?php
declare(strict_types=1);

namespace Tardigrades\FieldType\Choice\Validator;

use Symfony\Component\Validator\Constraint;

class ChoiceChoicesConfig extends Constraint
{
    public string $message = 'The choice field contains no valid choices configuration';

    public function validatedBy()
    {
        return static::class.'Validator';
    }
}
