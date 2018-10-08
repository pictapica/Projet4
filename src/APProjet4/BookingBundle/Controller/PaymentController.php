<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller {
    
    /**
     * Retourne le montant total en euro
     * 
     * @param type $nbPerType
     * @param type $isFullDay
     * @return int
     */
    private function getTotal($nbPerType, $isFullDay) {
        return $nbPerType['normal'] * ($isFullDay ? 16 : 8) + $nbPerType['reduct'] * ($isFullDay ? 10 : 5) + $nbPerType['child'] * ($isFullDay ? 8 : 4) + $nbPerType['senior'] * ($isFullDay ? 12 : 6);
    }

    /**
     * Retourne le ombre de tickets par tarif  
     * 
     * @param type $tickets
     * @return int
     */
    private function getNbPerType($tickets) {
        $ret = [
            'normal' => 0,
            'reduct' => 0,
            'child' => 0,
            'senior' => 0,
        ];

        foreach ($tickets as $ticket) {
            $ret[$ticket->getFaretype()] ++;
        }
        return $ret;
    }
    
    /**
     * Enregistrement Paiement
     * 
     * @param Request $request
     * @return type
     * @throws NotFoundHttpException
     */
    public function validatePaymentAction(Request $request) {
        //TO do toto
        $session = $request->getSession();
        $order = $session->get('booking');

        //Récupèration du code commande : orderCode
        $orderCode = $order->getOrderCode();

        //Récupèration de la clé stripe
        $stripeToken = $request->get('stripeToken');

        //On vérifie l'existence de stripeToken
        if ($stripeToken == null) {
            $session->getFlashBag()->add('info', 'Oups, un problème est survenu lors de votre paiement !');
            return $this->redirectToRoute('approjet4_booking_recap');
        }
        //Si tout s'est bien passé,
        //Récupération de la commande déjà créée
        $BookingRepo = $this->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Booking');
        $booking = $BookingRepo->findOneByOrderCode($orderCode);

        if (null === $booking) {
            throw new NotFoundHttpException("La commande demandée n'existe pas.");
        }

        //Enregistrement du numéro de paiement
        $booking->setStripeToken($stripeToken);
        //Flush de la commande pour enregistrer stripeToken en bdd 
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        //Envoi du mail de confirmation
        $this->sendBookingEmail($request);

        //Effaçage de toutes les infos session sauf la locale
        $session->remove('booking');
        $session->remove('event_id');
        $session->remove('nbTickets');
        $session->remove('visitDate');

        $session->getFlashBag()->add('info', 'Un mail vient de vous être envoyé !');

        return $this->redirectToRoute('approjet4_booking_showPayment', ['orderCode' => $orderCode]);
    }


    /**
     * Affichage page de confirmation paiement
     * 
     * @param type $orderCode
     * @return type
     * @throws NotFoundHttpException
     */
    public function showPaymentAction($orderCode) {

        //Récupération des variables de commande en Bdd pour afficher la page de confirmation
        $BookingRepo = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Booking');
        $booking = $BookingRepo->findOneByOrderCode($orderCode);

        if (null === $booking) {
            throw new NotFoundHttpException("La commande demandée n'existe pas.");
        }

        //Récupération du nombre de tickets
        $nbPerType = $this->getNbPerType($booking->getTickets());

        return $this->render('@APProjet4Booking/Booking/paymentConfirmation.html.twig', [
                    'event' => $booking->getEvent(),
                    'visitDate' => $booking->getTickets()->first()->getVisitDate(),
                    'booking' => $booking,
                    'orderCode' => $orderCode,
                    'stripeToken' => $booking->getStripeToken(),
                    'email' => $booking->getEmail(),
                    'nbType' => $nbPerType,
                    'tickets' => $booking->getTickets(),
                    'total' => $this->getTotal($nbPerType, $booking->getFullDay())
        ]);
    }


    /**
     * Envoie d'un e-mail
     * @param Request $request
     * @return type
     * @throws NotFoundHttpException
     * 
     */
    private function sendBookingEmail(Request $request) {

        $session = $request->getSession();

        //Récupération du numéro de commande en session 
        $orderCode = $session->get('booking')->getOrderCode();

        //Récupération des variables de commande en Bdd pour compléter le mail
        $BookingRepo = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Booking');
        $booking = $BookingRepo->findOneByOrderCode($orderCode);

        if (null === $booking) {
            throw new NotFoundHttpException("La commande demandée n'existe pas.");
        }

        //Récupération de l'id de l'événement
        $id = $booking->getEvent();

        //Récupération de la date de visite 
        $visitDate = $booking->getTickets()->get('visitDate');

        //Récupération de lévénement
        $EventRepo = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $EventRepo->find($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'événement demandé n'existe pas.");
        }
        //Envoie du mail avec SwiftMailer
        $message = \Swift_Message::newInstance()
                ->setSubject('Votre visite au Louvre')
                ->setFrom(['billetterie@louvre.fr' => 'Musée du Louvre'])
                ->setTo([$booking->getEmail()])
                ->setBody(
                $this->renderView('@APProjet4Booking/Booking/ticket.html.twig', [
                    'id' => $id,
                    'booking' => $booking,
                    'visitDate' => $visitDate,
                    'event' => $event,
                    'bookingEmail' => $booking->getEmail(),
                    'stripeToken' => $booking->getStripeToken(),
                    'total' => $this->getTotal($this->getNbPerType($booking->getTickets()), $booking->getFullDay()),
                    'bookingOrderCode' => $orderCode,
                    'tickets' => $booking->getTickets()->getValues(),
                        ]
                ), 'text/html');

        return $this->get('mailer')->send($message);
    }

}
