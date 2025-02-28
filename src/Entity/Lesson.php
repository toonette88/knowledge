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

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'lessons')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Course $course = null;

    #[ORM\Column(length: 55)]
    #[Assert\NotBlank(message: 'Le titre ne peut pas être vide.')]
    #[Assert\Length(
        max: 55,
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $price = null;

    /**
     * @var Collection<int, LessonContent>
     */
    #[ORM\OneToMany(targetEntity: LessonContent::class, mappedBy: 'lesson', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $contents;

    public function __construct()
    {
        $this->contents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, LessonContent>
     */
    public function getContents(): Collection
    {
        return $this->contents;
    }

    /**
     * @return Collection<int, Progression>
     */
    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: Progression::class)]
    private Collection $progressions;

    public function addContent(LessonContent $content): static
    {
        if (!$this->contents->contains($content)) {
            $this->contents->add($content);
            $content->setLesson($this);
        }

        return $this;
    }

    public function removeContent(LessonContent $content): self
    {
        if ($this->contents->removeElement($content)) {
            // Vérifie si l'élément est bien retiré
            if ($content->getLesson() === $this) {
                $content->setLesson(null); // Dissocier proprement
            }
        }
    
        return $this;
    }

    public function getProgressions(): Collection
    {
        return $this->progressions;
    }

    public function getUserProgression(User $user): ?float
    {
        foreach ($this->progressions as $progression) {
            if ($progression->getUser() === $user) {
                return $progression->getPercentage();
            }
        }
        return 0.0; // Retourne 0 si aucune progression n'est trouvée
    }

}
