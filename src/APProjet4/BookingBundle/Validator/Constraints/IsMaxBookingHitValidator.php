<?php

//src/APProjet4/BookingBundle/Validator/Constraints/IsMaxBookingHitValidator.php

namespace APProjet4\BookingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsMaxBookingHitValidator extends ConstraintValidator {

    const MAX_TICKETS_PER_DAY = 1000;
    
    public function validate(Request $request, Constraint $constraint) {

        //Nb de tickets présents en Bdd
        //nb de ticket + nb de tickets choisis par le visiteur
        //On récupère la date de visite
        $d = new \DateTime($request->get('visitDate'));
        //On récupère les tickets
        $tickets = $this->getDoctrine()
                ->getRepository('APProjet4BookingBundle:Booking')
                ->findByVisitDate($d);
        console . log($tickets);
       
        //On compte les tickets
            if (count($tickets) > MAX_TICKETS_PER_DAY) {
                //On invalide
                $this->context->buildViolation($constraint->message)->addViolation();
            }
        
            //On doit savoir combien de billets ont été choisi par le visiteur 
            //pour additionner billet en bdd pour ce jour et billet commandés
    }
}
