<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet e-mail')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Unique identifier for the User
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // User's email, unique and validated
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'L\'email est requis.')]
    #[Assert\Email(message: 'Le format de l\'email est invalide.')]
    private ?string $email = null;

    // Array of roles assigned to the User, such as ROLE_USER, ROLE_ADMIN
    #[ORM\Column]
    private array $roles = [];

    // Encrypted password for the User
    #[ORM\Column]
    private ?string $password = null;

    // User's last name
    #[ORM\Column(length: 55)]
    #[Assert\NotBlank(message: 'Le nom est requis.')]
    private ?string $name = null;

    // User's first name
    #[ORM\Column(length: 55)]
    #[Assert\NotBlank(message: 'Le prénom est requis.')]
    private ?string $firstname = null;

    // Date and time when the User was created
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Collection of orders associated with the User
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $orders;

    // Collection of billings associated with the User
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Billing::class)]
    private Collection $billings;

    // Collection of progressions for lessons associated with the User
    #[ORM\OneToMany(targetEntity: Progression::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $progressions;

    // Collection of certifications associated with the User
    #[ORM\OneToMany(targetEntity: Certification::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $certifications;

    // Indicates if the User's email has been verified
    #[ORM\Column]
    private bool $isVerified = false;

    // Constructor initializes the collections and creation date
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->orders = new ArrayCollection();
        $this->billings = new ArrayCollection();
        $this->progressions = new ArrayCollection();
        $this->certifications = new ArrayCollection();
    }

    // Getter and setter for ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter and setter for email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    // Implements the getUserIdentifier method from UserInterface
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    // Getter and setter for roles (ensures the user has at least ROLE_USER)
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Ensure that every user has at least ROLE_USER
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    // Getter and setter for password (encrypted password)
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    // Method from PasswordAuthenticatedUserInterface to clear sensitive data
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    // Getter and setter for user's last name
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    // Getter and setter for user's first name
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;
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

    // Methods to manage the associated orders
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    // Getter for billings
    public function getBillings(): Collection
    {
        return $this->billings;
    }

    public function setBilling(Billing $billing): static
    {
        if ($billing->getUserId() !== $this) {
            $billing->setUserId($this);
        }

        $this->billing = $billing;
        return $this;
    }

    // Methods to manage progressions
    public function getProgressions(): Collection
    {
        return $this->progressions;
    }

    public function addProgression(Progression $progression): static
    {
        if (!$this->progressions->contains($progression)) {
            $this->progressions->add($progression);
            $progression->setUserId($this);
        }

        return $this;
    }

    public function removeProgression(Progression $progression): static
    {
        if ($this->progressions->removeElement($progression)) {
            if ($progression->getUserId() === $this) {
                $progression->setUserId(null);
            }
        }

        return $this;
    }

    // Methods to manage certifications
    public function getCertifications(): Collection
    {
        return $this->certifications;
    }

    public function addCertification(Certification $certification): static
    {
        if (!$this->certifications->contains($certification)) {
            $this->certifications->add($certification);
            $certification->setUserId($this);
        }

        return $this;
    }

    public function removeCertification(Certification $certification): static
    {
        if ($this->certifications->removeElement($certification)) {
            if ($certification->getUserId() === $this) {
                $certification->setUserId(null);
            }
        }

        return $this;
    }

    // Getter and setter for verification status
    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    // Method to check if the User has a specific role
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }
}
