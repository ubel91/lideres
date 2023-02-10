<?php

namespace App\Form;

use App\Entity\Libro;
use App\Entity\Unidad;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UnidadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => false,
                'attr' => ['placeholder'=>'Nombe de la Unidad'],
            ])
            ->add('archivo', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => ['placeholder'=>'Seleccione un archivo'],
                'constraints' => [
                    new File([
                        'maxSize' => '200M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Por favor seleccione un documento PDF válido',
                        'maxSizeMessage' => 'El archivo es muy grande ({{ size }} {{ suffix }}). Tamaño máximo permitido {{ limit }} {{ suffix }}.'
                    ])
                ]
            ])
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
            ->add('actividadForm', ActividadesType::class, [
                'required'=>false,
                'label'=>'Adicionar Actividad',
                'label_attr' => [
                    'style'=>'color:#aacd4e; font-weight: 600',
                    'class'=>'text-center'
                ],
                'row_attr'=>['style'=>'display:none']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Unidad::class,
        ]);
    }
}
