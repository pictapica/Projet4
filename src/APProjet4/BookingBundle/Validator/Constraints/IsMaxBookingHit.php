<?php

//src/APProjet4/BookingBundle/Validator/Constraints/IsMaxBookingHit.php

namespace APProjet4\BookingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/*
 * Annotation
 */

class IsMaxBookingHit extends Constraint {
    public $message = "Le nombre de billets vendu pour la journée a été atteint. Merci de commander pour un autre jour";
     
}