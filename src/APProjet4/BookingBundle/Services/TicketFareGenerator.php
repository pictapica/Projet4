<?php

namespace APProjet4\BookingBundle\Services;

class TicketFareGenerator {

    //Ages
    const MAX_BABY = 3;
    const MAX_CHILD = 12;
    const MAX_ADULT = 59;
    const MIN_SENIOR = 60;
    
    //Tarifs en €
    const FARE_BABY = 0;
    const FARE_CHILD = 8;
    const FARE_NORMAL = 16;
    const FARE_SENIOR = 12;
    const FARE_REDUCED = 10;
    const HALFDAY = 0.5;

    public function ageCalcul($ticket) {
        //Date du jour et Age du visiteur
        //On récupère la date de naissance saisie
        $birthDate = $ticket->getDateOfBirth();
        $today = new \DateTime();
        //On calcule l'age en fonction de la date d'aujourd'hui
        $interval = $today->diff($birthDate);
        //On récupère l'age en années
        $age = $interval->format('%y');
        return $age;
    }

    //retourne le tarif en fonction de l'age du visiteur
    public function fareCondition($age) {

        if ($age <= self::MAX_BABY) {
            return self::FARE_BABY;
        } elseif ($age <= self::MAX_CHILD) {
            return self::FARE_CHILD;
        } elseif ($age <= self::MAX_ADULT) {
            return self::FARE_NORMAL;
        } elseif ($age <= self::MIN_SENIOR) {
            return self::FARE_SENIOR;
        }
    }

    // Si le visiteur à choisi tarif réduit (affichage d'un message ? ) 
    //le tarif et de 10 pour la journée ou de 5 pour la demi-journée
    
    
    //Si le visiteur a choisi demi-journée
          // if ($fullDay === false {
           // $fare = $fare / 2;
        
}
