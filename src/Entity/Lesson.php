<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Many-to-one relation with Course
    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Course $course = null;

    // Title of the lesson, with validation rules for non-empty and max length
    #[ORM\Column(length: 55)]
    #[Assert\NotBlank(message: 'The title cannot be empty.')]
    #[Assert\Length(
        max: 55,
        maxMessage: 'The title cannot exceed {{ limit }} characters.'
    )]
    private ?string $title = null;

    // Price of the lesson
    #[ORM\Column]
    private ?int $price = null;

    // One-to-many relation with LessonContent
    /**
     * @var Collection<int, LessonContent>
     */
    #[ORM\OneToMany(targetEntity: LessonContent::class, mappedBy: 'lesson', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $contents;

    // Constructor initializing contents collection
    public function __construct()
    {
        $this->contents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter and setter for Course
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    // Getter and setter for Title
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    // Getter and setter for Price
    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    // Getter for Contents
    /**
     * @return Collection<int, LessonContent>
     */
    public function getContents(): Collection
    {
        return $this->contents;
    }

    // One-to-many relation with Progression
    /**
     * @return Collection<int, Progression>
     */
    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: Progression::class)]
    private Collection $progressions;

    // Method to add LessonContent
    public function addContent(LessonContent $content): static
    {
        if (!$this->contents->contains($content)) {
            $this->contents->add($content);
            $content->setLesson($this);
        }

        return $this;
    }

    // Method to remove LessonContent
    public function removeContent(LessonContent $content): self
    {
        if ($this->contents->removeElement($content)) {
            if ($content->getLesson() === $this) {
                $content->setLesson(null);
            }
        }
    
        return $this;
    }

    // Getter for Progressions
    public function getProgressions(): Collection
    {
        return $this->progressions;
    }

    // Method to get the progression of a specific user in the lesson
    public function getUserProgression(User $user): ?float
    {
        foreach ($this->progressions as $progression) {
            if ($progression->getUser() === $user) {
                return $progression->getPercentage();
            }
        }
        return 0.0; // Return 0 if no progression is found
    }
}
