<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use APProjet4\BookingBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContactDetailsController extends Controller {

    /**
     * Affichage de la page de saisie des informations visiteur
     * 
     * @param Request $request
     * @return type 
     * @throws NotFoundHttpException
     */
    public function showContactDetailsAction(Request $request) {
        
        // Récupération de la session
        $session = $request->getSession();

        //récupération des variables en session
        $visitDate = $session->get('visitDate');
        $booking = $session->get('booking');
        $id = $session->get('event_id');

        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($id);
        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }
        
        $this->container->get('js.vars')->routes = [
                'post_type' => $this->generateUrl('approjet4_booking_postContactDetails'),
                'recap' => $this->generateUrl('approjet4_booking_showRecap')
                ];
        
        $this->container->get('js.vars')->trans = [
            'fill' =>$this->get('translator')->trans('detailsForm.fill'),
            'deleteForm' =>$this->get('translator')->trans('detailsForm.deleteForm'),
            'fields' =>$this->get('translator')->trans('contactDetails.fields'),
            'birth' =>$this->get('translator')->trans('selectDate.container.birth'),
        ];
        
        $this->container->get('js.vars')->data = [
            'visitDate' =>$visitDate,
        ];
        
        return $this->render('APProjet4BookingBundle:Booking:contactDetails.html.twig', [
                    'id' => $id,
                    'visitDate' => $visitDate,
                    'booking' => $booking,
                    'fullDay' => $booking->getFullDay(),
                    'event' => $event,
        ]);
    }

    /**
     * Enregistrement des informations visiteur
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function postContactDetailsAction(Request $request) {
        // Récupération de la session
        $session = $request->getSession();

        $visit = $session->get('visitDate');
        $visitDate = date_create($visit);
        $booking = $session->get('booking');
        // On appelle le services
        $fare = $this->container->get('ticket.fare.system');
        
        $ticket = new Ticket();

        $tickets = $request->get('tickets');
        foreach ($tickets as $ticket) {   
            $t = new Ticket();
            if ($ticket['fareType'] == ""){
               $ticket['fareType'] = $fare->getTicketFareType($visitDate, $ticket['dateOfBirth']);
            }
            $t->setFareType($ticket['fareType']);
            $t->setFirstname($ticket['firstname']);
            $t->setLastname($ticket['lastname']);
            $t->setDateOfBirth(date_create($ticket['dateOfBirth']));
            $t->setCountry($ticket['country']);
            $t->setBooking($booking);
            $t->setVisitDate($visitDate);

            $booking->addTicket($t);
        }

        //On compte le nombre total de billets
        $nbTickets = count($tickets);

        //On renseigne les valeurs 
        $booking->setNbTickets($nbTickets);

        //On enregistre les valeurs en session
        $session->set('booking', $booking);
        $session->set('nbTickets', $nbTickets);

        //Si tout est bon on retourne un réponse success
        $response = [
            'success' => true
        ];
        return new JsonResponse($response);
    }
  
}
