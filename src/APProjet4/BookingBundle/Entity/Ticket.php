<?php

namespace APProjet4\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Ticket
 *
 * @ORM\Table(name="ticket")
 * @ORM\Entity(repositoryClass="APProjet4\BookingBundle\Repository\TicketRepository")
 */
class Ticket {

    const FARETYPE_CHILD = 1;
    //TO DO A terminer

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \Date
     *
     * @ORM\Column(name="visitDate", type="date")
     * @Assert\Date()
     */
    private $visitDate;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min=2)
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]+$/",
     *     match=false,
     *     message="Your first name cannot contain a number or other character")
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min=2)
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]+$/",
     *     match=false,
     *     message="Your last name cannot contain a number or other character")
     */
    private $lastname;

    /**
     * @var \Date
     *
     * @ORM\Column(name="dateOfBirth", type="date")
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = "1920-01-01",
     *      max = "now")
     */
    private $dateOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Country()
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="fareType", type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(max=7)
     */
    private $fareType;
    
    /**
     * @ORM\ManyToOne(targetEntity="Booking", inversedBy="tickets", cascade={"persist"})
     * @ORM\JoinColumn(name="booking_id", referencedColumnName="id")
     * @Assert\Valid()
     */
    private $booking;
    
    
    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set visitDate
     *
     * @param \Date $visitDate
     *
     * @return Ticket
     */
    public function setVisitDate($visitDate) {
        $this->visitDate = $visitDate;

        return $this;
    }

    /**
     * Get visitDate
     *
     * @return \Date
     */
    public function getVisitDate() {
        return $this->visitDate;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Ticket
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Ticket
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * Set dateOfBirth
     *
     * @param \Date $dateOfBirth
     *
     * @return Ticket
     */
    public function setDateOfBirth($dateOfBirth) {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \Date
     */
    public function getDateOfBirth() {
        return $this->dateOfBirth;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Ticket
     */
    public function setCountry($country) {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry() {
        return $this->country;
    }



    /**
     * Set booking
     *
     * @param \APProjet4\BookingBundle\Entity\Booking $booking
     *
     * @return Ticket
     */
    public function setBooking(\APProjet4\BookingBundle\Entity\Booking $booking = null) {
        $this->booking = $booking;

        return $this;
    }

    /**
     * Get booking
     *
     * @return \APProjet4\BookingBundle\Entity\Booking
     */
    public function getBooking() {
        return $this->booking;
    }
    
    
    /**
     * @assert\isTrue()
     */
    
    public function isFirstname(){
        return false;
    }


    /**
     * Set fareType
     *
     * @param string $fareType
     *
     * @return Ticket
     */
    public function setFareType($fareType)
    {
        $this->fareType = $fareType;

        return $this;
    }

    /**
     * Get fareType
     *
     * @return string
     */
    public function getFareType()
    {
        return $this->fareType;
    }

}
