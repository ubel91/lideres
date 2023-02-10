<?php

namespace App\Form;

use App\Entity\Catalogo;
use App\Entity\Libro;
use App\Form\EventListener\AddCategoriaFieldListener;
use App\Form\EventListener\AddSubCategoriaFieldListener;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class LibroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, ['label' => false, 'attr' => ['placeholder' => 'Título del Libro']])
            ->add('catalogo', EntityType::class, [
                'class' => Catalogo::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Escoja un Catálogo',
                'label' => false,
                'choice_value' => function ($choice) {
                    if ($choice)
                        return $choice->getId();
                }
            ])
            ->addEventSubscriber(new AddCategoriaFieldListener())
            ->addEventSubscriber(new AddSubCategoriaFieldListener())
            ->add('portada', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Seleccione una portada'],
                'help_attr' => ['style' => 'font-size:0.9rem'],
                'help' => '<span class="fas fa-exclamation-circle mr-1" style="font-size: 0.9rem; color: #17a2b8; background-color: #fff;"></span> Solo formatos de imagen válidos: .png, .jpeg, etc',
                'help_html' => true,
                'constraints' => [
                    new Image([
                        'maxSize' => '50M',
                        'mimeTypesMessage' => 'Formato de imagen no válido',
                        'maxSizeMessage' => 'Este archivo es muy grande ({{ size }} {{ suffix }}). Se permite un máximo de {{ limit }} {{ suffix }}.'
                    ])
                ]
            ])
            ->add('book_link', UrlType::class, [
                'label' => false,
                'property_path' => 'book_link',
                'default_protocol' => 'https',
                'attr' => ['placeholder' => 'Enlace'],
                'help_attr' => ['style' => 'font-size:0.9rem'],
                'help' => '<span class="fas fa-exclamation-circle mr-1" style="font-size: 0.9rem; color: #bd2130; background-color: #fff;"></span> Los enlaces solo serán visibles cuando escoja la opción <mark>Activar</mark>',
                'help_html' => true,
                'required' => false
            ])
            ->add('use_book_link', CheckboxType::class, [
                'label' => 'Activar',
                'label_attr' => ['class' => 'custom-control-label'],
                'required' => false
            ])
            ->add('code', TextareaType::class, [
                'label' => false,
                'property_path' => 'code',
                'attr' => ['placeholder' => 'Incrustar código'],
                'help_attr' => ['style' => 'font-size:0.9rem'],
                'help' => '<span class="fas fa-exclamation-circle mr-1" style="font-size: 0.9rem; color: #bd2130; background-color: #fff;"></span>Al incrustar el código se mostrará un nuevo texto en la parte inferior de la portada de los textos activados. Para activar escoja la opción <mark>Activar</mark>',
                'help_html' => true,
                'required' => false
            ])
            ->add('solucionario', TextareaType::class, [
                'label' => false,
                'property_path' => 'solucionario',
                'attr' => ['placeholder' => 'Solucionario'],
                'help_html' => true,
                'required' => false
            ])
            ->add('use_code', CheckboxType::class, [
                'label' => 'Activar',
                'label_attr' => ['class' => 'custom-control-label'],
                'required' => false
            ])
            ->add('paraDocente', CheckboxType::class, [
                'label' => 'Docentes',
                'label_attr' => ['class' => 'custom-control-label'],
                'required' => false
            ])
            ->add('paraEstudiante', CheckboxType::class, [
                'label' => 'Estudiantes',
                'label_attr' => ['class' => 'custom-control-label'],
                'required' => false
            ])

            ->add('registrar', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Libro::class,
        ]);
    }
}
