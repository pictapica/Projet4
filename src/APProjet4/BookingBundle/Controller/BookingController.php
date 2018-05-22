<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use APProjet4\BookingBundle\Entity\Booking;
use APProjet4\BookingBundle\Entity\Ticket;
use APProjet4\BookingBundle\Form\BookingType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session;

class BookingController extends Controller {

    const MAX_TICKETS_PER_DAY = 1000;

    //////////////// Affichage de la page d'accueil////////////////

    public function homeAction() {
        $content = $this->get('templating')->render('APProjet4BookingBundle:Booking:home.html.twig');
        return new Response($content);
    }

    ////////////////Affichage de la liste des évènements////////////////

    public function indexAction() {
        $repository = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Event');

        $listEvents = $repository->findAll();

        return $this->render('APProjet4BookingBundle:Booking:index.html.twig', array(
                    'listEvents' => $listEvents
        ));
    }

    ////////////////Date & type de billet////////////////

    public function selectDateAction($id) {
        $repository = $this->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }

        return $this->render('APProjet4BookingBundle:Booking:selectDate.html.twig', array(
                    'event' => $event,
        ));
    }

    ///////////////Quota par jour///////////////////////

    public function maxAction(Request $request) {

        //On récupère les tickets par date de visite
        //Si pas de tickets => availability = true 
        //Sinon on les compte (count($tickets) > MAX_TICKETS_PER_DAY
        //On revoie une reponse availability => false
        //sinon true
        //On récupère un tableau avec date availability = false qui va s'ajouter à datesDisabled
        //A utiliser dans la vue selectDate : ex: datesDisabled: [("2018/05/01"),("2018/11/01"),("2018/12/25")],


        $d = new \DateTime($request->get('visitDate'));
        $tickets = $this->getDoctrine()
                ->getRepository('APProjet4BookingBundle:Booking')
                ->findByVisitDate($d);
        if (!$tickets) {
            $response = [
                'availability' => true,
            ];
        } else {
            if (count($tickets) > MAX_TICKETS_PER_DAY) {
                console . log($tickets);
                $response = [
                    'availability' => false,
                ];
            } else {
                $response = [
                    'availability' => true,
                ];
            }
        }
        console . log($response);
        return new JsonResponse($response);
    }

    ////////////////Sauvegarde de la date/////////////////

    public function saveDateAction(Request $request) {

//If submitData True -> tarifs entiers if submitdata false ->1/2tarifs
        // Récupération de la session
        $session = $request->getSession();

        if (!$request->isMethod('POST')) {
            throw new \Exception('Méthode post attendue!');
        }
        //Vérification de l'absence des paramètres date, fullday et id event, 
        //Vérification si date dispo
        if (!('visitDate') || !('isFullDay') || !('id')) {

            $response = [
                'success' => false
            ];
            return new JsonResponse($response);
        }
        //Vérification de la validité des paramètres ? isHourPast(), isItAPastDay(), isItADisabledDay()
        //Vérification  et <1000 billets vendus ( On doit récupérer 
        // résultat fonction countTicketsPerDay dans le repository ou dire que if ($maxbookingAction = true) bla bla 
        //Vérification du billet journée pour la date actuelle (<14h)
        //Enregistrer la date sélectionnée, le type de ticket et l'id de l'évènement 
        $visitDate = $request->get('visitDate');
        $id = $request->get('id');
        $isFullDay = $request->get('isFullDay');
//
//        // On définit une nouvelle valeur pour ces variables
        $booking_visitDate = $session->set('visitDate', $visitDate);
        $event_id = $session->set('id', $id);
        $booking_isFullDay = $session->set('isFullDay', $isFullDay);

//        // On récupère le service validator
//        $validator = $this->get('validator');
//
//        // On déclenche la validation sur notre object
//        $listErrors = $validator->validate($ticket);
//
//        // Si $listErrors n'est pas vide, on affiche les erreurs
//        if (count($listErrors) > 0) {
//            // $listErrors est un objet, sa méthode __toString permet de lister joliement les erreurs
//            return new Response((string) $listErrors);
//        } else {
//            return new Response("L'annonce est valide !");
//        }
        if ('maxAction(true)') {
//        // On renvoie une réponse success
            $response = [
                'success' => true
            ];
            return new JsonResponse($response);
        }
    }

    /*
     *   Choix du nombre de billets
     * 4  Calcul le total selon le montant de chaque billet 
     */

    public function deleteFormAction() {
        
    }

    public function ContactDetailsAction(Request $request) {
        // Récupération de la session
        $session = $request->getSession();
        //récupération des variables en session
        $visitDate = $session->get('visitDate');
        $id = $session->get('id');
        $isFullDay = $session->get('isFullDay');

        $repository = $this->getDoctrine()->getManager()->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($id);
        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }

        $booking = new Booking();
        $ticket = new Ticket();

        $form1 = $this->createForm(BookingType::class);

        if ($request->isMethod('POST') && $form1->handelRequest($request)->isValid()) {
            $booking->setStatus(Booking::STATUS_INPROGRESS);
            $ticket = $form1->getData();
            $ticket->setFirstname();
            $this->get('session')->set('Booking', $booking);
            $this->get('session')->set('Ticket', $ticket);

            $session->getFlashBag()->add('info','Informations enregistrées');
            return $this->redirecToRoute('approjet4_booking_showRecap', array('id' => $booking->getId()));
        }
        return $this->render('APProjet4BookingBundle:Booking:contactDetails.html.twig', array(
                    'form1' => $form1->createView(),
                    'visitDate' => $visitDate,
                    'id' => $id,
                    'isFullDay' => $isFullDay,
                    'event' => $event));
    }

    public function showRecapAction(Request $request) {
        $session = $request->getSession();

        $visitDate = $session->get('visitDate');
        $id = $session->get('id');
        $isFullDay = $session->get('isFullDay');

        $repository = $this->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Event');
        $event = $repository->find($id);

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }

        return $this->render('APProjet4BookingBundle:Booking:recap.html.twig', array(
                    'visitDate' => $visitDate,
                    'id' => $id,
                    'isFullDay' => $isFullDay,
                    'event' => $event));
    }

}

/**
     *    Clic sur le champs e-mail
     * 5  Si aucun billet rempli : message d'erreur
     *    
     * 
     */
     /**
     *    Saisi du 2 ème email
     * 6  Si différent du premier : message d'erreur
      * 
     *    $confirmation.keyup(function(){
    if($(this).val() != $mdp.val()){ // si la confirmation est différente du mot de passe
        $(this).css({ // on rend le champ rouge
	    borderColor : 'red',
	    color : 'red'
        });
    }
    else{
	$(this).css({ // si tout est bon, on le rend vert
	    borderColor : 'green',
	    color : 'green'
	});
    }
});

     * 
     */
     /**
     *    Clic sur CONFIRMER
     * 7  Vérification et enregistrement de l'adresse e-mail
     *    Enregistre la commande dans le panier
     * 
     */
     /**
     *    Récupération du total en euros et du détail du nombre de billets pour chaque type de billets.
     * 8  Vérification des informations client (string, date..)
     *    
     * 
     */
     /**
     *    Commander d'autres billets 
     * 9  Enregistre les informations client 
     *    $em = $this->getDoctrine()->getManager();
            $em->persist($booking);
            $em->persist($ticket);
            $em->flush();
     * 
     */
/**
     *    Fina
     * 10  
     *    
     * 
     */
     
