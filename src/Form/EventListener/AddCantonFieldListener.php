<?php

namespace App\Form\EventListener;

use App\Entity\Canton;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;


class AddCantonFieldListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit'
        ];
    }

    private function addCantonForm($form, $id)
    {
        $formOptions = [
            'class' => Canton::class,
            'query_builder' => function(EntityRepository $repository) use ($id){
                return $repository->createQueryBuilder('canton')
                    ->innerJoin('canton.provincia', 'provincia')
                    ->where('provincia.id = :provincia')
                    ->setParameter('provincia', $id)
                    ;
            },
            'label' => false,
            'placeholder' => 'Debe seleccionar una Provincia primero...',
            'choice_label' => 'nombre',
        ];

        $form->add('canton', EntityType::class, $formOptions);
    }
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data){
            return;
        }
        $accesor = PropertyAccess::createPropertyAccessor();

        $canton = $accesor->getValue($data, 'canton');
        $id = ($canton) ? $canton->getProvincia()->getId() : null;
        $this->addCantonForm($form, $id);
    }
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $id = array_key_exists('provincia', $data) ? $data['provincia'] : null;
        $this->addCantonForm($form, $id);
    }
}
