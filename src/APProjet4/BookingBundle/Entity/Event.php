<?php

namespace APProjet4\BookingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="APProjet4\BookingBundle\Repository\EventRepository")
 */
class Event {

    /**
     * @var int
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;

    /**
     * @var date
     * 
     * @ORM\Column(name="startDate", type="date")
     */
    private $startDate;

    /**
     * @var date
     * 
     * @ORM\Column(name="endDate", type="date")
     */
    private $endDate;

    /**
     * @ORM\OneToOne(targetEntity="APProjet4\BookingBundle\Entity\Image", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $image;
    
    /**
     * @ORM\OneToMany(targetEntity="APProjet4\BookingBundle\Entity\Booking", mappedBy="event", cascade={"persist","remove"})
     */
    private $bookings;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Event
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Event
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }


    /**
     * Set startDate
     *
     * @param \Date $startDate
     *
     * @return Event
     */
    public function setStartDate($startDate) {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \Date
     */
    public function getStartDate() {

        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \Date $endDate
     *
     * @return Event
     */
    public function setEndDate($endDate) {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \Date
     */
    public function getEndDate() {
        return $this->endDate;
    }

    public function GetStartDateReservable($format = 'Y/m/d') {
        return $this->startDate->format($format);
    }

    public function GetEndDateReservable($format = 'Y/m/d') {
        return $this->endDate->format($format);
    }


    /**
     * Set image
     *
     * @param \APProjet4\BookingBundle\Entity\Image $image
     *
     * @return Event
     */
    public function setImage(\APProjet4\BookingBundle\Entity\Image $image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \APProjet4\BookingBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->bookings = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add booking
     *
     * @param \APProjet4\BookingBundle\Entity\Booking $booking
     *
     * @return Event
     */
    public function addBooking(\APProjet4\BookingBundle\Entity\Booking $booking)
    {
        $this->bookings[] = $booking;

        return $this;
    }

    /**
     * Remove booking
     *
     * @param \APProjet4\BookingBundle\Entity\Booking $booking
     */
    public function removeBooking(\APProjet4\BookingBundle\Entity\Booking $booking)
    {
        $this->bookings->removeElement($booking);
    }

    /**
     * Get bookings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBookings()
    {
        return $this->bookings;
    }
}
