<?php

namespace App\Entity;

use App\Entity\Category;
use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course
{
    // The unique identifier for the Course entity
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // The title of the course, limited to 55 characters
    #[ORM\Column(length: 55)]
    private ?string $title = null;

    // A detailed description of the course, stored as a TEXT column
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    // The price of the course, stored as an integer
    #[ORM\Column]
    private ?int $price = null;

    // The date the course was created, stored as an immutable DateTime object
    #[ORM\Column(name: "created_at", type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    // Many courses can belong to one category. A course can have one category, which is non-nullable.
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'courses')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Category $category = null;

    // A course can have many lessons. Each lesson is associated with one course.
    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Lesson::class, cascade: ['persist', 'remove'])]
    private Collection $lessons;

    // A course can have many order details (related to purchases/orders).
    #[ORM\OneToMany(targetEntity: OrderDetail::class, mappedBy: 'course')]
    private Collection $orderDetails;

    // A course can have many certifications (one per user who completes the course).
    #[ORM\OneToMany(targetEntity: Certification::class, mappedBy: 'course', orphanRemoval: true)]
    private Collection $certifications;

    public function __construct()
    {
        // Initialize collections for relationships
        $this->lessons = new ArrayCollection();
        $this->orderDetails = new ArrayCollection();
        $this->certifications = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    // String representation of the course (usually used for easy display, e.g., in a dropdown)
    public function __toString(): string
    {
        return $this->title;
    }

    // Getters and setters for each property

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    // Relationship with OrderDetail: a course can have many order details, representing purchases
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetail $orderDetail): static
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setCourse($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): static
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            if ($orderDetail->getCourse() === $this) {
                $orderDetail->setCourse(null);
            }
        }

        return $this;
    }   

    // Relationship with Certification: stores the certifications for users completing the course
    public function getCertifications(): Collection
    {
        return $this->certifications;
    }

    public function addCertification(Certification $certification): static
    {
        if (!$this->certifications->contains($certification)) {
            $this->certifications->add($certification);
            $certification->setCourse($this);
        }

        return $this;
    }

    public function removeCertification(Certification $certification): static
    {
        if ($this->certifications->removeElement($certification)) {
            if ($certification->getCourse() === $this) {
                $certification->setCourse(null);
            }
        }

        return $this;
    }

    // Relationship with Lesson: each course has many lessons
    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons[] = $lesson;
            $lesson->setCourse($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->removeElement($lesson)) {
            if ($lesson->getCourse() === $this) {
                $lesson->setCourse(null);
            }
        }

        return $this;
    }
}
