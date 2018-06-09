<?php

//src/APProjet4/BookingBundle/Validator/Constraints/IsMaxBookingHitValidator.php

namespace APProjet4\BookingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsMaxBookingHitValidator extends ConstraintValidator {

    public function validate($visitDay, Constraint $constraint) {

        //Nb de tickets présents en Bdd
        //nb de ticket + nb de tickets choisis par le visiteur
        }
    

}





