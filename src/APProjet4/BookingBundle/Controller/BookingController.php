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

    public function indexAction() {
        $EventRepository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
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
    //////////////// Affichage choix Date & type de billet//////////////////////

    public function showSelectDateAction($id) {
        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->findOneById($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }
        //TO DO///Récupération des dates complètes dans $disableDate

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
        $tickets = $this->getDoctrine()->getRepository('APProjet4BookingBundle:Booking')
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
        if (!('visitDate') || !('fullDay') || !('event_id')) {

            $response = [
                'success' => false
            ];
            return new JsonResponse($response);
        }
        //Vérification si date dispo
        //Vérification de la validité des paramètres ? isItAPastDay(), isItADisabledDay()
        //Vérification du billet journée pour la date actuelle (<14h) :  isHourPast() 
       
       
        //On récupère les variables
        $visitDate = $request->get('visitDate');
        $event_id = $request->get('event_id');
        $fullDay = $request->get('fullDay');
        
        //Création d'une nouvelle commande
        $booking = new Booking();

        //On récupère l'événement
        $repository = $this->getDoctrine() ->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($event_id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $event_id . " n'existe pas.");
        }

        //On renseigne les valeurs 
        $booking->setFullDay($fullDay);

        //On enregistre les valeurs en session
        $session->set('booking', $booking);
        $session->set('visitDate', $visitDate);
        
        //On définit une nouvelle valeur pour visitDate et on l'ajoute à la session
        $session->set('event_id', $event_id);

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
    ///////////////////////////Vérification du tarif choisi/////////////////////
    
    public function ckeckFareAction($dateOfBirth){
        
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
    /////////////////Enregistrement des informations visiteur///////////////////////

   public function postContactDetailsAction(Request $request) {
        // Récupération de la session
        $session = $request->getSession();
        
//        $fullDay = $session->get('fullDay');
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
        $booking->setNbTickets($nbTickets);

        //On enregistre les valeurs en session
        $session->set('booking', $booking);
        
        //Si tarif choisi correspond à la date de naissance
        if ('ckeckFareAction(true)') {
            // On renvoie une réponse success
            $response = [
                'success' => true
            ];
            return new JsonResponse($response);
        }
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

        //Récupération en session
        $booking = $session->get('booking');
        $id = $session->get('event_id');
        
        
        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
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
        
        //Récupèration des valeurs en session
        $booking = $session->get('booking');
        $event_id = $session->get('event_id');

        //Création de l'entité utilisateur
        $user = new User();
       
        //Récupération de l'adresse mail saisie      
        $email = $request->get('email');
        
        //Génération d'un code random pour le code commande
        $bytes = random_bytes(5);
        
        //Renseignement des valeurs
        $booking->setEmail($email);
        $booking->setOrderCode(bin2hex($bytes));
        $user->setEmail($email);
        
        if (null === $booking){
             throw new NotFoundHttpException("La commande demandée n'existe pas.");
        } 
        //Récupération de l'entité Event
        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($event_id);
        
        //Si event existe
        if($event) {
            //Liaison de l'évènement à la commande
            $booking->setEvent($event);
        }else {
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
     
    ////////////////////////////////////////////////////////////////////////////
    ///////////////////// Enregistrement Paiement //////////////////////////////
    
    public function validatePaymentAction(Request $request) {
        
        $session = $request->getSession();
        $booking = $session->get('booking');
        
        //Récupèration du code commande : orderCode
        $orderCode = $booking->getOrderCode();
        
        //Récupèration de la clé stripe
        $stripeToken = $request->get('stripeToken');

        //On vérifie l'existence de stripeToken
        if ($stripeToken == null){
        $session->getFlashBag()->add('info', 'Oups, un problème est survenu lors de votre paiement !'); 
        return $this->redirectToRoute('approjet4_booking_recap');
        } else {
            //Si tout s'est bien passé,
            //Récupération de la commande déjà créée
            $BookingRepo = $this->getDoctrine()
               ->getManager()
               ->getRepository('APProjet4BookingBundle:Booking');
            $booking = $BookingRepo->findOneByOrderCode(
            $orderCode);
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
    }
    
    
    
    ////////////////////////////////////////////////////////////////////////////
    ///////////////// Affichage page de confirmation paiement //////////////////

    public function showPaymentAction($orderCode) {
        
        //Récupération des variables de commande en Bdd pour afficher la page de confirmation
        $BookingRepo = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Booking');
        $booking = $BookingRepo->findOneByOrderCode($orderCode);
        
        //Récupération de l'id de l'événement
        $id = $booking->getEvent();
        
        //Récupération de lévénement
        $EventRepo = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $EventRepo->find($id);
        
        //Récupération du nombre de tickets
        $nbPerType = $this->getNbPerType($booking->getTickets());

        return $this->render('@APProjet4Booking/Booking/paymentConfirmation.html.twig' , [
                    'id' => $id,
                    'event' => $event,
                    'visitDate' => $booking->getTickets()->first()->getVisitDate(),
                    'booking' => $booking,
                    'orderCode' => $orderCode,
                    'stripeToken' =>$booking->getStripeToken(),
                    'email' => $booking->getEmail(),
                    'nbType' => $nbPerType,
                    'tickets'=> $booking->getTickets(),
                    'total' => $this->getTotal($nbPerType, $booking->getFullDay())
        ]);
    }
 
     /////////////////////////////////////////////////////////////////////////
    /////////////////////////Email et Billet///////////////////////////////////   
    //$id, $booking en arguments ??
    // 
   public function sendBookingEmail(Request $request) {
       
        $session = $request->getSession();
//
        //Récupération du numéro de commande en session 
        $orderCode = $session->get('booking')->getOrderCode();
        
        //Récupération des variables de commande en Bdd pour compléter le mail
        $BookingRepo = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Booking');
        $booking = $BookingRepo->findOneByOrderCode($orderCode);
        
        //Récupération de l'id de l'événement
        $id = $booking->getEvent();
        
        //Récupération de la date de visite 
        $visitDate = $booking->getTickets()->get('visitDate');
        
        //Récupération de lévénement
        $EventRepo = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $EventRepo->find($id);
        
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
                            'stripeToken'=> $booking->getStripeToken(),
                            'total' => $this->getTotal($this->getNbPerType($booking->getTickets()), $booking->getFullDay()),
                            'bookingOrderCode' => $orderCode,
                            'tickets' => $booking->getTickets()->getValues(),
                            ]
                        ), 'text/html');
        
        return $this->get('mailer')->send($message);
    }
}
