<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use APProjet4\BookingBundle\Entity\User;
/*use APProjet4\BookingBundle\Utils\NbAndTotal;*/
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class RecapAndBookingController extends Controller {

    /**
     * Affichage page récapitulative et saisie de l'adresse mail 
     * 
     * @param Request $request
     * @return type
     * @throws NotFoundHttpException
     */
    public function showRecapAction(Request $request) {
        $session = $request->getSession();

        //Récupération en session
        $booking = $session->get('booking');
        $id = $session->get('event_id');


        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }

        // On récupère le service
        $nb = $this->container->get('nb.and.total.system');
        $nbPerType = $nb->getNbPerType($booking->getTickets());
        $total = $nb->getTotalAmount($nbPerType, $booking->getFullDay());
        
        $this->container->get('js.vars')->routes = [
            'url_recap' => $this->generateUrl('approjet4_booking_postEmailAndBooking'),
            ];
        
        $this->container->get('js.vars')->trans = [
            'validityEmail' =>$this->get('translator')->trans('detailsForm.invalid'),
            'fields' =>$this->get('translator')->trans('contactDetails.fields'),
            'identical' =>$this->get('translator')->trans('recap.mail.identical'),
            'registered' =>$this->get('translator')->trans('recap.mail.registered'),
            ];
        
        $this->container->get('js.vars')->data = [
            'nbTickets' => $booking->getNbTickets(),
            ];
        
        return $this->render('APProjet4BookingBundle:Booking:recap.html.twig', [
                    'id' => $id,
                    'event' => $event,
                    'booking' => $booking,
                    'nbType' => $nbPerType,
                    'tickets' => $booking->getTickets(),
                    'total' => $total
        ]);
    }

    /**
     * Enregistrement email, booking et tickets en base de données
     * 
     * @param Request $request
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function postEmailAndBookingAction(Request $request) {

        //Récupération de la session
        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();

        //Récupèration des valeurs en session
        $booking = $session->get('booking');
        $event_id = $session->get('event_id');

        //Création de l'entité utilisateur
        $user = new User();

        //Récupération de l'adresse mail saisie      
        $email = $request->get('email');

        //Génération d'un code random unique pour le code commande 
        $bytes = (random_bytes(3));

        //Renseignement des valeurs
        $booking->setEmail($email);
        $booking->setOrderCode(date('Hym-d').bin2hex($bytes));
        $user->setEmail($email);

        if (null === $booking) {
            throw new NotFoundHttpException("La commande demandée n'existe pas.");
        }
        //Récupération de l'entité Event
        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($event_id);

        //Si event existe
        if ($event) {
            //Liaison de l'évènement à la commande
            $booking->setEvent($event);
        } else {
            $em->persist($booking->getEvent());
        }

        //On persiste les entités booking et user 
        $em->persist($booking);
        $em->persist($user);
        $em->flush();

        //Retourner une réponse json
        $response = [
            'success' => true
        ];
        return new JsonResponse($response);
    }

}
