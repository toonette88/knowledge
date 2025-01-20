<?php

namespace App\Entity;

use App\Repository\CertificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertificationRepository::class)]
class Certification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'certifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'certifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?cursus $cursus_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_obtained = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?user
    {
        return $this->user_id;
    }

    public function setUserId(?user $user_id): static
    {
        $this->user_id = $user_id;

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

    public function getDateObtained(): ?\DateTimeInterface
    {
        return $this->date_obtained;
    }

    public function setDateObtained(\DateTimeInterface $date_obtained): static
    {
        $this->date_obtained = $date_obtained;

        return $this;
    }
}
