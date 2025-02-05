<?php

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CourseTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    // Set up the test environment by booting the kernel and getting the Entity Manager
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    // Test creating a Course object using existing fixtures and verifying it
    public function testCreateCourseWithFixtures()
    {
        // Retrieve an existing category from the database via fixtures
        /** @var Category $category */
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Musique']);
        
        // Assert that the category exists ("Musique" should be in the database)
        $this->assertNotNull($category, 'The "Musique" category should exist.');

        // Create a new Course object
        $course = new Course();
        $course->setTitle('Course personnalisé')                // Set the title of the course
               ->setDescription('Description du course personnalisé') // Set the description
               ->setPrice(300)                                    // Set the price
               ->setCreatedAt(new \DateTimeImmutable())            // Set the creation date
               ->setCategory($category);                          // Associate the course with the "Musique" category

        // Persist the Course object to the database
        $this->entityManager->persist($course);
        $this->entityManager->flush();

        // Retrieve the saved course record from the database
        $savedCourse = $this->entityManager->getRepository(Course::class)->find($course->getId());

        // Assert that the course record was successfully saved
        $this->assertNotNull($savedCourse, 'The course should have been saved.');

        // Verify that the saved course contains the correct values
        $this->assertEquals('Course personnalisé', $savedCourse->getTitle(), 'The title of the course should be correct.');
        $this->assertEquals(300, $savedCourse->getPrice(), 'The price of the course should be correct.');
        $this->assertEquals('Musique', $savedCourse->getCategory()->getName(), 'The associated category should be correct.');
        $this->assertInstanceOf(\DateTimeImmutable::class, $savedCourse->getCreatedAt(), 'The creation date should be an instance of DateTimeImmutable.');
    }
}
