<?php

namespace Tests\APProjet4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase 
{
    
   private $client = null;
    
    public function setUp() 
    {
        $this->client = static::createClient();
    }
    
    //Test if the Homepage is up
    public function testHomePageIsUp() 
    {
        $this->client->request('GET', '/');
        
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }
    
    public function testIndexPageIsup()
    {
       
        
//        $repo= $this->getMockBuilder('APProjet4\BookingBundle\Repository\EventRepository')
//            ->disableOriginalConstructor()
//            ->getMock();
//        
//        $repo
//            ->method('findAll')
//            ->willReturn([]);
        
        $this ->client->request('GET', '/events');
        
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }
}
