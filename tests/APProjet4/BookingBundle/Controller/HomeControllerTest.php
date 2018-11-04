<?php

namespace Tests\APProjet4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase {

    private $client = null;

    public function setUp() {
        $this->client = static::createClient();
    }

    //Test if the Homepage is up
    public function testHomePageIsUp() {

        $this->client->request('GET', '/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    //Test if the homepage containt "Louvre"
    public function testIndex() {

        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertGreaterThan(
                0, $crawler->filter('html:contains("Louvre")')->count()
        );
    }

}
