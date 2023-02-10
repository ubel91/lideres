<?php


namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class ExistCode
 * @package App\Validator\Constraints
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class ExistCode extends Constraint
{
    public $message = 'El codigo "{{ code }}", no esta autorizado para el libro "{{ libro }}"';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

