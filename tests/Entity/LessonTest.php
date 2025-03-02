<?php

namespace App\Tests\Entity;

use App\Entity\Lesson;
use App\Entity\Course;
use App\Entity\LessonContent;
use PHPUnit\Framework\TestCase;

class LessonTest extends TestCase
{
    // Test that a new Lesson instance is created correctly
    public function testLessonInstance()
    {
        $lesson = new Lesson();

        // Assert that the created object is an instance of the Lesson class
        $this->assertInstanceOf(Lesson::class, $lesson);
    }

    // Test the setter and getter for the associated Course
    public function testSetAndGetCourse()
    {
        $lesson = new Lesson();  // Create a new Lesson instance
        $course = new Course();  // Create a new Course instance
        $lesson->setCourse($course);  // Associate the course with the lesson

        // Assert that the course is correctly set
        $this->assertSame($course, $lesson->getCourse());
    }

    // Test the setter and getter for the title of the lesson
    public function testSetAndGetTitle()
    {
        $lesson = new Lesson();
        $lesson->setTitle('Introduction à Symfony');  // Set the lesson title

        // Assert that the title is correctly set
        $this->assertSame('Introduction à Symfony', $lesson->getTitle());
    }

    // Test the setter and getter for the price of the lesson
    public function testSetAndGetPrice()
    {
        $lesson = new Lesson();
        $lesson->setPrice(49);  // Set the price of the lesson

        // Assert that the price is correctly set
        $this->assertSame(49, $lesson->getPrice());
    }

    // Test adding and removing LessonContent objects associated with the lesson
    public function testAddAndRemoveContent()
    {
        $lesson = new Lesson();  // Create a new Lesson instance
        $content = new LessonContent();  // Create a new LessonContent instance

        // Add the content to the lesson
        $lesson->addContent($content);
        $this->assertCount(1, $lesson->getContents());  // Verify that the content has been added
        $this->assertSame($lesson, $content->getLesson());  // Ensure that the content is associated with the lesson

        // Remove the content from the lesson
        $lesson->removeContent($content);
        $this->assertCount(0, $lesson->getContents());  // Verify that the content has been removed
        $this->assertNull($content->getLesson());  // Ensure that the content is no longer associated with the lesson
    }
}
