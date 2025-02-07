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

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
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

    public function setCourse(?Course $course): static
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
        if ($this->contents->contains($content)) {
            $this->contents->removeElement($content);
            // Optionnel : Dissocie le contenu de la leçon
            $content->setLesson(null);
        }
    
        return $this;
    }

}
