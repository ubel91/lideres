<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Form\EventListener\AddCantonFieldListener;
use App\Form\EventListener\AddProvinciaFieldListener;
use App\Repository\RoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label'  => false ,
                'attr' => ['placeholder' => 'Nombre de usuario']
            ])
            ->add('nombre', TextType::class, ['label'  => false ,'attr' => ['placeholder' => 'Nombres']])
            ->add('primer_apellido', TextType::class, ['label'  => false ,'attr' => ['placeholder' => 'Primer Apellido']])
            ->add('segundo_apellido', TextType::class, ['label'  => false ,'attr' => ['placeholder' => 'Segundo Apellido']])
            ->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'))
            ->add('roles', EntityType::class, [
                'class' => Role::class,
                'query_builder' => function(RoleRepository $rp){
                    return $rp->createQueryBuilder('r')
                        ->where('r.rolename = :name OR r.rolename = :name2')
                        ->setParameters(array(
                            'name' => Role::ROLE_ESTUDIANTE,
                            'name2' => Role::ROLE_PROFESOR
                        ));
                },
                'choice_label' => function ($choice) {
                    if (Role::ROLE_ESTUDIANTE === $choice->getRolename()) {
                        return 'Estudiante';
                    } elseif (Role::ROLE_PROFESOR === $choice->getRolename()){
                        return 'Docente';
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
                    $arrayClass = ['class' => 'estudiante-change'];
                    if (is_object($choice)){
                        if ($choice->getRolename() === Role::ROLE_ESTUDIANTE)
                            $arrayClass['data'] = 'radioEstudiante';
                        elseif ($choice->getRolename() === Role::ROLE_PROFESOR)
                            $arrayClass['data'] = 'radioProfesor';
                        return $arrayClass;
                    }

                },
                'label_attr' => ['class' => 'custom-control-label'],
                'multiple' => false,
                'expanded' => true
            ])
            ->add('e_mail', EmailType::class, ['label'  => false ,'attr' => ['placeholder'  => 'Correo Electrónico']])
//            ->add('celular', TelType::class, ['label'  => false ,'attr' => ['placeholder' => 'Celular']])
            ->add('registrar', SubmitType::class)
            ->add('profesorForm', ProfesorType::class, ['label' => false])
            ->add('estudiantesForm', EstudianteType::class, ['label' => false])
            ->add('nombre_institucion', TextType::class, ['label'  => false ,'attr' => ['placeholder' => 'Nombre Institución']])
            ->add('pais_institucion', TextType::class, [ 'data' => 'Ecuador' , 'label'  => false ,'attr' => ['placeholder' => 'Ecuador', 'readonly' => 'readonly']])
            ->addEventSubscriber(new AddProvinciaFieldListener())
            ->addEventSubscriber(new AddCantonFieldListener())
            ->add('photo', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr'=>['title' => 'Escoja una foto'],
                'constraints' => [
                    new Image([
                        'maxSize' => '5M'
                    ])
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_token_id'   => 'user_item',
        ]);
    }

    public function onPreSetData(FormEvent $event){

        $form = $event->getForm();
        $data = $event->getData();

        if ($data instanceof User){
            $form->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'Clave',
                'first_options' => [
                    'constraints' => [
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
                    'label' => false,
                    'attr' => ['placeholder'=>'Repita su Contraseña']
                ],
                'invalid_message' => 'Las contraseñas no coinciden',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'required' => false
            ]);
        } else {
            $form->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'Clave',
                'first_options' => [
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
                    'label' => false,
                    'attr' => ['placeholder'=>'Repita su Contraseña']
                ],
                'invalid_message' => 'Las contraseñas no coinciden',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ]);
        }

    }
}
