<?php
namespace App\Entity;

use App\Repository\OrderDetailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderDetailRepository::class)]
class OrderDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Many-to-one relationship with the Order entity, representing the order to which this detail belongs
    #[ORM\ManyToOne(inversedBy: 'orderDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    // Many-to-one relationship with the Course entity, representing the course associated with this order detail (optional)
    #[ORM\ManyToOne(inversedBy: 'orderDetails')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Course $course = null;

    // Many-to-one relationship with the Lesson entity, representing the lesson associated with this order detail (optional)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Lesson $lesson = null;

    // The unit price for this order detail, must be a positive value
    #[ORM\Column]
    #[Assert\Positive(message: 'Le prix unitaire doit être un nombre positif.')]
    private ?float $unitPrice = null;

    // Getter and setter for order ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter and setter for associated Order entity
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;

        return $this;
    }

    // Getter and setter for associated Course entity
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
    }

    // Getter and setter for associated Lesson entity
    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): static
    {
        $this->lesson = $lesson;

        return $this;
    }

    // Getter and setter for unit price
    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }
}
