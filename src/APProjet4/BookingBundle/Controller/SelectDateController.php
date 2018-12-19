<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use APProjet4\BookingBundle\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Exception;

class SelectDateController extends Controller {

    /**
     * Affichage choix Date & type de billet
     * @param type $id
     * @return type
     * @throws NotFoundHttpException
     */
    public function showSelectDateAction($id) {

        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->findOneById($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }

        $validDate = $this->container->get('disabled.dates.system');
        //Tableau des dates à griser dans le datepicker
        $arrayDatesDisabled = $validDate->mergeDatesDisabled($id);
        
        $this->container->get('js.vars')->routes = [
                'post_date' => $this->generateUrl('approjet4_booking_pick_a_date'),
                'contact_details' => $this->generateUrl('approjet4_booking_showContactDetails')
                ];
        
        $this->container->get('js.vars')->trans = [
            'quatorze' =>$this->get('translator')->trans('selectDate.container.14h'),
            'dixhuit' =>$this->get('translator')->trans('selectDate.container.18h'),
            'opening2' => $this->get('translator')->trans('opening2'),
            'opening3' => $this->get('translator')->trans('opening3'),
            'opening4' => $this->get('translator')->trans('opening4'),
            'warning1' => $this->get('translator')->trans('selectDate.container.warning1'),
            'warning2' => $this->get('translator')->trans('selectDate.container.warning2'),
            'warning3' => $this->get('translator')->trans('selectDate.container.warning3')
        ];
        $this->container->get('js.vars')->local = 'fr';
                
        $this->container->get('js.vars')->viewData = [
            'disabledDates' => $arrayDatesDisabled,
            'eventId' => $id,
                ];
        
        return $this->render('APProjet4BookingBundle:Booking:selectDate.html.twig', [
                    'event' => $event,
                    
//                    'arrayDatesDisabled' => json_encode($arrayDatesDisabled),
        ]);
    }

    /**
     * Sauvegarde, vérifie la date sélectionnée et l'enregistre en session
     * @param Request $request
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function saveDateAction(Request $request) {

        // Récupération de la session
        $session = $request->getSession();

        //Vérification de l'absence des paramètres date, fullday et id event, 
        if (!('visitDate') || !('fullDay') || !('event_id')) {

            $response = [
                'success' => false,
                'message' => 'Merci de sélectionner une date valide !'
            ];
            return new JsonResponse($response);
        }
        //On récupère les variables
        $visitDate = $request->get('visitDate');
        $event_id = $request->get('event_id');
        $fullDay = $request->get('fullDay');

        //Création d'une nouvelle commande
        $booking = new Booking();

        //On récupère l'événement
        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($event_id);

        if (null === $event) {
            $response = [
                'success' => false,
                'message' => "L'évènement d'id " . $event_id . " n'existe pas."
            ];
            return new JsonResponse($response);
        }
        //On vérifie que ce n'est pas un jour où il est impossible de commander avec le service controlVisitDateSystem
        // On récupère le service
        $control = $this->container->get('control.visitdate.system');

        try {
            $control->controlVisitDate($visitDate, $event_id, $fullDay);
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            return new JsonResponse($response);
        }


        //On renseigne les valeurs 
        $booking->setFullDay($fullDay);

        //On enregistre les valeurs en session
        $session->set('booking', $booking);
        $session->set('visitDate', $visitDate);

        //On définit une nouvelle valeur pour visitDate et on l'ajoute à la session
        $session->set('event_id', $event_id);

        // On renvoie une réponse success
        $response = [
            'success' => true
        ];
        return new JsonResponse($response);
    }

}
