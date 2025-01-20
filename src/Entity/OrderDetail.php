<?php

namespace App\Entity;

use App\Repository\OrderDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderDetailRepository::class)]
class OrderDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order_id = null;

    #[ORM\ManyToOne(inversedBy: 'lesson_id')]
    private ?cursus $cursus_id = null;

    #[ORM\ManyToOne]
    private ?lesson $lesson_id = null;

    #[ORM\Column]
    private ?float $unit_price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?Order
    {
        return $this->order_id;
    }

    public function setOrderId(?Order $order_id): static
    {
        $this->order_id = $order_id;

        return $this;
    }

    public function getCursusId(): ?cursus
    {
        return $this->cursus_id;
    }

    public function setCursusId(?cursus $cursus_id): static
    {
        $this->cursus_id = $cursus_id;

        return $this;
    }

    public function getLessonId(): ?lesson
    {
        return $this->lesson_id;
    }

    public function setLessonId(?lesson $lesson_id): static
    {
        $this->lesson_id = $lesson_id;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unit_price;
    }

    public function setUnitPrice(float $unit_price): static
    {
        $this->unit_price = $unit_price;

        return $this;
    }
}
