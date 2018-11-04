<?php

namespace Tests\APProjet4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SelectDateControllerTest extends WebTestCase {

    public function setUp() {
        $this->client = static::createClient();
    }

    /**
     *  Test if /purchase page exist ( false car true Url = /purchase/{id})
     */
    public function testShowSelectDateAction() {
        $this->client->request('GET', '/purchase');
        $this->assertFalse($this->client->getResponse()->isSuccessful());
    }
}
