<?php

namespace APProjet4\BookingBundle\Utils;

use Symfony\Component\Config\Definition\Exception\Exception;

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
        if (!isset($tickets)){
            throw new Exception('Attention $tickets est un tableau vide');
        }
        //if (isset($tickets)){
                foreach ($tickets as $ticket) {
                    $nbPerType[$ticket->getFaretype()] ++;
           // }
        }
        return $nbPerType;
    }

    /**
     * Retourne le montant total en euro
     * 
     * @param type $nbPerType
     * @param type $isFullDay
     * @return int
     */
    public static function getTotalAmountOld($nbPerType, $isFullDay) {
        
        if (!isset($nbPerType)){
            throw new Exception('oh non ! Le tableau est vide !'); 
        }   
        if (!isset($nbPerType['normal']) || !isset($nbPerType['reduct']) || 
                !isset($nbPerType['child']) || !isset($nbPerType['senior'])) {
            throw new Exception('oh non ! Il manque un morceau de tableau !'); 
        }

        if (($nbPerType['normal']) !== null && ($nbPerType['reduct']) !== null && 
                ($nbPerType['child']) !== null && ($nbPerType['senior'])  !== null){
            return ($nbPerType['normal'] * ($isFullDay ? 16 : 8)) +
                    ($nbPerType['reduct'] * ($isFullDay ? 10 : 5)) + ($nbPerType['child'] *
                    ($isFullDay ? 8 : 4)) + ($nbPerType['senior'] * ($isFullDay ? 12 : 6)); 
        }
    }
    
    public static function getTotalAmount($nbPerType, $isFullDay) {
        if (!is_array($nbPerType)){
            throw new Exception(' An array is excepted !'); 
        }
        
        if (empty($nbPerType)){
            return 0;
        }
        $ret = 0;
        if (isset($nbPerType['normal'])){
            $ret += ($nbPerType['normal'] * ($isFullDay ? 16 : 8));
        }
        if (isset($nbPerType['child'])){
            $ret += ($nbPerType['child'] * ($isFullDay ? 8 : 4));
        }
        if (isset($nbPerType['reduct'])){
            $ret += ($nbPerType['reduct'] * ($isFullDay ? 10 : 5));
        }
        if (isset($nbPerType['senior'])){
            $ret += ($nbPerType['senior'] * ($isFullDay ? 12 : 6));
        }
        
        return $ret;
    }
}
            
