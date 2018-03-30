<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class BookingController {

    public function indexAction() {
        return new Response('Mon Hello world !');
    }

}
