<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use APProjet4\BookingBundle\Entity\Booking;
use APProjet4\BookingBundle\Form\BookingType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class BookingController extends Controller {

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

    ////////////////Test////////////////

    public function testAction($id) {
        $repository = $this->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Event')
        ;

        $event = $repository->find($id);

        $url = $event->getImage()->getUrl();
        $alt = $event->getImage()->getAlt();

        if (null === $event) {
            throw new NotFoundHttpException("L'évènement d'id " . $id . " n'existe pas.");
        }

        return $this->render('APProjet4BookingBundle:Booking:contactDetails.html.twig', array(
                    'event' => $event,
                    'url' => $url,
                    'alt' => $alt
        ));
    }

    ////////////////Date & jour////////////////
    /**
     *    Au clic sur l'expo de son choix -> selectDate.html.twig
     *    Vérification du nombre de billets vendus dans la journée (<1000)
     * 1   Affichage du calendrier (impossible de commander pour les jours passés, 
     *    les dimanches, les jours fériés et les jours où il y a déjà 1000 billets vendus)
     */
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

    /**
     *    Choix de la date
     * 2  Sauvegarde de la date choisie
     *    Choix JOURNEE ou DEMI-JOURNEE 
     */
    public function maxAction(Request $request) {
        define("MAX_BOOKING_DATE", 1000);
        if ($request->isXmlHttpRequest()) {
            $d = new \DateTime($request->get('date'));

            $bookings = $this->getDoctrine()
                    ->getRepository('APProjet4BookingBundle:Booking')
                    ->findByDate($d);

            if (!$bookings) {
                $response = array(
                    'availability' => true,
                );
            } else {
                if (count($bookings) > MAX_BOOKING_DATE) {
                    $response = array(
                        'availability' => false,
                    );
                } else {
                    $response = array(
                        'availability' => true,
                    );
                }
            }
            return new JsonResponse($response);
        }
    }

    /* 3  Vérification de l'heure de la commande si >14h impossible de commander 
     *    des billets journée pour le jour-même 
     *    Message */

    public function saveDateAction(Request $request, $orderCode) {
        $em = $this->getDoctrine()->getManager();
        if ($orderCode) {
            $booking = $em->getRepository('APProjet4BookingBundle:Booking')->findOneByOrderCode($orderCode);
            if (!booking) {
                return $this->redirectToRoute('approjet4_booking_selectDate');
            } else {
                if ($booking->getStatus() === Booking::STATUS_PAID) {
                    return $this->redirectToRoute('approjet4_booking_paid', array($orderCode => $booking->getOrderCode()
                    ));
                }
            }
        } else {
            $booking = new Booking();
            $em->persist($booking);

            $visitDate = $request->query->get('pick_a_date');
            if (!$visitDate) {
                $this->getFlashBag()
                        ->add('notice', 'Vous devez saisir une date de visite!');
            } else {
                if (availability === true) {

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($visitDate);
                    $em->flush();
                } else {
                    $this->getFlashBag()
                            ->add('notice', 'Le nombre de visiteurs possible pour cette journée est dépassé');
                }
            }
        }
    }

    /*
     *   Choix du nombre de billets
     * 4  Calcul le total selon le montant de chaque billet 
     */

    public function contactDetailsShowAction() {
        $form1 = $this->createForm(BookingType::class);
        return $this->render('APProjet4BookingBundle:Booking:contactDetails.html.twig', array(
                    'form1' => $form1->createView(),
        ));
    }

    public function saveContactDetailsAction(Request $request) {
        $booking = new Booking();

        if ($request->isMethod('POST') && $form1->handelRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($booking);
            $em->flush();
            return $this->redirecToRoute('approjet4_booking_email', array('id' => $booking->getId()));
        }
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
     *    
     * 
     */
/**
     *    Fina
     * 10  
     *    
     * 
     */
     
