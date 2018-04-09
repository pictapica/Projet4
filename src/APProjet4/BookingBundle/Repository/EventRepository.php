<?php

namespace APProjet4\BookingBundle\Repository;

use Doctrine\ORM\Tools\Pagination\Paginator;

class EventRepository extends \Doctrine\ORM\EntityRepository
{
    public function getEvents($page, $nbPerPage)
  {
    $query = $this->createQueryBuilder('a')
      ->getQuery()
    ;
    $query
      // On définit l'annonce à partir de laquelle commencer la liste
      ->setFirstResult(($page-1) * $nbPerPage)
      // Ainsi que le nombre d'annonce à afficher sur une page
      ->setMaxResults($nbPerPage)
    ;
    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
    // (n'oubliez pas le use correspondant en début de fichier)
    return new Paginator($query, true);
  }
  
     public function myFindAll()
  {
    // Méthode 2 : en passant par le raccourci (je recommande)
    $queryBuilder = $this->createQueryBuilder('a');
    // On n'ajoute pas de critère ou tri particulier, la construction
    // de notre requête est finie
    // On récupère la Query à partir du QueryBuilder
    $query = $queryBuilder->getQuery();
    // On récupère les résultats à partir de la Query
    $results = $query->getResult();
    // On retourne ces résultats
    return $results;
  }
}
