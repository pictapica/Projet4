<?php

//src/APProjet4/BookingBundle/Validator/Constraints/IsItADisableDay.php

namespace APProjet4\BookingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/*
 * @Annotation
 */

class IsItADisableDay extends Constraint {
    public $message = "Le musée est fermé tous les mardis. "
            . "Pour acheter un billet pour le dimanche, merci de vous rendre aux guichets du musée";
     
}