<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('photo', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr'=>['title' => 'Escoja una foto'],
                'row_attr'=>['style'=>'opacity:0'],
                'constraints' => [
                    new Image([
                        'maxSize' => '10M'
                    ])
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'row_attr'=> ['style'=>'display:none'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor introduzca su contraseña',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Su contraseña debe tener al menos {{ limit }} caracteres',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label' => false,
                    'attr' => ['placeholder'=>'Nueva Contraseña']
                ],
                'second_options' => [
                    'row_attr'=> ['style'=>'display:none'],
                    'label' => false,
                    'attr' => ['placeholder'=>'Repita su Contraseña']
                ],
                'invalid_message' => 'Las contraseñas no coinciden',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
            ->add('Cambiar', SubmitType::class, ['row_attr'=> ['style'=>'display:none']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
