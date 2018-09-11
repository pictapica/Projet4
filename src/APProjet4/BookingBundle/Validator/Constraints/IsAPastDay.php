<?php

//src/APProjet4/BookingBundle/Validator/Constraints/isItAPastDay.php

namespace APProjet4\BookingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/*
 * @Annotation
 */

class isItAPastDay extends Constraint {
    public $message = "Acheter pour un jour passé? Quelle drôle d'idée !";
     
}