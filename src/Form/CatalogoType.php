<?php

namespace App\Form;

use App\Entity\Catalogo;
use App\Entity\Etapa;
use App\Entity\GradoEscolar;
use App\Entity\Materia;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CatalogoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
//            ->add('libros')
            ->add('etapas', EntityType::class, [
                'class' => Etapa::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Escoja una Etapa',
                'label' => false,
//                'choice_value' => function ($choice){
//                    if ($choice){
//                        return $choice->getId();
//                    }
//                }
            ])
            ->add('materias', EntityType::class, [
                'class' => Materia::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Escoja una Materia',
                'label' => false,
//                'choice_value' => function ($choice){
//                    if ($choice)
//                        return $choice->getId();
//                }
            ])
            ->add('grados', EntityType::class, [
                'class' => GradoEscolar::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Escoja un Grado',
                'label' => false,
//                'choice_value' => function ($choice){
//                    if ($choice)
//                        return $choice->getId();
//                }
            ])
            ->add('registrar', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Catalogo::class,
        ]);
    }
//    /**
//     * @inheritDoc
//     */
//    public function mapFormsToData($forms, &$viewData)
//    {
//        $data = new Catalogo();
//        $forms = iterator_to_array($forms);
//        // "etapas" is the name of the field in the CatalogoType form
//        $etapas = $forms['etapas'];
//
//        foreach ($etapas as $etapa) {
//            // $data should be a Contact object
//            $data->addEtapa($etapa);
//        }
//
//        $materias = $forms['materias'];
//
//        foreach ($materias as $materia) {
//            // $data should be a Contact object
//            $data->addMateria($materia);
//        }
//        $grados = $forms['grados'];
//
//        foreach ($grados as $grado) {
//            // $data should be a Contact object
//            $data->addGrado($grado);
//        }
//
//        $nombre = $forms['nombre'];
//
//        $data->setNombre($nombre);
//
//
//        // ...Map remaining form fields to $data
//    }

//    /**
//     * @inheritDoc
//     */
//    public function mapDataToForms($viewData, $forms)
//    {
//        // TODO: Implement mapDataToForms() method.
//    }
}
