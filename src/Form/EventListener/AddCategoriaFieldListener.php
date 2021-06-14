<?php

namespace App\Form\EventListener;
use App\Entity\Categoria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;


class AddCategoriaFieldListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit'
        ];
    }

    private function addCategoriaForm($form, $categoria = null)
    {
        $formOptions = [
            'class' => Categoria::class,
            'placeholder' => 'Seleccione una categoría',
            'mapped' => false,
            'choice_label' => 'nombre',
            'label' => false
        ];
        if ($categoria) {
            $formOptions['data'] = $categoria;
        }
        $form->add('categoria', EntityType::class, $formOptions);
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
        $categoria = ($subCategoria) ? $subCategoria->getCategoria() : null;
        $this->addCategoriaForm($form, $categoria);
    }
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $this->addCategoriaForm($form);
    }
}
