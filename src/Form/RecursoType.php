<?php

namespace App\Form;

use App\Entity\Libro;
use App\Entity\Recurso;
use App\Entity\TipoRecurso;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class RecursoType extends AbstractType
{
    private $mimeTypesFile = [
        'video/mp4',
        'audio/mpeg',
        'application/pdf',
        'application/x-pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreRecurso', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Nombre del Recurso']
            ])
            ->add('tipo', EntityType::class, [
                'class' => TipoRecurso::class,
                'label' => false,
                'required'=> true,
                'placeholder' => 'Escoja un tipo',
                'choice_label' => function ($choice) {
                    if (TipoRecurso::REFERENCE_URL === $choice->getNombre()) {
                        return 'Youtube';
                    } elseif (TipoRecurso::REFERENCE_FILE === $choice->getNombre()){
                        return 'Doc(x), xls(x), pdf, mp3, mp4';
                    } else {
                        return '';
                    }
                },
            ])
            ->add('libro', EntityType::class, [
                'class' => Libro::class,
                'label' => false,
                'placeholder' => 'Escoja un Libro',
                'choice_label' => 'nombre',
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'))
//            ->addEventListener(FormEvents::POST_SUBMIT, array($this, 'onPostSubmit'))
            ->add('paraPlataforma', CheckboxType::class, [
                'label' => 'Plataforma',
                'label_attr' => ['class' => 'custom-control-label'],
                'required' => false
            ])
            ->add('paraDocente', CheckboxType::class, [
                'label' => 'Recursos Docentes',
                'label_attr' => ['class' => 'custom-control-label'],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recurso::class,
        ]);
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ($data instanceof Recurso && $data->getTipo()){
            if ($data->getTipo()->getNombre() === TipoRecurso::REFERENCE_FILE){
                $form->add('referenciaFile', FileType::class, [
                    'label' => false,
                    'required' => false,
                    'mapped'=>false,
                    'attr' => ['placeholder' => 'Escoja un Archivo'],
                    'constraints' => [
                        new File([
                            'maxSize' => '500M',
                            'mimeTypes' => $this->mimeTypesFile,
                            'mimeTypesMessage' => 'Por favor seleccione un archivo válido',
                            'maxSizeMessage' => 'El archivo es muy grande ({{ size }} {{ suffix }}). Tamaño máximo permitido {{ limit }} {{ suffix }}.'
                        ])
                    ]
                ])
               ->add('referencia', TextType::class, [
                    'label' => false,
                    'empty_data' => '',
                    'row_attr' => ['style' => 'display:none'],
                    'attr' => ['placeholder' => 'URL']
                ]);
            } else {
                $form->add('referencia', TextType::class, [
                    'label' => false,
                    'empty_data' => '',
                    'attr' => ['placeholder' => 'URL'],
                ])
                    ->add('referenciaFile', FileType::class, [
                        'label' => false,
                        'mapped'=>false,
                        'attr' => ['placeholder' => 'Escoja un Archivo'],
                        'row_attr' => ['style' => 'display:none; position:absolute; z-index:-1'],
                        'required' => false,
                        'constraints' => [
                            new File([
                                'mimeTypes' => $this->mimeTypesFile,
                                'mimeTypesMessage' => 'Por favor seleccione un archivo válido',
                                'maxSize' => '500M',
                                'maxSizeMessage' => 'El archivo es muy grande ({{ size }} {{ suffix }}). Tamaño máximo permitido {{ limit }} {{ suffix }}.'
                            ])
                        ]
                    ])
                ;
            }
        } else {
            $form->add('referencia', TextType::class, [
                'label' => false,
                'empty_data' => '',
                'row_attr' => ['style' => 'display:none'],
                'attr' => ['placeholder' => 'URL']
            ])
                ->add('referenciaFile', FileType::class, [
                    'label' => false,
                    'mapped'=>false,
                    'attr' => ['placeholder' => 'Escoja un Archivo'],
                    'row_attr' => ['style' => 'display:none; position:absolute; z-index:-1'],
                    'required' => false,
                    'constraints' => [
                        new File([
                            'mimeTypes' => $this->mimeTypesFile,
                            'mimeTypesMessage' => 'Por favor seleccione un archivo válido',
                            'maxSize' => '500M',
                            'maxSizeMessage' => 'El archivo es muy grande ({{ size }} {{ suffix }}). Tamaño máximo permitido {{ limit }} {{ suffix }}.'
                        ])
                    ]
                ]);
        }
    }
}
