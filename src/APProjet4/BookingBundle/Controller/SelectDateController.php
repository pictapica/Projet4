<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use APProjet4\BookingBundle\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SelectDateController extends Controller {

    const MAX_TICKETS_PER_DAY = 1000;
    const HEURE_MAX = 12; // 14h heure de Paris

    /**
     * Retourne true si la date selectionnée est un mardi ou un dimanche
     *
     * @param date $visitDate
     * @return boolean
     */

    private function isADisabledDay($visitDate) {

        $visitDay = new \DateTime($visitDate);
        $day = $visitDay->format('l');
        if ($day == 'Mardi' || $day == 'Dimanche' ||
                $day == "Tuesday" || $day == "Sunday") {
            return true;
        }
        return false;
    }

    /**
     * Retourne true si la date de visite sélectionnée est un jour passé
     * @param date $visitDate
     * @return boolean
     */
    private function isAPastDay($visitDate) {
        $now = new \DateTime();
        $d = new \DateTime($visitDate);
        //Si le jour de visite choisi est antérieur à aujourd'hui
        if ($d->format('Y-m-d') < $now->format('Y-m-d')) {
            return true;
        }
        return false;
    }

    /**
     * Retourne true si la date sélectionnée et un jour férié ou un jour où + de 1000 tickets ont été vendus
     * @param date $visitDate
     * @param int $id
     * @return boolean
     */
    private function IsAHolidayDate($visitDate, $id) {
        $visit = new \DateTime($visitDate);
        $arrayDatesDisabled = $this->mergeDatesDisabled($id);
        if (in_array($visit, $arrayDatesDisabled)) {
            return true;
        }
        return false;
    }

    /**
     * Retourne true si le jour sélectionné est aujourd'hui et qu'il est plus de 14h
     * @param type $visitDate
     * @return boolean
     */
    public function isHourPast($visitDate) {
        $now = new \DateTime();
        //Si la date de visite est aujourd'hui et qu'il est plus de 14h 
        if ($visitDate == $now && $now->format('H') >= self::HEURE_MAX) {
            return true;
        }
        return false;
    }

    /**
     * Retourne un tableau contenant les années sur lesquelles s'étale l'événement
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    private function getEventPeriod($id) {
        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->findOneById($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }
        $startDate = $event->getStartDate();
        $endDate = $event->getEndDate();

        $startYear = $startDate->format('Y');
        $endYear = $endDate->format('Y');

        $yearEvent = [];
        for ($i = $startYear; $i <= $endYear; $i++) {
            $yearEvent[] = $i;
        }
        return $yearEvent;
    }

    /**
     * Retourne un tableau des jours fériés des années couvertes pas l'événement
     * @param int $event_id
     * @return array
     */
    private function getHolidays($event_id) {
        $yearEvent = $this->getEventPeriod($event_id);
        foreach ($yearEvent as $key => $year) {

            unset($yearEvent[$key + 1]);
            $easterDate = easter_date($year);
            $easterDay = date('j', $easterDate);
            $easterMonth = date('n', $easterDate);
            $easterYear = date('Y', $easterDate);

            $holidaysDates[$key] = [
                // Dates fixes
                date('Y-m-d', mktime(0, 0, 0, 1, 1, $year)), // 1er janvier
                date('Y-m-d', mktime(0, 0, 0, 5, 1, $year)), // Fête du travail
                date('Y-m-d', mktime(0, 0, 0, 5, 8, $year)), // Victoire 45
                date('Y-m-d', mktime(0, 0, 0, 7, 14, $year)), // Fête nationale
                date('Y-m-d', mktime(0, 0, 0, 8, 15, $year)), // Assomption
                date('Y-m-d', mktime(0, 0, 0, 11, 1, $year)), // Toussaint
                date('Y-m-d', mktime(0, 0, 0, 11, 11, $year)), // Armistice
                date('Y-m-d', mktime(0, 0, 0, 12, 25, $year)), // Noel
                // Dates variables
                date('Y-m-d', mktime(0, 0, 0, $easterMonth, $easterDay + 1, $easterYear)), // Lundi de paques
                date('Y-m-d', mktime(0, 0, 0, $easterMonth, $easterDay + 39, $easterYear)), // Ascension
                date('Y-m-d', mktime(0, 0, 0, $easterMonth, $easterDay + 50, $easterYear)), // Pentecote
            ];
        }
        $holidays = array_reduce($holidaysDates, 'array_merge', []);

        return $holidays;
    }

    /**
     * Retourne un tableau contenant les dates des jours où plus de 1000 billets ont été vendus
     * @return array
     */
    private function getDatesWithMaxBooking() {

        $repository = $this->getDoctrine()->getRepository('APProjet4BookingBundle:Ticket');
        $datesList = $repository->countTicketsPerDay();

        return $datesList;
    }

    /**
     * Retourne un tableau contenant les dates fériées + les dates où + de 1000 billets ont été vendus
     * @param int $id
     * @return array
     */
    private function mergeDatesDisabled($id) {

        $holidays = $this->getHolidays($id);
        $datesList = $this->getDatesWithMaxBooking();

        $arrayDatesDisabled = array_merge($datesList, $holidays);
        return $arrayDatesDisabled;
    }

//    private function eliminateOutOfRangeDates($id) {
//        $arrayDatesDisabled = $this->mergeDatesDisabled($id);
//        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
//        $event = $repository->findOneById($id);
//
//        if (null === $event) {
//            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
//        }
//        $startDate = $event->getStartDate();
//        $endDate = $event->getEndDate();
//        $period = [$startDate, $endDate];
//        foreach ($period as $date){
//            $range = [$date];
//        }
//        
//        $goodDate = array_intersect($range, $arrayDatesDisabled);
//        return $goodDate;
//    }

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
        //Tableau des dates à griser dans le datepicker
        $arrayDatesDisabled = $this->mergeDatesDisabled($id);
//        $goodDate = $this->eliminateOutOfRangeDates($id);
        
        return $this->render('APProjet4BookingBundle:Booking:selectDate.html.twig', [
                    'event' => $event,
                    'arrayDatesDisabled' => json_encode($arrayDatesDisabled),
//                    'goodDate' => json_encode($goodDate),
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
                'success' => false
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
            throw new Exception("L'évènement d'id " . $event_id . " n'existe pas.");
        }

        //Si le jour est un mardi ou un dimanche
        if ($this->isADisabledDay($visitDate)) {
            throw new Exception('Le musée est fermé tous les mardis. '
            . "Pour acheter un billet pour le dimanche, merci de vous rendre aux guichets du musée");
        }

        //Si le jour est passé
        if ($this->isAPastDay($visitDate)) {
            throw new Exception("Acheter pour un jour passé? Quelle drôle d'idée !");
        }

        //Si le client clique sur billet journée alors que la date de visite est aujourd'hui et qu'il est plus de 14h
        if ($fullDay === "true" && $this->isHourPast($visitDate)) {
            throw new Exception("Il est impossible d'acheter un billet pour la journée après 14h");
        }

        //Si le jour est un jour férié ou que 1000 billets ont déjà été vendus
        if ($this->IsAHolidayDate($visitDate, $event_id)) {
            throw new Exception("Le musée est fermé les 1er mai, 1er novembre et 25 décembre. "
            . "Pour commander pour les autres jours fériés de l'année, "
            . "merci de vous rendre aux guichets du musée");
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
