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
     * @ORM\Column(name="visitDate", type="datetime")
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $visitDate;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min=2)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min=2)
     */
    private $lastname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateOfBirth", type="datetime")
     * @Assert\Date()
     */
    private $dateOfBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=50)
     * @Assert\NotBlank()
     */
    private $country;

    /**
     * @var bool
     *
     * @ORM\Column(name="reduced", type="boolean")
     */
    private $reduced;
    

    /**
     * @ORM\ManyToOne(targetEntity="Booking", inversedBy="tickets", cascade={"persist"})
     * @ORM\JoinColumn(name="booking_id", referencedColumnName="id")
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
     * @param \DateTime $visitDate
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
     * @return \DateTime
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
     * @param \DateTime $dateOfBirth
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
     * @return \DateTime
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
     * Set reduced
     *
     * @param boolean $reduced
     *
     * @return Ticket
     */
    public function setReduced($reduced) {
        $this->reduced = $reduced;

        return $this;
    }

    /**
     * Get reduced
     *
     * @return bool
     */
    public function getReduced() {
        return $this->reduced;
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

}
