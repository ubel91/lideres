<?php

namespace App\Form;

use App\Entity\Profesor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ProfesorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('numero_identificacion', NumberType::class,
//                [
//                    'label'  => false ,
//                    'attr' => [
//                        'placeholder' => 'Numero Identificación'
//                    ],
//                    'constraints' => [
//                        new Regex('/^[0-9]{10}/')
//                    ],
//                    'required' =>false,
//                ])
//            ->add('celular', TelType::class, ['label'  => false ,'attr' => ['placeholder' => 'Celular']])
//            ->add('identificacion')
//            ->add('user')
//            ->add('libros')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profesor::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'profesor_item',
        ]);
    }
}
