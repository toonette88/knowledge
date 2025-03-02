<?php

namespace App\Entity;

use App\Repository\ProgressionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProgressionRepository::class)]
class Progression
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Many-to-one relationship with the User entity, representing the user whose progression is being tracked
    #[ORM\ManyToOne(inversedBy: 'progressions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Many-to-one relationship with the Lesson entity, representing the lesson whose progression is tracked
    #[ORM\ManyToOne(targetEntity: Lesson::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lesson $lesson = null;

    // The chapter number, can be null, but if provided, must be a non-negative integer
    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero(message: 'Le chapitre doit être un entier positif ou nul.')]
    private ?int $chapter = null;

    // The percentage of completion for the lesson, must be between 0 and 100
    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        min: 0,
        max: 100,
        notInRangeMessage: 'Le pourcentage doit être compris entre {{ min }} et {{ max }}.'
    )]
    private ?int $percentage = null;

    // Getter and setter for ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter and setter for the associated User entity
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    // Getter and setter for the associated Lesson entity
    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): static
    {
        $this->lesson = $lesson;

        return $this;
    }

    // Getter and setter for the chapter number
    public function getChapter(): ?int
    {
        return $this->chapter;
    }

    public function setChapter(?int $chapter): static
    {
        $this->chapter = $chapter;

        return $this;
    }

    // Getter and setter for the progression percentage
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
