<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingController extends Controller {

    // Une action par affichage de page, une action par changement de page
    public function homeAction() {

        $content = $this->get('templating')->render('APProjet4BookingBundle:Booking:home.html.twig');
        return new Response($content);
    }

    public function indexAction() {

        $repository = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('APProjet4BookingBundle:Event')
        ;

        $listEvents = $repository->findAll();


        // On donne toutes les informations nécessaires à la vue
        return $this->render('APProjet4BookingBundle:Booking:index.html.twig', array(
                    'listEvents' => $listEvents
        ));
    }

    public function testAction($id) {
        // On récupère le repository
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

        return $this->render('APProjet4BookingBundle:Booking:test.html.twig', array(
                    'event' => $event,
                    'url' => $url,
                    'alt' => $alt
        ));
    }

    /**
     *    Au clic sur l'expo de son choix -> selectDate.html.twig
     *    Vérification du nombre de billets vendus dans la journée (<1000)
     * 1   si "true" :  affichage du calendrier (impossible de commander pour les jours passés, 
     *    les dimanches, les jours fériés et les jours où il y a déjà 100 billets vendus)
     * 
     */
    public function selectDateAction() {

        $content = $this->get('templating')->render('APProjet4BookingBundle:Booking:selectDate.html.twig');
        return new Response($content);
    }

    /**
     *    Choix de la date
     * 2  Sauvegarde de la date choisie

     *    Choix JOURNEE ou DEMI-JOURNEE
     * 3  Vérification de l'heure de la commande si >14h impossible de commander 
     *    des billets journée pour le jour-même 
     *    Message 

     *    Choix du nombre de billets
     * 4  Calcul le total selon le montant de chaque billet 
     *    
     * 
     */
    
    public function contactDetailsAction() {
        $content = $this->get('templating')->render('APProjet4BookingBundle:Booking:contactDetails.html.twig');
        return new Response($content);
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
     
