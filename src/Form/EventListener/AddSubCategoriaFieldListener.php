<?php

namespace App\Form\EventListener;

use App\Entity\SubCategoria;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;


class AddSubCategoriaFieldListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit'
        ];
    }

    private function addSubCategoriaForm($form, $id)
    {
        $formOptions = [
            'class' => SubCategoria::class,
            'query_builder' => function(EntityRepository $repository) use ($id){
                return $repository->createQueryBuilder('subCategoria')
                    ->innerJoin('subCategoria.categoria', 'categoria')
                    ->where('categoria.id = :categoria')
                    ->setParameter('categoria', $id)
                    ;
            },
            'label' => false,
            'placeholder' => 'Debe seleccionar una Categoría primero...',
            'choice_label' => 'nombre'
        ];

        $form->add('subCategoria', EntityType::class, $formOptions);
    }
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data){
            return;
        }
        $accesor = PropertyAccess::createPropertyAccessor();

        $subCategoria = $accesor->getValue($data, 'subCategoria');
        $id = ($subCategoria) ? $subCategoria->getCategoria()->getId() : null;
        $this->addSubCategoriaForm($form, $id);
    }
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $id = array_key_exists('categoria', $data) ? $data['categoria'] : null;
        $this->addSubCategoriaForm($form, $id);
    }
}
