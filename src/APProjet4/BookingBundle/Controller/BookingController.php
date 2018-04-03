<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller{

    public function indexAction() {
        
        $content = $this->get('templating')->render('APProjet4BookingBundle:Booking:index.html.twig');
        return new Response($content);
    }

    public function newOrderAction(){
        $content = $this->get('templating')->render('APProjet4BookingBundle:Booking:newOrder.html.twig');
        return new Response($content);
    }
}
