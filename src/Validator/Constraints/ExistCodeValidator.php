<?php


namespace App\Validator\Constraints;

use App\Repository\CodigoRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistCodeValidator extends ConstraintValidator
{
    private $codigoRepository;

    /**
     * ExistCodeValidator constructor.
     * @param $codigoRepository
     */
    public function __construct(CodigoRepository $codigoRepository)
    {
        $this->codigoRepository = $codigoRepository;
    }


    public function validate($value, Constraint $constraint)
    {

        if (!$constraint instanceof ExistCode){
            throw new UnexpectedTypeException($constraint, ExistCode::class);
        }

        if (null === $value || '' === $value){
            return;
        }

        $validCodes = $this->codigoRepository->findCodigosByBook($value->getLibro()->getId());
        $codeToValidate = $value->getCodigoActivacion();
        $libroName = $value->getLibro()->getNombre();
        $exist = false;
        foreach ($validCodes as $code){
            if ($codeToValidate === $code['codebook']){
                $exist = true;
            }
        }

        if (!$exist){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $codeToValidate)
                ->setParameter('{{ libro }}', $libroName)
                ->addViolation();
        }
    }
}