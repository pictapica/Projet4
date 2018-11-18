<?php

namespace APProjet4\BookingBundle\Services;

use Doctrine\ORM\EntityManager;

//Return $arrayDatesDisabled
class DisabledDatesSystem {
    
    private $em;
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    /**
     * Retourne un tableau contenant les dates fériées + les dates où + de 1000 billets ont été vendus
     * pendant les dates de l'événement
     * @param int $id
     * @return array
     */
    public function mergeDatesDisabled($id) {

        $holidays = $this->getHolidays($id);
        $datesList = $this->getDatesWithMaxBooking();

        $arrayDatesDisabled = array_merge($datesList, $holidays);

       return $arrayDatesDisabled;
    }
    
    
    /**
     * Retourne un tableau contenant les dates des jours où plus de 1000 billets ont été vendus
     * @return array
     */
    public function getDatesWithMaxBooking() {

        $repository = $this->em->getRepository('APProjet4BookingBundle:Ticket');
        $datesList = $repository->countTicketsPerDay();

        return $datesList;
    }
    
    /**
     * Retourne un tableau des jours fériés des années couvertes pas l'événement
     * @param int $event_id
     * @return array
     */
    public function getHolidays($event_id) {
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
        }$holidays = array_reduce($holidaysDates, 'array_merge', []);
        
        return $holidays;
    }
    
    /**
     * Retourne un tableau contenant les années sur lesquelles s'étale l'événement
     * @param int $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function getEventPeriod($id) {
        $repository = $this->em->getRepository('APProjet4BookingBundle:Event');
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
    
    public function GetEventDates($id) {
        $repository = $this->em->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->findOneById($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }
        $startDate = $event->getStartDate();
        $endDate = $event->getEndDate();
        
        $eventDates = [$startDate,$endDate]; 
        
        return $eventDates;
    }
}
