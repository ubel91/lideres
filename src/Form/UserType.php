<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, ['label'  => false, 'attr'=>['placeholder' => 'Usuario']])
            ->add('nombre', TextType::class, ['label'  => false ,'attr' => ['placeholder' => 'Nombre Completo']])
            ->add('primer_apellido', TextType::class, ['label'  => false ,'attr' => ['placeholder' => 'Primer Apellido']])
            ->add('segundo_apellido', TextType::class, ['label'  => false ,'attr' => ['placeholder' => 'Segundo Apellido']])

            ->add('e_mail',EmailType::class,[ 'label' => false,'attr' => ['placeholder' => 'Correo Electrónico']])
            ->add('registrar', SubmitType::class, ['label' => 'Guardar', 'attr'=>['class'=>'btn btn-success']])
            ->add('roles', EntityType::class, [
                'class' => Role::class,
                'query_builder' => function(RoleRepository $rp){
                    return $rp->createQueryBuilder('r')
                        ->where('r.rolename = :name OR r.rolename = :name2')
                        ->setParameters(array(
                            'name' => 'ROLE_ADMIN',
                            'name2' => 'ROLE_SUPER_ADMIN'
                        ));
                },
                'choice_label' => function ($choice) {
                    if (Role::ROLE_ADMIN === $choice->getRolename()) {
                        return 'Admin';
                    } elseif (Role::ROLE_SUPER_ADMIN === $choice->getRolename()){
                        return 'Super Admin';
                    } else {
                        return '';
                    }
                },
                'choice_value' => function ($choice){
                    if (is_object($choice))
                        return $choice->getId();
                },
                'label' => false,
                'attr' => [ 'class' => 'custom-control custom-radio custom-control-inline'],
                'choice_attr' => function($choice, $key, $value) {
                    if (is_object($choice))
                        if ($choice->getId())
                            return ['class' => 'custom-control-input'];
                },
                'label_attr' => ['class' => 'custom-control-label mr-5'],
                'multiple' => false,
                'expanded' => true
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'))
//            ->add('profesorForm', ProfesorType::class, ['label' => false])
//            ->add('estudiantesForm', EstudianteType::class, ['label' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,

        ]);
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ($data instanceof User){
            $form->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => false,
                'mapped' => false,
                'required' => false,
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor introduzca su contraseña',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'La constraseña debe tener al menos {{ limit }} caracteres',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ])
                    ],
                    'label'=>false,
                    'attr' => ['placeholder' => 'Contraseña']
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => ['placeholder'=>'Repita su Contraseña']
                ],
                'invalid_message' => 'Las contraseñas no coinciden',
                ]);
        } else {
            $form->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => false,
                'mapped' => false,
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Por favor introduzca su contraseña',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'La constraseña debe tener al menos {{ limit }} caracteres',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ])
                    ],
                    'attr' => ['placeholder' => 'Contraseña']
                ],
                'second_options' => [
                    'label' => false,
                    'attr' => ['placeholder'=>'Repita su Contraseña']
                ],
                'invalid_message' => 'Las contraseñas no coinciden',
                ]);
            }
    }
}
