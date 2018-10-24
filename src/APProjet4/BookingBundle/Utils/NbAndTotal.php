<?php

namespace APProjet4\BookingBundle\Controller;

class NbAndTotal {
    
    /**
     * Retourne le nombre de tickets par tarif  
     * 
     * @param type $tickets
     * @return int
     */
    public static function getNbPerType($tickets) {
        
        $nbPerType = [
            'normal' => 0,
            'reduct' => 0,
            'child' => 0,
            'senior' => 0,
        ];

        foreach ($tickets as $ticket) {
            $nbPerType[$ticket->getFaretype()] ++;
        }
        return $nbPerType;
        
         //NbAndTotal::getNbPerType()     

    }
    /**
     * Retourne le montant total en euro
     * 
     * @param type $nbPerType
     * @param type $isFullDay
     * @return int
     */
    public static function getTotalAmount($nbPerType, $isFullDay) {
        
        return $nbPerType['normal'] * ($isFullDay ? 16 : 8) +
                $nbPerType['reduct'] * ($isFullDay ? 10 : 5) + $nbPerType['child'] *
                ($isFullDay ? 8 : 4) + $nbPerType['senior'] * ($isFullDay ? 12 : 6);
        
        //NbAndTotal::getTotalAmount()
    }
}

