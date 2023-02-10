<?php

namespace App\Form\EventListener;
use App\Entity\Provincia;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;


class AddProvinciaFieldListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit'
        ];
    }

    private function addProvinciaForm($form, $provincia = null)
    {
        $formOptions = [
            'class' => Provincia::class,
            'placeholder' => 'Seleccione una provincia',
            'mapped' => false,
            'choice_label' => 'nombre',
            'label' => false
        ];
        if ($provincia) {
            $formOptions['data'] = $provincia;
        }
        $form->add('provincia', EntityType::class, $formOptions);
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
        $provincia = ($canton) ? $canton->getProvincia() : null;
        $this->addProvinciaForm($form, $provincia);
    }
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $this->addProvinciaForm($form);
    }
}
