<?php

namespace App\Entity;

use App\Repository\LessonContentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LessonContentRepository::class)]
class LessonContent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // The content of the lesson (could be text or other formats)
    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    // Many-to-one relationship with Lesson
    #[ORM\ManyToOne(targetEntity: Lesson::class, inversedBy: 'contents')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Lesson $lesson = null;

    // The part number to help organize multiple sections of content
    #[ORM\Column]
    private ?int $part = null;

    // Getter for ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter and setter for content
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    // Getter and setter for lesson
    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;

        if ($lesson !== null) {
            $lesson->addContent($this); // Ensures the content is added to the lesson's collection
        }

        return $this;
    }

    // Getter and setter for part
    public function getPart(): ?int
    {
        return $this->part;
    }

    public function setPart(int $part): static
    {
        $this->part = $part;

        return $this;
    }
}
