<?php

//src/APProjet4/BookingBundle/Controller/BookingController.php

namespace APProjet4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller {

    // Une action par affichage de page, une action par changement de page
    public function indexAction() {

        $content = $this->get('templating')->render('APProjet4BookingBundle:Booking:index.html.twig');
        return new Response($content);
    }

    public function newOrderAction() {
        $content = $this->get('templating')->render('APProjet4BookingBundle:Booking:newOrder.html.twig');
        return new Response($content);
    }

}

/**
     *    Au clic sur l'expo de son choix -> newOrder.html.twig
     *    Vérification du nombre de billets vendus dans la journée (<1000)
     * 1   si "true" :  affichage du calendrier (impossible de commander pour les jours passés, 
     *    les dimanches, les jours fériés et les jours où il y a déjà 100 billets vendus)
     * 
     */
   
     /**
     *    Choix de la date
     * 2  Sauvegarde de la date choisie
     *    
     * 
     */
     /**
     *    Choix JOURNEE ou DEMI-JOURNEE
     * 3  Vérification de l'heure de la commande si >14h impossible de commander 
     *    des billets journée pour le jour-même 
     *    Message 
     */
     /**
     *    Choix du nombre de billets
     * 4  Calcul le total selon le montant de chaque billet 
     *    
     * 
     */
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
     *    
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
     * 9  
     *    
     * 
     */
     
