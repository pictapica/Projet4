<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use APProjet4\BookingBundle\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SelectDateController extends Controller {

    const MAX_TICKETS_PER_DAY = 1000;
    const HEURE_MAX = 12; // 14h heure de Paris

    ////////////////////////////////////////////////////////////////////////////
    /////////////////////////////Mardi ou dimanche ? ///////////////////////////

    private function isADisabledDay($visitDate) {

        $visitDay = new \DateTime($visitDate);
        $day = $visitDay->format('l');
        if ($day == 'Mardi' || $day == 'Dimanche' ||
                $day == "Tuesday" || $day == "Sunday") {
            return true;
        }
        return false;
    }

    ////////////////////////////////////////////////////////////////////////////
    ///////////////////////////// Jour passé ? //////////////////////////////////

    private function isAPastDay($visitDate) {

        $now = new \DateTime();
        $d = new \DateTime($visitDate);
        //Si le jour de visite choisi est antérieur à aujourd'hui
        if ($d->format('Y-m-d') < $now->format('Y-m-d')) {
            return true;
        }
        return false;
    }

    ////////////////////////////////////////////////////////////////////////////
    ///////////////////////////// Jour fériés? //////////////////////////////////

    private function isHolidaysDay($visitDate) {

  
        $visitDay = new \DateTime($visitDate);
        $year = $visitDay->format('Y');

        if($year === null) {
            $year = intval(date('Y'));
        }
        
        $easterDate = easter_date($year);
        $easterDay = date('j', $easterDate);
        $easterMonth = date('n', $easterDate);
        $easterYear = date('Y', $easterDate);

        $holidays = array(
            // Dates fixes
            mktime(0, 0, 0, 1, 1, $year), // 1er janvier
            mktime(0, 0, 0, 5, 1, $year), // Fête du travail
            mktime(0, 0, 0, 5, 8, $year), // Victoire 45
            mktime(0, 0, 0, 7, 14, $year), // Fête nationale
            mktime(0, 0, 0, 8, 15, $year), // Assomption
            mktime(0, 0, 0, 11, 1, $year), // Toussaint
            mktime(0, 0, 0, 11, 11, $year), // Armistice
            mktime(0, 0, 0, 12, 25, $year), // Noel
            // Dates variables
            mktime(0, 0, 0, $easterMonth, $easterDay + 1, $easterYear),
            mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear),
            mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear),
        );

        if (in_array($visitDate, $holidays)) {
            return true;
        }
        return false;
    }
    
    private function checkPeriod($id){
        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->findOneById($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }
        $startDate = $event->getStartDate;
        $endDate = $event->getEndDate;
        
        $period = [$startDate, $endDate];
        
        return $period;
        
    }
    //////////////////////////////////////////////////////////////////////////// 
    //////////////////////////////Quota par jour////////////////////////////////

    private function checkMaxBookingAction(Request $request) {

        //On récupère les tickets par date de visite
        $d = new \DateTime($request->get('visitDate'));
        $tickets = $this->getDoctrine()->getRepository('APProjet4BookingBundle:Booking')
                ->findByVisitDate($d);

        //Si j'ai des tickets et s'il y en a plus de 1000
        if ($tickets && count($tickets) > MAX_TICKETS_PER_DAY) {
            return false;
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////Jours où 1000 billets vendus////////////////

    private function getDatesWithMaxBooking() {

        $repository = $this->getDoctrine()->getRepository('APProjet4BookingBundle:Ticket');
        $datesList = $repository->countTicketsPerDay();
        //ex ['2018-09-22', '2018-09-29'];

        return $datesList;
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
        //Avant l'affichage de la page selectDate et de son datepicker, on doit savoir s'il y a des jours où il y a plus de 1000
        //billets vendus. Si c'est le cas, les dates doivents être envoyées sous forme de tableau pour être ensuite grisées
        //sur le datepicker
        $datesList = $this->getDatesWithMaxBooking();
        

        return $this->render('APProjet4BookingBundle:Booking:selectDate.html.twig', [
                    'event' => $event,
                    'datesList' => json_encode($datesList),
//                    'holidaysDays' => $holidaysDays,
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
        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($event_id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $event_id . " n'existe pas.");
        }

        //Si le jour est un mardi ou un dimanche
        if ($this->isADisabledDay($visitDate)) {
            throw new NotFoundHttpException('Le musée est fermé tous les mardis. '
            . "Pour acheter un billet pour le dimanche, merci de vous rendre aux guichets du musée");
        }

        if ($this->isHolidaysDay($visitDate)) {
            throw new NotFoundHttpException("Le musée est fermé les 1er mai, 1er novembre et 25 décembre. "
            . "Pour commander pour les autres jours fériés de l'année, "
            . "merci de vous rendre aux guichets du musée");
        }

        if ($this->isAPastDay($visitDate)) {
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
        } else {
            $response = [
                'getDatesWithMaxBooking()'
            ];
            return new JsonResponse($response);
        }
    }
}
