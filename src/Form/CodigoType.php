<?php

namespace App\Form;

use App\Entity\Codigo;
use App\Entity\Libro;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use function Matrix\add;

class CodigoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('libro', EntityType::class, [
                'class' => Libro::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Escoja un Libro',
                'label' => false,
                'choice_value' => function ($choice){
                    if ($choice)
                        return $choice->getId();
                }
            ])
            ->add('fechaInicio', DateType::class)
            ->add('fechaFin', DateType::class)
            ->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Codigo::class,
        ]);
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ($data instanceof Codigo && $data->getId()){
            $form->add('codebook', TextType::class, [
                'label'=>false,
                'attr'=>['placeholder'=>'Código de Activación' ]
            ]);
        } else {
            $form->add('codebook', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => ['placeholder'=>'Escoja un archivo Excel...'],
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel'
                            ],
                        'mimeTypesMessage' => 'Por favor seleccione un archivo Excel válido',
                    ])
                ]
            ]);
        }

        $form->add('Asignar', SubmitType::class);
    }
}
