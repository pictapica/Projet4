<?php

//namespace Tests\APProjet4\BookingBundle\Controller;
//
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//use APProjet4\BookingBundle\Entity\Ticket;
//
//class ContactDetailsControllerTest extends WebTestCase {
//
//    //Test qu'on envoie bien une réponse json
//    //
//    //2 returns, 1 exception
//    //Soit retourner un objet $response false soit un objet $response true
//    public function testAddTickets() {
//        $client = static::createClient();
//        
//        $crawler = $client->request('GET', '/details');
//        
//        $form = $crawler->selectButton('Finaliser votre commande')->form();
//        $form['fareType'] = 'normal';
//        $form['lastname'] = 'Doe';
//        $form['firstname'] = 'John';
//        $form['dateOfBirth'] = '1979-06-24';
//        $form['country'] = 'FR';
//        $crawler = $client->submit($form);
//        
//        $client->followRedirect();
//        echo $client->getResponse()->getContent();
//    }
//
//}
