<?php

//src/APProjet4/BookingBundle/Validator/Constraints/IsTooOld.php

namespace APProjet4\BookingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/*
 * Annotation
 */

class IsTooOld extends Constraint {
    public $message = "Merci de vérifier la date de naissance";
     
}