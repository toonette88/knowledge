<?php

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\Category;
use App\Entity\Certification;
use App\Entity\OrderDetail;
use App\Entity\Progression;
use PHPUnit\Framework\TestCase;

class CourseTest extends TestCase
{
    // Test that a new Course instance is created correctly
    public function testCourseInstance()
    {
        $course = new Course();

        // Assert that the created object is an instance of the Course class
        $this->assertInstanceOf(Course::class, $course);
        
        // Assert that the createdAt property is an instance of \DateTimeImmutable
        $this->assertInstanceOf(\DateTimeImmutable::class, $course->getCreatedAt());
    }

    // Test the setter and getter for the title
    public function testSetAndGetTitle()
    {
        $course = new Course();
        $course->setTitle('Symfony pour les débutants');  // Set title

        // Assert that the title is correctly set
        $this->assertSame('Symfony pour les débutants', $course->getTitle());
    }

    // Test the setter and getter for the description
    public function testSetAndGetDescription()
    {
        $course = new Course();
        $course->setDescription('Une formation complète sur Symfony.');  // Set description

        // Assert that the description is correctly set
        $this->assertSame('Une formation complète sur Symfony.', $course->getDescription());
    }

    // Test the setter and getter for the price
    public function testSetAndGetPrice()
    {
        $course = new Course();
        $course->setPrice(199);  // Set price

        // Assert that the price is correctly set
        $this->assertSame(199, $course->getPrice());
    }

    // Test the setter and getter for the category
    public function testSetAndGetCategory()
    {
        $course = new Course();
        $category = new Category();  // Create a new category
        $course->setCategory($category);  // Set category

        // Assert that the category is correctly set
        $this->assertSame($category, $course->getCategory());
    }

    // Test adding and removing OrderDetail from the course
    public function testAddAndRemoveOrderDetail()
    {
        $course = new Course();
        $orderDetail = new OrderDetail();  // Create a new order detail

        $course->addOrderDetail($orderDetail);  // Add the order detail
        // Assert that the order details collection contains 1 item
        $this->assertCount(1, $course->getOrderDetails());

        $course->removeOrderDetail($orderDetail);  // Remove the order detail
        // Assert that the order details collection contains 0 items
        $this->assertCount(0, $course->getOrderDetails());
    }

    // Test adding and removing Certification from the course
    public function testAddAndRemoveCertification()
    {
        $course = new Course();
        $certification = new Certification();  // Create a new certification

        $course->addCertification($certification);  // Add the certification
        // Assert that the certifications collection contains 1 item
        $this->assertCount(1, $course->getCertifications());

        $course->removeCertification($certification);  // Remove the certification
        // Assert that the certifications collection contains 0 items
        $this->assertCount(0, $course->getCertifications());
    }

    // Test the string representation of the course
    public function testToString()
    {
        $course = new Course();
        $course->setTitle('Test Symfony');  // Set title for the course

        // Assert that the string representation of the course returns the title
        $this->assertSame('Test Symfony', (string) $course);
    }
}
