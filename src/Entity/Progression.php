<?php

namespace App\Entity;

use App\Repository\ProgressionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgressionRepository::class)]
class Progression
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'progressions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'progressions')]
    private ?cursus $cursus_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $chapter = null;

    #[ORM\Column(nullable: true)]
    private ?float $percentage = null;

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

    public function getChapter(): ?int
    {
        return $this->chapter;
    }

    public function setChapter(?int $chapter): static
    {
        $this->chapter = $chapter;

        return $this;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    public function setPercentage(?float $percentage): static
    {
        $this->percentage = $percentage;

        return $this;
    }
}
