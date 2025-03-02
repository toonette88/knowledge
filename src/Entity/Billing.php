<?php

namespace App\Entity;

use App\Repository\BillingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BillingRepository::class)]
class Billing
{
    // The unique identifier for the Billing entity
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // A one-to-one relationship between Billing and Order. 
    // The 'inversedBy' property references the Billing property in the Order entity.
    // Cascade operations are used so that the related entities are persisted or removed automatically.
    #[ORM\OneToOne(inversedBy: 'billing', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    // A many-to-one relationship between Billing and User. 
    // The 'inversedBy' property references the billings collection in the User entity.
    // The user associated with this billing must be provided (cannot be null).
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'billings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // The Stripe payment ID associated with the payment for the order.
    // This field cannot be blank, as it identifies the payment transaction.
    #[ORM\Column]
    #[Assert\NotBlank]
    private ?string $stripePaymentId = null;

    // The amount of money paid for the order. 
    // This field must be a positive float, and the validation ensures that only valid amounts are stored.
    #[ORM\Column]
    #[Assert\Positive]
    private ?float $amount = null;

    // The timestamp indicating when the billing record was created.
    // This value is immutable, so once set, it cannot be changed.
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Getter for the Billing entity's ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter and setter for the associated Order entity
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): static
    {
        $this->order = $order;
        return $this;
    }

    // Getter and setter for the associated User entity
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    // Getter and setter for the Stripe payment ID
    public function getStripePaymentId(): ?int
    {
        return $this->stripePaymentId;
    }

    public function setStripePaymentId(string $stripePaymentId): static
    {
        $this->stripePaymentId = $stripePaymentId;
        return $this;
    }

    // Getter and setter for the amount paid
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    // Getter and setter for the creation date
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
