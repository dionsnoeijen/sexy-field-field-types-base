<?php
declare(strict_types=1);

namespace Tardigrades\FieldType\Choice\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ChoiceChoicesConfigValidator extends ConstraintValidator
{
    const VALUE = 'value';
    const TEXT = 'text';

    public function validate($choices, Constraint $constraint): void
    {
        if (!$constraint instanceof ChoiceChoicesConfig) {
            throw new UnexpectedTypeException($constraint, ChoiceChoicesConfig::class);
        }

        // ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.)
        // to take care of that
        if (null === $choices || '' === $choices) {
            return;
        }

        if (!is_array($choices)) {
            throw new UnexpectedValueException($choices, 'array');
        }

        if (!count($choices)) {
            throw new UnexpectedValueException($choices, print_r($choices, true));
        }

        foreach ($choices as $choice) {
            if (empty($choice[self::TEXT])) {
                throw new UnexpectedValueException($choice, 'text value required');
            }
            if (empty($choice[self::VALUE])) {
                throw new UnexpectedValueException($choice, 'value required');
            }
        }
    }
}
