<?php

//src/APProjet4/BookingBundle/Validator/Constraints/IsItAPastDayValidator.php

namespace APProjet4\BookingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsItAPastDayValidator extends ConstraintValidator {

    public function validate($visitDay, Constraint $constraint) {

        $now = new \DateTime();
        //Si le jour de visite choisi est antérieur à aujourd'hui
        if ($visitDay->format('Y-m-d') < $now->format('Y-m-d')) {
            //On invalide
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

}
