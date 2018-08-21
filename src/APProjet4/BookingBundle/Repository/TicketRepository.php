<?php

namespace APProjet4\BookingBundle\Repository;

/**
 * TicketRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TicketRepository extends \Doctrine\ORM\EntityRepository {
    
    //To do : ecrire la fonction permettant de récuperer le nombre de tickets vendu pour le jour de la commande ($orderDate)
    
    public static function countTickets($orderDate) {
//        $qb = $this->createQueryBuilder('t');
//
//        $qb     ->select('count(t.id)')// On compte le nombre d'id de tickets
//                ->leftJoin('t.booking', 'b') //On lie avec l'entité booking
//                ->where('b.orderDate=:orderdate')//On défini un paramètre  => "orderDate"
//                ->setParameter('orderdate', $orderDate) //On attribut une valeur à ce paramètre 
//                ->andWhere('b.status>=:status') //On défini un second paramètre
//                ->setParameter('status', Booking::STATUS_PAID); // On attribut une valeur à ce paramètre
//        // On retourne les résultats
//        return $qb
//                ->getQuery()
//                ->getResult()
//        ;
//    

    
    //doit renvoyer un tableau de dates
    //On met tout dans attributs Tickets
    //qui sera un tableau contenant plein de tickets
    }
}
//On selectionne les dates pour lesquelles il y a des bookings de status_Paid et pour chaque date on compte le nombre d'id de tickets
//si le nb de ticket est > à 1000 on retourne la date (un tableau)