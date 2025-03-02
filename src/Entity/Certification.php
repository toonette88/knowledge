<?php

namespace App\Entity;

use App\Repository\CertificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertificationRepository::class)]
class Certification
{
    // The unique identifier for the Certification entity
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // A many-to-one relationship between Certification and User.
    // Each certification is associated with one user. The 'inversedBy' indicates the corresponding property on the User entity.
    // The 'JoinColumn' ensures that the user is not nullable for this certification.
    #[ORM\ManyToOne(inversedBy: 'certifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // A many-to-one relationship between Certification and Course.
    // Each certification is associated with one course, and it is also non-nullable.
    // The 'inversedBy' indicates the corresponding property on the Course entity.
    #[ORM\ManyToOne(inversedBy: 'certifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    // The date when the certification was obtained. The column type is a mutable DateTime, meaning it stores both date and time.
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateObtained = null;

    // Getter for the certification ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter and setter for the user associated with the certification
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    // Getter and setter for the course associated with the certification
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;

        return $this;
    }

    // Getter and setter for the date the certification was obtained
    public function getDateObtained(): ?\DateTimeInterface
    {
        return $this->dateObtained;
    }

    public function setDateObtained(\DateTimeInterface $dateObtained): static
    {
        $this->dateObtained = $dateObtained;

        return $this;
    }
}
