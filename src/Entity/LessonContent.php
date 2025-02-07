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

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'contents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lesson $lesson = null;

    #[ORM\Column]
    private ?int $part = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;
    
        if ($lesson !== null) {
            $lesson->addContent($this);
        }
    
        return $this;
    }

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
