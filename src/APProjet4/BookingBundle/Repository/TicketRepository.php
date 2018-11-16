<?php

namespace APProjet4\BookingBundle\Repository;

use Doctrine\ORM\EntityRepository;


class TicketRepository extends EntityRepository {
    
    /**
     * Collect the number of tickets sold per visit date
     * 
     */
    public function countTicketsPerDay() 
    {
        $qb = $this->createQueryBuilder('t')
        
            ->select('COUNT(t.id) as nbTickets')// We count the number of ticket ids
            ->groupBy('t.visitDate')
            ->having('nbTickets >= 1000');

                
        // We return the results
        return $nbtickets = $qb
            ->getQuery()
            ->getResult();
    }
}
