<?php
// src/BookingBundle/EventSubscriber/LocaleSubscriber.php

namespace APProjet4\BookingBundle\EventSubscriber;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class LocaleSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        // On essaie de voir si la locale a été fixée dans le paramètre de routing _locale
        $locale = $request->attributes->get('_locale');
        if ($locale) {
            $request->getSession()->set('_locale', $locale);
        } else {
            // Si aucune Locale n'a été fixée explicitement dans la requête, on utilise celle de la session
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // Doit être enregistré avant le Locale listener par défaut
            KernelEvents::REQUEST => array(array('onKernelRequest', 20)),
        );
    }
}
