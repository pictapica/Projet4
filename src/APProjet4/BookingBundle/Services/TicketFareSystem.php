<?php

namespace APProjet4\BookingBundle\Services;

//Return FareType 
class TicketFareSystem {

    const MINCHILD = 4;
    const MAXCHILD = 12;
    const MAXADULT = 59;
    const MINSENIOR = 60;
    const MAXSENIOR = 120;

    /**
     * Returns true if in the right interval
     * @param string $age
     * @return boolean
     * 
     */
    private function isBaby($age) {
        return ($age < self::MINCHILD);
    }

    /**
     * Returns true if in the right interval
     * @param string $age
     * @return boolean
     */
    private function isChild($age) {
        return (($age >= self::MINCHILD) && ($age < self::MAXCHILD));
    }

    /**
     * Returns true if in the right interval
     * @param string $age
     * @return boolean
     */
    private function isNormal($age) {
        return (($age >= self::MAXCHILD) && ($age <= self::MAXADULT));
    }

    /**
     * Returns true if in the right interval
     * @param string $age
     * @return boolean
     */
    private function isSenior($age) {
        return (($age >= self::MINSENIOR) && ($age <= self::MAXSENIOR));
    }

    /**
     * Returns fareType
     * @param type $visitDate
     * @param type $dateOfBirth
     * @return string
     * 
     */
    public function getTicketFareType($visitDate, $dateOfBirth) {
        $age = $this->getAge($visitDate, $dateOfBirth);
        if ($this->isNormal($age)) {
            return 'normal';
        } elseif ($this->isChild($age)) {
            return 'child';
        } elseif ($this->isSenior($age)) {
            return 'senior';
        } elseif ($this->isBaby($age)) {
            return 'baby';
        }
    }

    /**
     * Returns the age according to the date of birth
     * 
     * @param date $visitDate
     * @param date $dateOfBirth
     * @return string
     */
    public function getAge($visitDate, $dateOfBirth) {
        $birth = new \DateTime($dateOfBirth);
        $datediff = $visitDate->diff($birth);
        $age = $datediff->format('%y');
        return $age;
    }

}
