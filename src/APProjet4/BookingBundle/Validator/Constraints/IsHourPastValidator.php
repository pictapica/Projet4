<?php

//src/APProjet4/BookingBundle/Validator/Constraints/IsHourPastValidator.php

namespace APProjet4\BookingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsHourPastValidator extends ConstraintValidator {

    const HEURE_MAX = '14';

    public function validate(Constraint $constraint) {

        $now = new \DateTime();
        //Si la date de visite est aujourd'hui et qu'il est plus de 14h
        if (($this->visitDate->format('Ymd') === $now->format('Ymd')) && ($now->format('H') >= self::HEURE_MAX)) {
            //On invalide
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

}
