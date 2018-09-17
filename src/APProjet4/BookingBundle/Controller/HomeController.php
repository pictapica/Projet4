<?php

//src/APProjet4/BookingBundle/Controller/HomeController.php

namespace APProjet4\BookingBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller {


    ////////////////////////////////////////////////////////////////////////////
    ///////////// Affichage de la page d'accueil////////////////////////////////

    public function homeAction() {
        
        $content = $this->get('templating')->render('APProjet4BookingBundle:Booking:home.html.twig');
        
        return new Response($content);
    }
    
    ////////////////////////////////////////////////////////////////////////////
    /////////// Affichage de la liste des évènements////////////////////////////

    public function indexAction() {
        $EventRepository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $listEvents = $EventRepository->findAll();
        
        $nbTickets = 0;
        $tickets = 0;

        return $this->render('APProjet4BookingBundle:Booking:index.html.twig', [
                    'listEvents' => $listEvents,
                    'nbTickets' => $nbTickets,
                    'tickets' =>$tickets,
        ]);
    }
}
