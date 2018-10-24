<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use APProjet4\BookingBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContactDetailsController extends Controller {

    const MINCHILD = 4;
    const MAXCHILD = 12;
    const MAXADULT = 59;
    const MINSENIOR = 60;
    const MAXSENIOR = 120;

    /**
     * Affichage de la page de saisie des informations visiteur
     * 
     * @param Request $request
     * @return type
     * @throws NotFoundHttpException
     */
    public function showContactDetailsAction(Request $request) {
        // Récupération de la session
        $session = $request->getSession();

        //récupération des variables en session
        $visitDate = $session->get('visitDate');
        $booking = $session->get('booking');
        $id = $session->get('event_id');

        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($id);
        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }

        return $this->render('APProjet4BookingBundle:Booking:contactDetails.html.twig', [
                    'id' => $id,
                    'visitDate' => $visitDate,
                    'booking' => $booking,
                    'fullDay' => $booking->getFullDay(),
                    'event' => $event,
        ]);
    }

    /**
     * Enregistrement des informations visiteur
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function postContactDetailsAction(Request $request) {
        // Récupération de la session
        $session = $request->getSession();

        $visit = $session->get('visitDate');
        $visitDate = date_create($visit);
        $booking = $session->get('booking');
        $ticket = new Ticket();
        $errors = [];

        $tickets = $request->get('tickets');
        foreach ($tickets as $index => $ticket) {
            $t = new Ticket();
            $t->setFareType($ticket['fareType']);
            $t->setFirstname($ticket['firstname']);
            $t->setLastname($ticket['lastname']);
            $t->setDateOfBirth(date_create($ticket['dateOfBirth']));
            $t->setCountry($ticket['country']);
            $t->setBooking($booking);
            $t->setVisitDate($visitDate);
            try {
                $this->checkAgeAndFare($visitDate, (date_create($ticket['dateOfBirth'])), ($ticket['fareType']));
            } catch (Exception $ex) {
                $errors[] = [$index,$ex->getMessage()];
            }
            $booking->addTicket($t);
        }

        if (count($errors) > 0) {
            $response = [
                'success' => false,
                'errors' => $errors
            ];
            return new JsonResponse($response);
        }

        //On compte le nombre total de billets
        $nbTickets = count($tickets);

        //On renseigne les valeurs 
        $booking->setNbTickets($nbTickets);

        //On enregistre les valeurs en session
        $session->set('booking', $booking);
        $session->set('nbTickets', $nbTickets);

        //Si tout est bon on retourne un réponse success
        $response = [
            'success' => true
        ];
        return new JsonResponse($response);
    }

    /**
     * Retourne l'age en fonction de la date de naissance
     * 
     * @param type $visitDate
     * @param type $dateOfBirth
     * @return type
     */
    private function getAge($visitDate, $dateOfBirth) {
        $datediff = $visitDate->diff($dateOfBirth);
        $age = $datediff->format('%y');
        return $age;
    }

    /**
     * Retourne true si dans le bon interval
     * @param string $age
     * @return boolean
     */
    private function IsChild($age) {
        return (($age >= self::MINCHILD) && ($age < self::MAXCHILD));
    }

    /**
     * Retourne true si dans le bon interval
     * @param string $age
     * @return boolean
     */
    private function IsNormal($age) {
        return (($age >= self::MAXCHILD) && ($age <= self::MAXADULT));
    }

    /**
     * Retourne true si dans le bon interval
     * @param string $age
     * @return boolean
     */
    private function isSenior($age) {
        return (($age >= self::MINSENIOR) && ($age <= self::MAXSENIOR));
    }

    /**
     * Retourne true si le tarif choisi correspond à la date de naissance sinon Exception
     * @param date $visitDate
     * @param date $dateOfBirth
     * @param string $fareType
     * @return boolean
     * @throws Exception
     */
    private function checkAgeAndFare($visitDate, $dateOfBirth, $fareType) {
        $age = $this->getAge($visitDate, $dateOfBirth);
        switch ($fareType) {
            case 'child':
                if (!$this->isChild($age)) {
                    throw new Exception("Le tarif enfant s'applique entre 4 et 12 ans. Les enfants de moins de 4 ans n'ont pas besoin de billet d'entrée"); //Verifie age entre 4 et 12
                }
                break;
            case 'normal':
                if (!$this->isNormal($age)) {
                    throw new Exception("Le tarif normal s'applique entre 12 et 59 ans"); //Vérifie age entre 12 et <60
                }
                break;
            case 'senior':
                if (!$this->isSenior($age)) {
                    throw new Exception("Le tarif senior s'applique dès 60 ans"); // Vérifie age >60
                }
                break;
            case 'reduct':
                return true;
            default:
                throw new Exception("Il semble qu'il y ait eu un problème. Merci de vérifier les informations saisies.");
        }
    }
}
