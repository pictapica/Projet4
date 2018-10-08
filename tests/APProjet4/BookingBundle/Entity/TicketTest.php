<?php

namespace Tests\APProjet4\BookingBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use APProjet4\BookingBundle\Entity\Ticket;

class TicketTest extends WebTestCase {
    
    //Test l'hydratation du formulaire 
    public function testHydratation(){
        
        $ticket = new Ticket();
        
        $ticket->setLastname('Dupont');
        $ticket->setFirstname('Marc');
        $ticket->setDateOfBirth('1979-06-24');
        $ticket->setFareType('normal');
        $ticket->setVisitDate('2018-12-12');
        $ticket->setBooking();
        
        $this->assertEquals('Dupont', $ticket->getLastname());
        $this->assertEquals('Marc', $ticket->getFirstname());
        $this->assertEquals('1979-06-24', $ticket->getDateOfBirth());
        $this->assertEquals('normal', $ticket->getFareType());
        $this->assertEquals('2018-12-12', $ticket->getVisitDate());
        $this->assertNull($ticket->getBooking());
    }
    
}
