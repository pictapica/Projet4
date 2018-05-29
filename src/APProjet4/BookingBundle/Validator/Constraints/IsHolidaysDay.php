<?php

//src/APProjet4/BookingBundle/Validator/Constraints/IsHolidaysDay.php

namespace APProjet4\BookingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/*
 * Annotation
 */

class IsHolidaysDay extends Constraint {
    public $message = "Le musée est fermé les 1er mai, 1er novembre et 25 décembre";
     
}