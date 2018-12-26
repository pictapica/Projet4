<?php

namespace Tests\APProjet4\BookingBundle\Services;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use APProjet4\BookingBundle\Services\NbAndTotalSystem;
use APProjet4\BookingBundle\Entity\Ticket;



class NbAndTotalSystemTest extends WebTestCase {

    public function testGetNbPerType() {
        
        $nbAndTotal = new NbAndTotalSystem();

        $this->assertSame([
            'normal' => 1,
            'reduct' => 0,
            'child' => 0,
            'senior' => 0,
            'baby'=> 0], $nbAndTotal->getNbPerType([new Ticket('normal')]));
        $this->assertSame([
            'normal' => 0,
            'reduct' => 1,
            'child' => 0,
            'senior' => 0,
            'baby'=> 0], $nbAndTotal->getNbPerType([new Ticket('reduct')]));
        $this->assertSame([
            'normal' => 0,
            'reduct' => 0,
            'child' => 1,
            'senior' => 0,
            'baby'=> 0], $nbAndTotal->getNbPerType([new Ticket('child')]));
        $this->assertSame([
            'normal' => 0,
            'reduct' => 0,
            'child' => 0,
            'senior' => 1,
            'baby'=> 0], $nbAndTotal->getNbPerType([new Ticket('senior')]));
        $this->assertSame([
            'normal' => 0,
            'reduct' => 0,
            'child' => 0,
            'senior' => 0,
            'baby'=> 1], $nbAndTotal->getNbPerType([new Ticket('baby')]));
    }

    //Test if array is empty
    public function testEmptyArray() {
        $nbAndTotal = new NbAndTotalSystem();
        $this->assertEquals(0, $nbAndTotal->getTotalAmount([], true));
    }

    public function testExceptionIfNotAnArray() {
        $nbAndTotal = new NbAndTotalSystem();
        $this->expectException(get_class(new \Exception()));
        $nbAndTotal->getTotalAmount(12, true);
    }

    //Test ticket 'Normal'
    public function testNormal() {
        $nbAndTotal = new NbAndTotalSystem();
        $this->assertEquals(16, $nbAndTotal->getTotalAmount(['normal' => 1], true));
        $this->assertEquals(8, $nbAndTotal->getTotalAmount(['normal' => 1], false));
    }

    //Test ticket 'Reduct'
    public function testReduct() {
        $nbAndTotal = new NbAndTotalSystem();
        $this->assertEquals(10, $nbAndTotal->getTotalAmount(['reduct' => 1], true));
        $this->assertEquals(5, $nbAndTotal->getTotalAmount(['reduct' => 1], false));
    }

    //Test ticket 'Child'
    public function testChild() {
        $nbAndTotal = new NbAndTotalSystem();
        $this->assertEquals(8, $nbAndTotal->getTotalAmount(['child' => 1], true));
        $this->assertEquals(4, $nbAndTotal->getTotalAmount(['child' => 1], false));
    }

    //Test ticket 'Senior'
    public function testSenior() {
        $nbAndTotal = new NbAndTotalSystem();
        $this->assertEquals(12, $nbAndTotal->getTotalAmount(['senior' => 1], true));
        $this->assertEquals(6, $nbAndTotal->getTotalAmount(['senior' => 1], false));
    }
    

    protected $nbTicketArray;

    public function setUp() {
        $this->nbTicketArray = [
            'normal' => 1,
            'reduct' => 1,
            'child' => 1,
            'senior' => 1,
            'baby' =>1,
        ];
    }

    public function testGetTotalAmount() {
        $nbAndTotal = new NbAndTotalSystem();
        $this->assertEquals(46, $nbAndTotal->getTotalAmount($this->nbTicketArray, true));
    }

    /**
     * Test de la présence des clés dans le tableau
     */
    public function getNbPerTypeTest() {
        $nbAndTotal = new NbAndTotalSystem();
        $this->assertArrayHasKey('normal', $nbAndTotal->getTotalAmount(['normal' => 1, 'reduct' => 0, 'child' => 0, 'senior' => 0, 'baby' =>0], true));
    }

}
