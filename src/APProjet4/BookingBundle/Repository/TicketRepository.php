<?php

namespace APProjet4\BookingBundle\Repository;

use Doctrine\ORM\EntityRepository;


class TicketRepository extends EntityRepository {
    
    //To do : ecrire la fonction permettant de récuperer le nombre de tickets vendu par date de visite

    public function countTicketsPerDay() 
    {
        $qb = $this->createQueryBuilder('t')
        
            ->select('COUNT(t.id) as nbTickets')// On compte le nombre d'id de tickets
            ->groupBy('t.visitDate')
            ->having('nbTickets >= 1000');

                
        // On retourne les résultats
        return $nbtickets = $qb
            ->getQuery()
            ->getResult()
        ;
    }
    
    
}
//select count(*) as "nbTickets" 
//from tickets 
//group by visitDate
//having "nbTickets" >=1000

//on compte le nombre d'id de tickets vendus pour la même date de visite
////si pour une date, le nb de ticket est > à 1000 on retourne la date (un tableau)
//select('COUNT(t.id)')
