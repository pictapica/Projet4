<?php

//src/APProjet4/BookingBundle/Validator/Constraints/isHourPast.php

namespace APProjet4\BookingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/*
 * Annotation
 */

class IsHourPast extends Constraint {
    public $message = "Il est impossible d'acheter un billet de type\"Journée après 14h";
     
}

