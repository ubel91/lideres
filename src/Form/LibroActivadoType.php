<?php

namespace App\Form;

use App\Entity\Libro;
use App\Entity\LibroActivado;
use App\Entity\Role;
use App\Repository\LibroRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class LibroActivadoType extends AbstractType
{
    private $security;

    /**
     * LibroActivadoType constructor.
     * @param $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $choiceBooks = $options['choiceBooks'];
//        dd($choiceBooks[30]);
        $builder
            ->add('codigo_activacion', TextType::class)
//            ->add('libro', EntityType::class, [
//                'class' => Libro::class,
//                'choice_label' => 'nombre',
//                'placeholder' => 'Escoja un Libro',
//                'label' => false,
//                'choices' => $choiceBooks,
//            ])
            ->add('Activar', SubmitType::class)
//            ->add('profesor')
//            ->add('estudiante')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LibroActivado::class,
//            'choiceBooks' => null
        ]);
    }
}
