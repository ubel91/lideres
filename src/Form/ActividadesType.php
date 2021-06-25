<?php

namespace App\Form;

use App\Entity\Actividades;
use App\Entity\Unidad;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActividadesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('actividad_nombre', TextType::class, [
                'label'=>false,
                'property_path' => 'nombre',
                'attr' => ['placeholder'=>'Nombre de la Actividad']
            ])
            ->add('pagina', IntegerType::class, [
                'label'=>false,
                'help_attr' => ['style' => 'font-size:0.9rem'],
                'help' => '<span class="fas fa-exclamation-circle mr-1" style="font-size: 0.9rem; color: #17a2b8; background-color: #fff;"></span> El conteo de páginas comienzan desde 0',
                'help_html' => true,
                'attr' => ['placeholder'=>'Página para la actividad'],
                'scale'=> 0
            ])
            ->add('actividad_url', UrlType::class, [
                'label'=>false,
                'property_path' => 'url',
                'default_protocol'=>'https',
                'attr' => ['placeholder'=>'URL de Actividad Genially'],
                'required' => false,
            ])
            ->add('soundCloud', UrlType::class, [
                'label'=>false,
                'property_path' => 'soundCloud',
                'default_protocol'=>'https',
                'attr' => ['placeholder'=>'URL para SoundCloud'],
                'required' => false,
            ])
            ->add('id', HiddenType::class, [
                'required'=>false,
                'mapped'=>false
            ])
            ->add('unidad', EntityType::class, [
                'class' => Unidad::class,
                'placeholder' => 'Seleccione una Unidad',
                'choice_label' => 'nombre',
                'row_attr' => ['style' => 'display : none']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Actividades::class,
        ]);
    }
}
