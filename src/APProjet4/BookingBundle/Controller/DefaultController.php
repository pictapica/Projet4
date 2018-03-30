<?php

namespace APProjet4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('APProjet4BookingBundle:Default:index.html.twig');
    }
}
