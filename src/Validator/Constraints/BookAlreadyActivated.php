<?php


namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class BookAlreadyActivated
 * @package App\Validator\Constraints
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class BookAlreadyActivated extends Constraint
{
    public $message = 'El libro "{{ libro }}" fue activado por usted anteriormente';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

