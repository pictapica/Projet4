<?php

namespace APProjet4\BookingBundle\Services;

use APProjet4\BookingBundle\Services\DisabledDatesSystem;
use Exception;

//Return true si le control est valide
class ControlVisitDateSystem {

    const HEURE_MAX = 12; // 14h heure de Paris

    private $disabledDates;

    public function __construct(DisabledDatesSystem $disabledDates) {
        $this->disabledDates = $disabledDates;
    }

    public function controlVisitDate($visitDate, $event_id, $fullDay) {
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
        return true;
    }

    /**
     * Retourne true si la date selectionnée est un mardi ou un dimanche
     *
     * @param date $visitDate
     * @return boolean
     */
    public function isADisabledDay($visitDate) {

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
        $arrayDatesDisabled = $this->disabledDates->mergeDatesDisabled($id);
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

}
