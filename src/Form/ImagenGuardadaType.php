<?php

namespace App\Form;

use App\Entity\ImagenGuardada;
use App\Entity\Unidad;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImagenGuardadaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('archivo', FileType::class, [
                'mapped'=>false,
                'required'=>false
            ])
            ->add('mimeType')
            ->add('pagina')
            ->add('unidad', EntityType::class, [
                'class' => Unidad::class,
                'choice_label' => 'nombre'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ImagenGuardada::class,
        ]);
    }
}
