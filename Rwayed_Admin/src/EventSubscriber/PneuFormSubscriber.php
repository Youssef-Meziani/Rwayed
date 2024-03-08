<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PneuFormSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $form = $event->getForm();

        $pneu = $form->getData();
        if ($form->has('photo_files')) {
            $photoFiles = $form->get('photo_files')->getData();
            if (count($photoFiles) > 5) {
                $form->get('photo_files')->addError(new FormError('You can only upload a maximum of 5 images.'));
            }
        }
    }
}
