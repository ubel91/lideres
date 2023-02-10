<?php

namespace App\Form;

use App\Entity\Estudiantes;
use App\Entity\GradoEscolar;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class EstudianteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('fecha_nacimiento', DateTimeType::class, [
            //     'widget' => 'single_text',
            //     'html5' => false,
            //     'format' => 'dd/MM/yyyy',
            //     'attr' => ['class' => 'js-datepicker'],
            // ])
            // ->add('nombre_representante', TextType::class, ['label' => false, 'attr' => ['placeholder' => 'Nombre Representante']])
            // ->add('primer_apellido_representante', TextType::class, ['label' => false, 'attr' => ['placeholder' => 'Primer Apellido Representante']])
            // ->add('segundo_apellido_representante', TextType::class, ['label' => false, 'attr' => ['placeholder' => 'Segundo Apellido Representante']])
            // ->add('celular', TelType::class, ['label' => false, 'attr' => ['placeholder' => 'Teléfono Celular']])
            ->add('grado', EntityType::class, [
                    'label' => 'Grado escolar',
                    'placeholder' => '--Seleccione--',
                    'class' => GradoEscolar::class,
                    'choice_label' => 'nombre'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Estudiantes::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'estudiante_item',
        ]);
    }
}
