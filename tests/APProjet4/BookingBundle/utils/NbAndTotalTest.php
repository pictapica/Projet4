<?php

namespace Tests\APProjet4\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use APProjet4\BookingBundle\Utils\NbAndTotal;
use APProjet4\BookingBundle\Entity\Ticket;

class NbAndTotalTest extends WebTestCase {

    public function testGetNbPerType() {

        $this->assertSame([
            'normal' => 1,
            'reduct' => 0,
            'child' => 0,
            'senior' => 0], NbAndTotal::getNbPerType([new Ticket('normal')]));
        $this->assertSame([
            'normal' => 0,
            'reduct' => 1,
            'child' => 0,
            'senior' => 0], NbAndTotal::getNbPerType([new Ticket('reduct')]));
        $this->assertSame([
            'normal' => 0,
            'reduct' => 0,
            'child' => 1,
            'senior' => 0], NbAndTotal::getNbPerType([new Ticket('child')]));
        $this->assertSame([
            'normal' => 0,
            'reduct' => 0,
            'child' => 0,
            'senior' => 1], NbAndTotal::getNbPerType([new Ticket('senior')]));
    }

    //Test if array is empty
    public function testEmptyArray() {
        $this->assertEquals(0, NbAndTotal::getTotalAmount([], true));
    }

    public function testExceptionIfNotAnArray() {
        $this->expectException(get_class(new \Exception()));
        NbAndTotal::getTotalAmount(12, true);
    }

    //Test ticket 'Normal'
    public function testNormal() {
        $this->assertEquals(16, NbAndTotal::getTotalAmount(['normal' => 1], true));
        $this->assertEquals(8, NbAndTotal::getTotalAmount(['normal' => 1], false));
    }

    //Test ticket 'Reduct'
    public function testReduct() {
        $this->assertEquals(10, NbAndTotal::getTotalAmount(['reduct' => 1], true));
        $this->assertEquals(5, NbAndTotal::getTotalAmount(['reduct' => 1], false));
    }

    //Test ticket 'Child'
    public function testChild() {
        $this->assertEquals(8, NbAndTotal::getTotalAmount(['child' => 1], true));
        $this->assertEquals(4, NbAndTotal::getTotalAmount(['child' => 1], false));
    }

    //Test ticket 'Senior'
    public function testSenior() {
        $this->assertEquals(12, NbAndTotal::getTotalAmount(['senior' => 1], true));
        $this->assertEquals(6, NbAndTotal::getTotalAmount(['senior' => 1], false));
    }

    protected $nbTicketArray;

    public function setUp() {
        $this->nbTicketArray = [
            'normal' => 1,
            'reduct' => 1,
            'child' => 1,
            'senior' => 1
        ];
    }

    public function testGetTotalAmount() {
        $this->assertEquals(46, NbAndTotal::getTotalAmount($this->nbTicketArray, true));
    }

//    public function testNbEntryInArray() {
//        $this->assertCount(4, $this->nbTicketArray);
//    }

    /**
     * Test de la présence des clés dans le tableau
     */
    public function getNbPerTypeTest() {
        $this->assertArrayHasKey('normal', NbAndTotal::getTotalAmount(['normal' => 1, 'reduct' => 0, 'child' => 0, 'senior' => 0], true));
    }

}
