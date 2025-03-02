<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use App\Enum\OrderStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Many-to-one relationship with the User entity, representing the user who made the order
    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Total amount of the order, must be a positive value
    #[ORM\Column]
    #[Assert\Positive(message: 'Le total doit être un nombre positif.')]
    private ?float $total = null;

    // The status of the order (e.g., pending, completed, etc.), stored as an enum type
    #[ORM\Column(type: 'string', enumType: OrderStatus::class)]
    private OrderStatus $status;

    // Date when the order was created, immutable datetime type
    #[ORM\Column]
    #[Assert\NotNull(message: 'La date de création est obligatoire.')]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, OrderDetail>
     */
    // One-to-many relationship with the OrderDetail entity, representing the details of the order (e.g., products, quantities)
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderDetail::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $orderDetails;

    // One-to-one relationship with Billing entity, representing the billing information of the order
    #[ORM\OneToOne(mappedBy: 'order', cascade: ['persist', 'remove'])]
    private ?Billing $billing = null;

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable(); // Set creation date to the current datetime
    }

    // Getter and setter for order ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter and setter for the associated user
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    // Getter and setter for the total amount of the order
    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    // Getter and setter for the order status
    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    // Getter and setter for creation date
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetail>
     */
    // Getter for the collection of order details
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    // Add an order detail to the collection and establish the reverse side relationship
    public function addOrderDetail(OrderDetail $orderDetail): static
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setOrder($this); // Associate order detail with this order
        }

        return $this;
    }

    // Remove an order detail from the collection
    public function removeOrderDetail(OrderDetail $orderDetail): static
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            if ($orderDetail->getOrder() === $this) {
                $orderDetail->setOrder(null); // Dissociate order detail from this order
            }
        }

        return $this;
    }

    // Getter and setter for billing information associated with this order
    public function getBilling(): ?Billing
    {
        return $this->billing;
    }

    public function setBilling(?Billing $billing): static
    {
        if ($billing && $billing->getOrder() !== $this) {
            $billing->setOrder($this); // Ensure the reverse relationship is set
        }

        $this->billing = $billing;

        return $this;
    }
}
