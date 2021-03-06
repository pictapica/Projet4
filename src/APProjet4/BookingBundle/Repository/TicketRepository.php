<?php

namespace APProjet4\BookingBundle\Repository;

use Doctrine\ORM\EntityRepository;


class TicketRepository extends EntityRepository {
    
    /**
     * récuperer le nombre de tickets vendu par date de visite
     * 
     */
    public function countTicketsPerDay() 
    {
        $qb = $this->createQueryBuilder('t')
        
            ->select('COUNT(t.id) as nbTickets')// On compte le nombre d'id de tickets
            ->groupBy('t.visitDate')
            ->having('nbTickets >= 1000');

                
        // On retourne les résultats
        return $nbtickets = $qb
            ->getQuery()
            ->getResult();
    }
}
