<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use APProjet4\BookingBundle\Entity\Booking;
use APProjet4\BookingBundle\Entity\Ticket;
use APProjet4\BookingBundle\Form\BookingType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session;

class BookingController extends Controller {

    const MAX_TICKETS_PER_DAY = 1000;
    
    //////////////////////////////////////////////////////   
   ///////////// Affichage de la page d'accueil//////////

    public function homeAction() {
        $content = $this->get('templating')->render('APProjet4BookingBundle:Booking:home.html.twig');
        return new Response($content);
    }
    
    //////////////////////////////////////////////////////   
   ///////////Affichage de la liste des évènements/////////

    public function indexAction() {
        $repository = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Event');

        $listEvents = $repository->findAll();

        return $this->render('APProjet4BookingBundle:Booking:index.html.twig', array(
                    'listEvents' => $listEvents
        ));
    }
    
    //////////////////////////////////////////////////////   
    ////////////////Date & type de billet////////////////

    public function selectDateAction($id) {
        $repository = $this->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }

        return $this->render('APProjet4BookingBundle:Booking:selectDate.html.twig', array(
                    'event' => $event,
        ));
    }

    //////////////////////////////////////////////////////   
    ///////////////Quota par jour///////////////////////

    public function checkMaxBookingAction(Request $request) {
    //On récupère un tableau avec date availability = false qui va s'ajouter à datesDisabled
       

    //On récupère les tickets par date de visite
        $d = new \DateTime($request->get('visitDate'));
        $tickets = $this->getDoctrine()
                ->getRepository('APProjet4BookingBundle:Booking')
                ->findByVisitDate($d);
        console.log($tickets);
        //Si pas de tickets 
        if (!$tickets) {
            $response = [
                'availability' => true,
            ];
        } else { //Sinon on les compte (count($tickets) > MAX_TICKETS_PER_DAY

            if (count($tickets) > MAX_TICKETS_PER_DAY) {
                $response = [
                    'availability' => false,
                ];
            } else {
                $response = [
                    'availability' => true,
                ];
            }
        }
        echo json_encode($response);
        return new JsonResponse($response);
    }

    
    
    ////////////////Sauvegarde de la date/////////////////

    public function saveDateAction(Request $request) {

        // Récupération de la session
        $session = $request->getSession();

        if (!$request->isMethod('POST')) {
            throw new \Exception('Méthode post attendue!');
        }
        //Vérification de l'absence des paramètres date, fullday et id event, 

        if (!('visitDate') || !('isFullDay') || !('id')) {

            $response = [
                'success' => false
            ];
            return new JsonResponse($response);
        }
        //Vérification si date dispo
        //Vérification de la validité des paramètres ? isItAPastDay(), isItADisabledDay()
        //Vérification du billet journée pour la date actuelle (<14h) :  isHourPast() 


        $visitDate = $request->get('visitDate');
        $id = $request->get('id');
        $isFullDay = $request->get('isFullDay');

        // On définit une nouvelle valeur pour ces variables
        $booking_visitDate = $session->set('visitDate', $visitDate);
        $event_id = $session->set('id', $id);
        $booking_isFullDay = $session->set('isFullDay', $isFullDay);

        // On récupère le service validator??? 
        //Vérification  et <1000 billets vendus ( On doit récupérer résultat fonction countTicketsPerDay dans le 
        //repository ou dire que if ($maxBookingAction = true) ? 
        if ('checkMaxBookingAction(true)') {
//        // On renvoie une réponse success
            $response = [
                'success' => true
            ];
            return new JsonResponse($response);
        }
    }

    /*
     *   Choix du nombre de billets
     * 4  Calcul le total selon le montant de chaque billet 
     */

    public function deleteFormAction() {
        
    }

    public function ContactDetailsAction(Request $request) {
        // Récupération de la session
        $session = $request->getSession();
        
        //récupération des variables en session
        $visitDate = $session->get('visitDate');
        $id = $session->get('id');
        $isFullDay = $session->get('isFullDay');

        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($id);
        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }

        $booking = new Booking();

        $form1 = $this->createForm(BookingType::class);

        if ($request->isMethod('POST') && $form1->handleRequest($request)) {//+ is valid
            $booking->setStatus(Booking::STATUS_INPROGRESS);
            $booking->setFullDay($isFullDay);
            
            $ticket = new Ticket();
            $form1->getData();

            $this->get('session')->set('Booking', $booking);
            $this->get('session')->set('Ticket', $ticket);
            
            //Récupération des paramètres
            $lastname = $request->get('lastname');
            $firstname = $request->get('firstname');
            $dateOfBirth = $request->get('dateOfBirth');
            $country = $request->get('country');
            
            $ticket->setVisitDate($visitDate);
            $ticket->setLastname($lastname);
            $ticket->setFirstname($firstname);
            $ticket->setDateOfBirth($dateOfBirth);
            $ticket->setCountry($country);

            $response = [
                'success' => true
            ];
            return new JsonResponse($response);
        }
        return $this->render('APProjet4BookingBundle:Booking:contactDetails.html.twig', array(
                    'form1' => $form1->createView(),
                    'visitDate' => $visitDate,
                    'id' => $id,
                    'isFullDay' => $isFullDay,
                    'event' => $event));
    }

    public function showRecapAction(Request $request) {
        $session = $request->getSession();

        $visitDate = $session->get('visitDate');
        $id = $session->get('id');
        $isFullDay = $session->get('isFullDay');

        $repository = $this->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }

        return $this->render('APProjet4BookingBundle:Booking:recap.html.twig', array(
                    'visitDate' => $visitDate,
                    'id' => $id,
                    'isFullDay' => $isFullDay,
                    'event' => $event));
    }

}

/**
     *    Clic sur le champs e-mail
     * 5  Si aucun billet rempli : message d'erreur
     *    
     * 
     */
     /**
     *    Saisi du 2 ème email
     * 6  Si différent du premier : message d'erreur
      * 
     *    $confirmation.keyup(function(){
    if($(this).val() != $mdp.val()){ // si la confirmation est différente du mot de passe
        $(this).css({ // on rend le champ rouge
	    borderColor : 'red',
	    color : 'red'
        });
    }
    else{
	$(this).css({ // si tout est bon, on le rend vert
	    borderColor : 'green',
	    color : 'green'
	});
    }
});

     * 
     */
     /**
     *    Clic sur CONFIRMER
     * 7  Vérification et enregistrement de l'adresse e-mail
     *    Enregistre la commande dans le panier
     * 
     */
     /**
     *    Récupération du total en euros et du détail du nombre de billets pour chaque type de billets.
     * 8  Vérification des informations client (string, date..)
     *    
     * 
     */
     /**
     *    Commander d'autres billets 
     * 9  Enregistre les informations client 
     *    $em = $this->getDoctrine()->getManager();
            $em->persist($booking);
            $em->persist($ticket);
            $em->flush();
     * 
     */
/**
     *    Fina
     * 10  
     *    
     * 
     */
     
