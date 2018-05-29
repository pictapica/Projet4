<?php

//src/APProjet4/BookingBundle/Validator/Constraints/IsHolidaysDayValidator.php

namespace APProjet4\BookingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsHolidaysDayValidator extends ConstraintValidator {

    public function validate($visitDay, Constraint $constraint) {

        //Si le jour de visite choisi est un jour férié
        if ($visitDay->format('D') == 'mardi' || $visitDay->format('D') == 'Dimanche'){
            //On invalide
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

}





