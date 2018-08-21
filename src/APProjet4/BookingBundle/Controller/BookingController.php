<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use APProjet4\BookingBundle\Entity\Booking;
use APProjet4\BookingBundle\Entity\Ticket;
use APProjet4\BookingBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class BookingController extends Controller {

    const MAX_TICKETS_PER_DAY = 1000;
    

    ////////////////////////////////////////////////////////////////////////////
    ///////////// Affichage de la page d'accueil////////////////////////////////

    public function homeAction() {
        
        $content = $this->get('templating')->render('APProjet4BookingBundle:Booking:home.html.twig');
        
        return new Response($content);
    }
    
    ////////////////////////////////////////////////////////////////////////////
    /////////// Affichage de la liste des évènements////////////////////////////

    public function indexAction(Request $request) {
        $EventRepository = $this->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Event');

        $listEvents = $EventRepository->findAll();
        
        $nbTickets = 0;
        $tickets = 0;

        return $this->render('APProjet4BookingBundle:Booking:index.html.twig', [
                    'listEvents' => $listEvents,
                    'nbTickets' => $nbTickets,
                    'tickets' =>$tickets,
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////  
    //////////////// Date & type de billet//////////////////////////////////////

    public function showSelectDateAction($id) {
        $repository = $this->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Event');

        $event = $repository->find($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }
        //Récupération des dates complètes dans $disableDate

        return $this->render('APProjet4BookingBundle:Booking:selectDate.html.twig', [
                    'event' => $event,
        ]);
    }

    //////////////////////////////////////////////////////////////////////////// 
    //////////////////////////////Quota par jour////////////////////////////////

    private function checkMaxBookingAction(Request $request) {
        //TO DO comment on récupère un tableau avec date availability = false qui va s'ajouter à datesDisabled
        //On récupère les tickets par date de visite
        $d = new \DateTime($request->get('visitDate'));
        $tickets = $this->getDoctrine()
                ->getRepository('APProjet4BookingBundle:Booking')
                ->findByVisitDate($d);

        //Si j'ai des tickets et s'il y en a plus de 1000
        if ($tickets && count($tickets)> MAX_TICKETS_PER_DAY){
            return false;
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////Sauvegarde de la date///////////////////////////

    public function saveDateAction(Request $request) {

        // Récupération de la session
        $session = $request->getSession();

        //Vérification de l'absence des paramètres date, fullday et id event, 
        if (!('visitDate') || !('fullDay') || !('id')) {

            $response = [
                'success' => false
            ];
            return new JsonResponse($response);
        }
        //Vérification si date dispo
        //Vérification de la validité des paramètres ? isItAPastDay(), isItADisabledDay()
        //Vérification du billet journée pour la date actuelle (<14h) :  isHourPast() 
       
       
        //On met à jour les variables
        $visitDate = $request->get('visitDate');
        $id = $request->get('id');
        $fullDay = $request->get('fullDay');
        
        $booking = new Booking();
 
        //On renseigne les valeurs 
        $booking->setFullDay($fullDay);
        $booking->setEvent($id);
        
        //On enregistre les valeurs en session
        $session->set('booking', $booking);

        // TO CLEAN ///// On définit une nouvelle valeur pour ces variables et on l'ajoute à la session
        $session->set('visitDate', $visitDate);
        $session->set('id', $id);
        $session->set('fullDay', $fullDay);
   
        //Vérification  si <1000 billets vendus 
        if ('checkMaxBookingAction(true)') {
            // On renvoie une réponse success
            $response = [
                'success' => true
            ];
            return new JsonResponse($response);
        }
    }

    
    ////////////////////////////////////////////////////////////////////////////
    ///////////Affichage de la page de saisie des informations visiteur/////////

    public function showContactDetailsAction(Request $request) {
        // Récupération de la session
        $session = $request->getSession();
        //récupération des variables en session
        $visitDate = $session->get('visitDate');
        $booking = $session->get('booking');
        $id = $session->get('id');
        $fullDay = $session->get('fullDay');

        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($id);
        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }
        return $this->render('APProjet4BookingBundle:Booking:contactDetails.html.twig', [
                    'id' => $id,
                    'visitDate' => $visitDate,
                    'booking' => $booking,
                    'fullDay' => $fullDay,
                    'event' => $event,
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////
    ///////////////////////////Vérification du tarif choisi/////////////////////
    
    public function ckeckFareAction($dateOfBirth){
        //now - 4ans
        //if (dateOfBirth > now - 4ans)
        //->true
        //if (dateOfBirth > now - 12 ans)
        //->true
        //if (dateOfBirth > now - 60 ans)
        //->true
        //else error
                
//        $now = new \DateTime();
//        $minus = date_diff($now, $dateOfBirth);
//        $age = $minus->format('%y');
        
        
        if ($dateOfBirth > (mktime(0, 0, 0, date("m"),   date("d"),   date("Y")-4))){
            return true;
        }if ($dateOfBirth > (mktime(0, 0, 0, date("m"),   date("d"),   date("Y")-12))){
            return true; 
        }if ($dateOfBirth < (mktime(0, 0, 0, date("m"),   date("d"),   date("Y")-60))){
            return true;
        }else {
            return false;}
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    /////////////////Sauvegarde des informations visiteur///////////////////////

   public function postContactDetailsAction(Request $request) {
        // Récupération de la session
        $session = $request->getSession();
        
        $fullDay = $session->get('fullDay');
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
                $booking->addTicket($t);
            }
        //On compte le nombre total de billets
        $nbTickets = count($tickets);
            
        //On renseigne les valeurs 
        $booking->setFullDay($fullDay);
        $booking->setNbTickets($nbTickets);

        //On passe le statut de commande à inProgress 
        $booking->setStatus(Booking::STATUS_INPROGRESS);
        $session->set('tickets', $tickets);
        //On enregistre les valeurs en session
        $session->set('nbTickets', $nbTickets);
        $session->set('booking', $booking);

        $response = [
            'success' => true
        ];
        return new JsonResponse($response);
    }
    
    ////////////////////////////////////////////////////////////////////////////
    //////////////Nombre de tickets par tarif////////////////   
    
    private function getNbPerType($tickets){
        $ret = [
            'normal' => 0,
            'reduct' => 0,
            'child' => 0,
            'senior' => 0,
        ];
        
        foreach ($tickets as $ticket){
                    $ret[$ticket->getFaretype()]++;
        }
        return $ret;
    }
    
    private function getTotal($nbPerType, $isFullDay){
        return $nbPerType['normal']*($isFullDay?16:8) 
                +$nbPerType['reduct']*($isFullDay?10:5) 
                + $nbPerType['child']*($isFullDay?8:4) 
                + $nbPerType['senior']*($isFullDay?12:6);
        
    }

    ////////////////////////////////////////////////////////////////////////////
    //////////////Affichage page récapitulative et saisie de l'adresse mail////////////////

    public function showRecapAction(Request $request) {
        $session = $request->getSession();

        //Récupération de booking en session
        $booking = $session->get('booking');
        $id = $session->get('id');
        
        
        $repository = $this->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }
        
        $nbPerType = $this->getNbPerType($booking->getTickets());
       
        return $this->render('APProjet4BookingBundle:Booking:recap.html.twig', [
                    'id' => $id,
                    'event' => $event,
                    'nbTickets'=>$booking->getNbTickets(),
                    'booking' => $booking,
                    'nbType' => $nbPerType,
                    'tickets'=> $booking->getTickets(),
                    'total' => $this->getTotal($nbPerType, $booking->getFullDay()),
                    
        ]);
    }
    
    ////////////////////////////////////////////////////////////////////////////
    /////////////Enregistrement email et enregistrement booking et tickets en base de données/////////////////
    
    public function postEmailAndBookingAction(Request $request) {
        
        //Récupération de la session
        $session = $request->getSession();
        
        $em = $this->getDoctrine()->getManager();
        
        //On récupère les valeurs de booking en session
        $booking = $session->get('booking');
        //On crée les entités utilisateur
        $user = new User();
       
        //Récupération de l'adresse mail saisie      
        $email = $request->get('email');
        
        //On génère un code random pour le code commande
        $bytes = random_bytes(5);
        
        //On renseigne les valeurs
        $booking->setEmail($email);
        $booking->setOrderCode(bin2hex($bytes));
 
        //On renseigne l'adresse mail de l'utilisateur
        $user->setEmail($email);
        
        if (null === $booking){
             throw new NotFoundHttpException("La commande demandée n'existe pas.");
        }   
        
        //To do : Récuperer la clé stripe : stripe_publishable_key
  
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
     
    
    ///////////////////////////////////////////////////////////////////////////
    ////////////////////////// Page de confirmation paiement ///////////////////

    public function showPaymentAction(Request $request, $orderCode) {

        //Récupération de l'adresse mail  
        $email = $request->get('email');
        
        $session = $request->getSession();
        
        //Récupération des variables de commande en Bdd pour afficher la page de confirmation
        $BookingRepo = $this->getDoctrine()
               ->getManager()
               ->getRepository('APProjet4BookingBundle:Booking');
        $booking = $BookingRepo->findOneByOrderCode(
            $orderCode);
        $id = $booking->getEvent();
        
//        //On récupère la clé stripe
//        $stripeToken = $request->get('stripeToken');
//        //Et on l'enregistre en session
//        $session->set('stripeToken', $stripeToken);
        
        $EventRepo = $this->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Event');
        $event = $EventRepo->find($id);
   
        $nbPerType = $this->getNbPerType($booking->getTickets());

        //TO DO//Si erreur : On prévient l'utilisateur avec un message popup
        //Si paiement ok : On enregistre booking en bdd status: paid
        $booking->setStatus(Booking::STATUS_PAID);
        
       
        //Si tout s'est bien passé, on envoie le mail de confirmation 
        $this->sendBookingEmail($request);
        
        //On flush la commande une dernière fois. 
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        
        //On efface alors la session
        $session->clear();
        
        return $this->render('@APProjet4Booking/Booking/paymentConfirmation.html.twig' , [
                    'id' => $id,
                    'event' => $event,
                    'visitDate' => $booking->getTickets()->get('visitDate'),
                    'booking' => $booking,
                    'orderCode' => $orderCode,
                    'email' => $email,
                    'stripeToken'=> $stripeToken,
                    'nbType' => $nbPerType,
                    'tickets'=> $booking->getTickets(),
                    'total' => $this->getTotal($nbPerType, $booking->getFullDay())
        ]);
    }
 
     /////////////////////////////////////////////////////////////////////////
    /////////////////////////Email et Billet///////////////////////////////////   
    //$id, $booking, $stripeToken en arguments
   public function sendBookingEmail(Request $request) {
        $session = $request->getSession();

        //Récupération de variables en session 
        $stripeToken = $session->get('stripeToken');
        $orderCode = $session->get('booking')->getOrderCode();
        
        //Récupération des variables de commande en Bdd pour compléter le mail
        $BookingRepo = $this->getDoctrine()
               ->getManager()
               ->getRepository('APProjet4BookingBundle:Booking');
        $booking = $BookingRepo->findOneByOrderCode(
            $orderCode);
        $id = $booking->getEvent();
        
        $visitDate = $booking->getTickets()->get('visitDate');
        
        $bookingEmail = $booking->getEmail();
        $bookingOrderCode = $booking->getOrderCode();
        $tickets = $booking->getTickets()->getValues();
  
        $EventRepo = $this->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Event');
        $event = $EventRepo->find($id);
        
        $message = \Swift_Message::newInstance()
                ->setSubject('Votre visite au Louvre')
                ->setFrom(['billetterie@louvre.fr' => 'Musée du Louvre'])
                ->setTo([$bookingEmail])
                ->setBody(
                        $this->renderView('@APProjet4Booking/Booking/ticket.html.twig', [
                            'id' => $id,
                            'booking' => $booking,
                            'visitDate' => $visitDate,
                            'event' => $event,
                            'bookingEmail' => $bookingEmail,
                            'stripeToken'=> $stripeToken,
                            'total' => $this->getTotal($this->getNbPerType($booking->getTickets()), $booking->getFullDay()),
                            'bookingOrderCode' => $bookingOrderCode,
                            'tickets' => $tickets,
                            ]
                        ), 'text/html');
        
        return $this->get('mailer')->send($message);
    }
}


     
