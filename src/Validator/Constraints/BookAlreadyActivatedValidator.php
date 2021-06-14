<?php


namespace App\Validator\Constraints;

use App\Repository\CodigoRepository;
use App\Repository\LibroActivadoRepository;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BookAlreadyActivatedValidator extends ConstraintValidator
{
    private $codigoRepository;
    private $token;
    private $librosActivados;

    /**
     * BookAlreadyActivatedValidator constructor.
     * @param CodigoRepository $codigoRepository
     * @param LibroActivadoRepository $libroActivadoRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(CodigoRepository $codigoRepository, LibroActivadoRepository $libroActivadoRepository, TokenStorageInterface $tokenStorage)
    {
        $this->codigoRepository = $codigoRepository;
        $this->token = $tokenStorage;
        $this->librosActivados = $libroActivadoRepository;
    }


    public function validate($value, Constraint $constraint)
    {

        if (!$constraint instanceof BookAlreadyActivated){
            throw new UnexpectedTypeException($constraint, BookAlreadyActivated::class);
        }

        if (null === $value || '' === $value){
            return;
        }

        $codeToValidate = $value->getCodigoActivacion();
        $codeDates = $this->codigoRepository->findDatesByCode($codeToValidate);
        $librosActivados = $this->librosActivados->findAll();
        $libroName = $value->getLibro()->getNombre();
        $now = new DateTime('NOW');
        $exist = false;
        $currentUserId = $this->token->getToken()->getUser()->getId();
        foreach ($librosActivados as $la){
//            dd($la->getCodigoActivacion());
            if ($codeToValidate === $la->getCodigoActivacion()){
                if ($now > $codeDates['fechaInicio'] || $codeDates['fechaFin'] > $now)
                    $exist = true;
            }
        }

        if ($exist){
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $codeToValidate)
                ->setParameter('{{ libro }}', $libroName)
                ->addViolation();
        }
    }
}