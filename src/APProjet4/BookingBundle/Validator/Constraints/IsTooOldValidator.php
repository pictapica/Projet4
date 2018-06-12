<?php

//src/APProjet4/BookingBundle/Validator/Constraints/IsTooOldValidator.php

namespace APProjet4\BookingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsTooOldValidator extends ConstraintValidator {

    public function validate($dateOfBirth, Constraint $constraint) {
        //Pour vérifier que la date de naissance est valable et plausible !
        $now = new \DateTime();
        $difference = date_diff($now, $dateOfBirth);
        $age = $difference->format('%y');
        
        if ($age >120) {
            $this->content->buildViolation($constraint->message)->addViolation();
        }
    }

}