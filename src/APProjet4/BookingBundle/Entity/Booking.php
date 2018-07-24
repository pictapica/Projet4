<?php

namespace APProjet4\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use APProjet4\BookingBundle\Validator\Constraints\IsHourPast;

/**
 * Booking
 *
 * @ORM\Table(name="booking")
 * @ORM\Entity(repositoryClass="APProjet4\BookingBundle\Repository\BookingRepository")
 */
class Booking {

    const STATUS_START = 1;
    const STATUS_INPROGRESS = 2;
    const STATUS_PAID = 3;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="orderDate", type="datetime")
     * @Assert\DateTime()
     */
    private $orderDate;

    /**
     * @var string
     *
     * @ORM\Column(name="orderCode", type="string", length=12, unique=true)
     */
    private $orderCode = null;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var string
     * @ORM\column(name="email", type="string")
     * @Assert\Email()
     */
    private $email;

    /**
     * @var bool
     * @ORM\Column(name="fullDay", type="boolean")
     * 
     */
    private $fullDay = null;
    
    /**
     * @var int
     * @ORM\Column(name="nbTickets", type="integer")
     */
    private $nbTickets;
    
    /**
     * @ORM\OneToMany(targetEntity="Ticket", mappedBy="booking",cascade={"persist"})
     */
    private $tickets;
    
    

    public function __construct() {
        $this->status = self::STATUS_START;
        $this->orderDate = new \Datetime();
        $this->tickets = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set orderDate
     *
     * @param \DateTime $orderDate
     *
     * @return Booking
     */
    public function setOrderDate($orderDate) {
        $this->orderDate = $orderDate;

        return $this;
    }

    /**
     * Get orderDate
     *
     * @return \DateTime
     */
    public function getOrderDate() {
        return $this->orderDate;
    }

    /**
     * Set orderCode
     *
     * @param string $orderCode
     *
     * @return Booking
     */
    public function setOrderCode($orderCode) {
        $this->orderCode = $orderCode;

        return $this;
    }

    /**
     * Get orderCode
     *
     * @return string
     */
    public function getOrderCode() {
        return $this->orderCode;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Booking
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Booking
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Add ticket
     *
     * @param \APProjet4\BookingBundle\Entity\Ticket $ticket
     *
     * @return Booking
     */
    public function addTicket(\APProjet4\BookingBundle\Entity\Ticket $ticket) {
        $this->tickets[] = $ticket;

        return $this;
    }

    /**
     * Remove ticket
     *
     * @param \APProjet4\BookingBundle\Entity\Ticket $ticket
     */
    public function removeTicket(\APProjet4\BookingBundle\Entity\Ticket $ticket) {
        $this->tickets->removeElement($ticket);
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTickets() {
        return $this->tickets;
    }

    /**
     * Set fullDay
     *
     * @param boolean $fullDay
     *
     * @return Booking
     */
    public function setFullDay($fullDay) {
        $this->fullDay = ($fullDay == 'true');

        return $this;
    }

    /**
     * Get fullDay
     *
     * @return boolean
     */
    public function getFullDay() {
        return $this->fullDay;
    }


    /**
     * Set nbTickets
     *
     * @param integer $nbTickets
     *
     * @return Booking
     */
    public function setNbTickets($nbTickets)
    {
        $this->nbTickets = $nbTickets;

        return $this;
    }

    /**
     * Get nbTickets
     *
     * @return integer
     */
    public function getNbTickets()
    {
        return $this->nbTickets;
    }
    
    public function setNbFareType($nbFareType)
    {
        $this->nbFareType = $nbFareType;

        return $this;
    }

 
}
