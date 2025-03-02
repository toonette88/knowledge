<?php

namespace App\Tests\Entity;

use App\Entity\LessonContent;
use App\Entity\Lesson;
use PHPUnit\Framework\TestCase;

class LessonContentTest extends TestCase
{
    // Test that a new LessonContent instance is created correctly
    public function testLessonContentInstance()
    {
        $content = new LessonContent();
        
        // Assert that the created object is an instance of the LessonContent class
        $this->assertInstanceOf(LessonContent::class, $content);
    }

    // Test the setter and getter for the content
    public function testSetAndGetContent()
    {
        $content = new LessonContent();
        $text = "Ceci est le contenu de la leçon.";  // Sample lesson content
        $content->setContent($text);  // Set content

        // Assert that the content is correctly set
        $this->assertSame($text, $content->getContent());
    }

    // Test the setter and getter for the associated Lesson
    public function testSetAndGetLesson()
    {
        $lesson = new Lesson();  // Create a new Lesson instance
        $content = new LessonContent();  // Create a new LessonContent instance

        $content->setLesson($lesson);  // Associate the lesson with the content

        // Assert that the lesson is correctly set
        $this->assertSame($lesson, $content->getLesson());
        
        // Assert that the lesson's contents collection contains the lesson content
        $this->assertTrue($lesson->getContents()->contains($content));
    }

    // Test the setter and getter for the part (e.g., the sequence of content within the lesson)
    public function testSetAndGetPart()
    {
        $content = new LessonContent();
        $content->setPart(2);  // Set part to 2

        // Assert that the part is correctly set
        $this->assertSame(2, $content->getPart());
    }
}
