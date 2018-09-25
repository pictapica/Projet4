<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use APProjet4\BookingBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class ContactDetailsController extends Controller {

    const MINCHILD = 4;
    const MAXCHILD = 12;
    const MAXADULT = 59;
    const MINSENIOR = 60;
    const MAXSENIOR = 120;
    
    ////////////////////////////////////////////////////////////////////////////
    ///////////Affichage de la page de saisie des informations visiteur/////////

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

        return $this->render('APProjet4BookingBundle:Booking:contactDetails.html.twig', [
                    'id' => $id,
                    'visitDate' => $visitDate,
                    'booking' => $booking,
                    'fullDay' => $booking->getFullDay(),
                    'event' => $event,
        ]);
    }
    ////////////////////////////////////////////////////////////////////////////
    /////////////////Enregistrement des informations visiteur///////////////////////

    public function postContactDetailsAction(Request $request) {
        // Récupération de la session
        $session = $request->getSession();

        $visit = $session->get('visitDate');

        $visitDate = date_create($visit);
        $booking = $session->get('booking');
        $ticket = new Ticket();
        
        $tickets = $request->get('tickets');
        foreach ($tickets as $ticket) {
            $t = new Ticket();
            $t->setFareType($ticket['fareType']);
            $t->setFirstname($ticket['firstname']);
            $t->setLastname($ticket['lastname']);
            $t->setDateOfBirth(date_create($ticket['dateOfBirth']));
            $t->setCountry($ticket['country']);
            $t->setBooking($booking);
            $t->setVisitDate($visitDate);
                if (($this->checkAgeAndFare($visitDate, (date_create($ticket['dateOfBirth'])), ($ticket['fareType'])))){
                  throw new NotFoundHttpException("Attention, votre date de naissance ne correspond pas au tarif choisi");  
                }
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
        
    ////////////////////////////////////////////////////////////////////////////
    ///////////////////////////Vérification du tarif choisi/////////////////////
    private function getAge($visitDate, $dateOfBirth){
        $datediff = $visitDate->diff($dateOfBirth);
        $age = $datediff->format('%y');
        return $age;
    }
    
    private function IsChild($age){
        if (($age >= self::MINCHILD) && ($age < self::MAXCHILD)){
            return false;
        }
        return true;
    }
    
    private function IsNormal($age){
        if (($age >= self::MAXCHILD) && ($age <= self::MAXADULT)){
            return false;
        }
        return true;
    }
    
    private function isSenior($age){
        if (($age >= self::MINSENIOR) && ($age <= self::MAXSENIOR)){
            return false;
        }
        return true;
    }
        
    private function checkAgeAndFare($visitDate, $dateOfBirth, $fareType) {
        $age = $this->getAge($visitDate, $dateOfBirth);   
        switch ($fareType) {
            case 'child': 
                if (($this->isChild($age))){
                    throw new NotFoundHttpException("Le tarif enfant s'applique "
                            . "entre 4 et 12 ans. Les enfants de moins de 4 ans n'ont pas besoin de billet d'entrée");//Verifie age entre 4 et 12
                }return false;
                
            case 'normal':
                if(($this->isNormal($age))){
                    throw new NotFoundHttpException("Le tarif normal s'applique entre 12 et 59 ans");//Vérifie age entre 12 et <60
                }return false;
                
            case 'senior':
                if(($this->isSenior($age))){
                    throw new NotFoundHttpException("Le tarif sénior s'applique dès 60 ans");// Vérifie age >60
                }return false;
                
            case 'reduct':
                return false;
            default:
               throw new NotFoundHttpException("Il semble qu'il y ait eu un problème. Merci de vérifier les informations saisies.");
        }
    }
}