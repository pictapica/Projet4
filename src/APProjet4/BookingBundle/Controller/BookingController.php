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
    const HEURE_MAX = 12;// 14h heure de Paris
    
    ////////////////////////////////////////////////////////////////////////////
    /////////////////////////////Mardi ou dimanche ? ///////////////////////////
    
    public function isADisabledDay($visitDate){
        
        $visitDay = new \DateTime($visitDate);
        $day = $visitDay->format('l');
        if ($day == 'Mardi' || $day == 'Dimanche'|| 
                $day == "Tuesday" || $day == "Sunday"){
            return true;
        }
        return false;
    }
    ////////////////////////////////////////////////////////////////////////////
    ///////////////////////////// Jour passé ? //////////////////////////////////
    
    public function isAPastDay($visitDate){
        
        $now = new \DateTime();
        $d = new \DateTime($visitDate);
        //Si le jour de visite choisi est antérieur à aujourd'hui
        if ($d->format('Y-m-d') < $now->format('Y-m-d')){
            return true;
        }
        return false;
    }
    ////////////////////////////////////////////////////////////////////////////
    ///////////////////////////// Jour fériés? //////////////////////////////////
    
    public function isHolidaysDay($visitDate){
        
        $visitDay = new \DateTime($visitDate);
        $year = $visitDay->format('Y');
        
        $easterDate  = easter_date($year);
        $easterDay   = date('j', $easterDate);
        $easterMonth = date('n', $easterDate);
        $easterYear   = date('Y', $easterDate);

        $holidays = array(
            // Dates fixes
            mktime(0, 0, 0, 1,  1,  $year),  // 1er janvier
            mktime(0, 0, 0, 5,  1,  $year),  // Fête du travail
            mktime(0, 0, 0, 5,  8,  $year),  // Victoire 45
            mktime(0, 0, 0, 7,  14, $year),  // Fête nationale
            mktime(0, 0, 0, 8,  15, $year),  // Assomption
            mktime(0, 0, 0, 11, 1,  $year),  // Toussaint
            mktime(0, 0, 0, 11, 11, $year),  // Armistice
            mktime(0, 0, 0, 12, 25, $year),  // Noel

            // Dates variables
            mktime(0, 0, 0, $easterMonth, $easterDay + 1,  $easterYear),
            mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear),
            mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear),
        );
        
        if (in_array($visitDate,$holidays)){
            return true;
        } 
        return false;
    }
    //////////////////////////////////////////////////////////////////////////// 
    //////////////////////////////Quota par jour////////////////////////////////

    private function checkMaxBookingAction(Request $request) {
        
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
    //////////////// Affichage choix Date & type de billet//////////////////////

    public function showSelectDateAction($id) {
        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->findOneById($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }
        //TO DO///Récupération des dates complètes dans $holidaysDay
        
//        if($holidaysDays{
//            return $holidaysDays;
//        }

        return $this->render('APProjet4BookingBundle:Booking:selectDate.html.twig', [
                    'event' => $event,
//                    'disabledDay' => $disabledDay,
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////Vérification de l'heure///////////////////////////
    public function isHourPast($visitDate) {
        
        $now = new \DateTime();
        
        //Si la date de visite est aujourd'hui et qu'il est plus de 14h 
        if ($visitDate == $now && $now->format('H') >= self::HEURE_MAX) {
          return true;  
        }
        return false;
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
        //Vérification si date dispo mardi, dimanche et jours fériés
        
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
        
        //Si le jour est un mardi ou un dimanche
        if ($this->isADisabledDay($visitDate)){
            throw new NotFoundHttpException("Le musée est fermé tous les mardis. "
        . "Pour acheter un billet pour le dimanche, merci de vous rendre aux guichets du musée");
        }
        
        if ($this->isHolidaysDay($visitDate)){
             throw new NotFoundHttpException("Le musée est fermé les 1er mai, 1er novembre et 25 décembre. "
            . "Pour commander pour les autres jours fériés de l'année, "
            . "merci de vous rendre aux guichets du musée");
        }
        
        if ($this->isAPastDay($visitDate)){
            throw new NotFoundHttpException("Acheter pour un jour passé? Quelle drôle d'idée !");
        }
        
        //Si le client clique sur billet journée alors que la date de visite est aujourd'hui et qu'il est plus de 14h
        if ($fullDay === "true" && $this->isHourPast($visitDate)) {
            throw new NotFoundHttpException("Il est impossible d'acheter un billet pour la journée après 14h");
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
    
    public function ckeckAgeAndFare($dateOfBirth){
        
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
        $validator = $this->get('validator');
        $listErrors = [];
       
        
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
                $errors = $validator->validate($tickets);
                if(count($errors)>0){
                    $listErrors[] = $errors;
                }
                $booking->addTicket($t);
            }

        // Si $listErrors n'est pas vide, on affiche les erreurs
        if(count($listErrors) > 0) {
          // $listErrors est un objet, sa méthode __toString permet de lister joliement les erreurs
          return new Response((string) $listErrors);
        } else {
  
        //On compte le nombre total de billets
        $nbTickets = count($tickets);
        
        //On renseigne les valeurs 
        $booking->setNbTickets($nbTickets);

        //On enregistre les valeurs en session
        $session->set('booking', $booking);
        $session->set('nbTickets', $nbTickets);
        
        //Si tarif choisi correspond à la date de naissance
        if ('ckeckAgeAndFare(true)') {
            // On renvoie une réponse success
            $response = [
                'success' => true
            ];
            return new JsonResponse($response);
        }
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
        //TO do toto
        $session = $request->getSession();
        $toto = $session->get('booking');
        
        //Récupèration du code commande : orderCode
        $orderCode = $toto->getOrderCode();
        
        //Récupèration de la clé stripe
        $stripeToken = $request->get('stripeToken');

        //On vérifie l'existence de stripeToken
        if ($stripeToken == null){
        $session->getFlashBag()->add('info', 'Oups, un problème est survenu lors de votre paiement !'); 
        return $this->redirectToRoute('approjet4_booking_recap');
        } 
        //Si tout s'est bien passé,
        //Récupération de la commande déjà créée
        $BookingRepo = $this->getDoctrine()
           ->getManager()
           ->getRepository('APProjet4BookingBundle:Booking');
        $booking = $BookingRepo->findOneByOrderCode($orderCode);
        
        if (null === $booking){
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
    
    ////////////////////////////////////////////////////////////////////////////
    ///////////////// Affichage page de confirmation paiement //////////////////

    public function showPaymentAction($orderCode) {
        
        //Récupération des variables de commande en Bdd pour afficher la page de confirmation
        $BookingRepo = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Booking');
        $booking = $BookingRepo->findOneByOrderCode($orderCode);
        
        if (null === $booking){
             throw new NotFoundHttpException("La commande demandée n'existe pas.");
        } 

        //Récupération du nombre de tickets
        $nbPerType = $this->getNbPerType($booking->getTickets());

        return $this->render('@APProjet4Booking/Booking/paymentConfirmation.html.twig' , [
                    'event' => $booking->getEvent(),
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

   public function sendBookingEmail(Request $request) {
       
        $session = $request->getSession();

        //Récupération du numéro de commande en session 
        $orderCode = $session->get('booking')->getOrderCode();
        
        //Récupération des variables de commande en Bdd pour compléter le mail
        $BookingRepo = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Booking');
        $booking = $BookingRepo->findOneByOrderCode($orderCode);
        
        if (null === $booking){
             throw new NotFoundHttpException("La commande demandée n'existe pas.");
        } 
        
        //Récupération de l'id de l'événement
        $id = $booking->getEvent();
        
        //Récupération de la date de visite 
        $visitDate = $booking->getTickets()->get('visitDate');
        
        //Récupération de lévénement
        $EventRepo = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $EventRepo->find($id);
        
        if (null === $event){
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
                            'stripeToken'=> $booking->getStripeToken(),
                            'total' => $this->getTotal($this->getNbPerType($booking->getTickets()), $booking->getFullDay()),
                            'bookingOrderCode' => $orderCode,
                            'tickets' => $booking->getTickets()->getValues(),
                            ]
                        ), 'text/html');
        
        return $this->get('mailer')->send($message);
    }
}
