<?php 

//src/APProjet4/BookingBundle/Controller/SessionController.php

namespace APProjet4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SessionController extends Controller {

////////////////////////////////////////////////////////////////////////////////
///////////////////Choix de la langue //////////////////////////////////////////
    
public function setLocaleAction(Request $request)
{
    $session = $request->getSession();
    
    //On récupère la locale
    $locale = $request->get('locale');
        
    //On enregistre la locale en session
    $session->set('_locale', $locale);
            
    //on redirige vers la page d'origine
    $url = $request->headers->get('referer');
//    if (empty($url)) {
//        $url = $this->container->get('router')->generate('index');
//    }
     return new RedirectResponse($url);
}
}
