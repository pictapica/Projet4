<?php

namespace APProjet4\BookingBundle\Services;

use APProjet4\BookingBundle\Entity\Booking;
use Twig_Environment as Environment;



class BookingEmailSystem extends \Twig_Extension{

    private $mailer;
    private $twig;

    public function _construct(\Swift_Mailer $mailer, Environment $twig) {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendBookingEmail(Booking $booking) {
        
        $bookingEmail = $booking->getEmail();
        $bookingOrderCode = $booking->getOrderCode();
        $tickets = $booking->getTickets()->getValues();
        
        
        $message = \Swift_Message::newInstance()
                ->setSubject('Votre visite au Louvre')
                ->setFrom(['billetterie@louvre.fr' => 'Musée du Louvre'])
                ->setTo(['demo@demo.fr'])
                ->setBody(
                        $this->twig->render('APProjet4BookingBundle:Booking:ticket.html.twig', [
                            'bookingEmail' => $bookingEmail,
                            'bookingOrderCode' => $bookingOrderCode,
                            'tickets' => $tickets,
                            ]
                        ), 'text/html');
        
        $this->mailer->send($message);
    }

}
