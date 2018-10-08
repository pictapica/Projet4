<?php

namespace Tests\APProjet4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SelectDateControllerTest extends WebTestCase {


    public function setUp() {
        $this->client = static::createClient();
    }

    /**
     *  Test if /purchase page exist
     */
    public function testShowSelectDateAction() {
        $this->client->request('GET', '/purchase');
        $this->assertFalse($this->client->getResponse()->isSuccessful());
    }

    /**
     * Test if the date select is not a tuesday
     * @dataProvider date
     */
    public function testIsADisabledDay($visitDate) {

        $visitDay = new \DateTime($visitDate);
        $day = $visitDay->format('l');
        $this->assertNotSame($day, 'Mardi');
    }

    public function date() {
        return [
            ['2018/10/20'],
            ['2019/01/17']
        ];
    }
    
}
