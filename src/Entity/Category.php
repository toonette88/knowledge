<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    // The unique identifier for the Category entity
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // The name of the category. This field cannot be blank.
    // Validation ensures that the category name is always provided and isn't empty.
    #[ORM\Column(length: 55)]
    #[Assert\NotBlank(message: "The category name should not be blank.")]
    private ?string $name = null;

    // A one-to-many relationship between Category and Course. 
    // This means that each category can have multiple courses associated with it.
    // The 'mappedBy' property indicates that the relationship is owned by the 'category' property in the Course entity.
    // Cascade operations are used so that the associated courses are automatically persisted or removed when necessary.
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Course::class, cascade: ['persist', 'remove'])]
    private Collection $courses;

    // The constructor initializes the courses collection as an ArrayCollection.
    // This is important because Doctrine expects collections to be initialized as ArrayCollection.
    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

    // Getter for the Category entity's ID
    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter and setter for the category name
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    // Getter for the collection of courses associated with this category
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    // Adds a course to the category's collection of courses.
    // It also sets the category for the course to ensure the bi-directional relationship is properly maintained.
    public function addCourse(Course $course): self
    {
        if (!$this->courses->contains($course)) {
            $this->courses[] = $course;
            $course->setCategory($this);  // Set the category for the course
        }

        return $this;
    }

    // Removes a course from the category's collection of courses.
    // It also ensures the category property of the course is nullified to maintain the integrity of the relationship.
    public function removeCourse(Course $course): self
    {
        if ($this->courses->removeElement($course)) {
            // If the course's category is the current category, we set it to null to break the relationship
            if ($course->getCategory() === $this) {
                $course->setCategory(null);
            }
        }

        return $this;
    }
}
