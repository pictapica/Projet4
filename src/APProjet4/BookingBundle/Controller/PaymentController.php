<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use APProjet4\BookingBundle\Utils\NbAndTotal;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller {

    /**
     * Enregistrement Paiement
     * 
     * @param Request $request
     * @return type
     * @throws NotFoundHttpException
     */
    public function validatePaymentAction(Request $request) {
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
        $BookingRepo = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Booking');
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

        $nbPerType = NbAndTotal::getNbPerType($booking->getTickets());
        $total = NbAndTotal::getTotalAmount($nbPerType, $booking->getFullDay());

        return $this->render('@APProjet4Booking/Booking/paymentConfirmation.html.twig', [
                    'event' => $booking->getEvent(),
                    'visitDate' => $booking->getTickets()->first()->getVisitDate(),
                    'booking' => $booking,
                    'orderCode' => $orderCode,
                    'stripeToken' => $booking->getStripeToken(),
                    'email' => $booking->getEmail(),
                    'nbType' => $nbPerType,
                    'tickets' => $booking->getTickets(),
                    'total' => $total
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
        $visitDate = $booking->getTickets()->first()->getVisitDate();

        //Récupération de l'événement
        $EventRepo = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $EventRepo->find($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'événement demandé n'existe pas.");
        }

        $nbPerType = NbAndTotal::getNbPerType($booking->getTickets());
        $total = NbAndTotal::getTotalAmount($nbPerType, $booking->getFullDay());

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
                    'total' => $total,
                    'bookingOrderCode' => $orderCode,
                    'tickets' => $booking->getTickets()->getValues(),
                        ]
                ), 'text/html');

        return $this->get('mailer')->send($message);
    }

}
