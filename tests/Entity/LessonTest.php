<?php

namespace App\Tests\Entity;

use App\Entity\Lesson;
use App\Entity\Course;
use App\Entity\LessonContent;
use PHPUnit\Framework\TestCase;

class LessonTest extends TestCase
{
    public function testLessonInstance()
    {
        $lesson = new Lesson();

        $this->assertInstanceOf(Lesson::class, $lesson);
    }

    public function testSetAndGetCourse()
    {
        $lesson = new Lesson();
        $course = new Course();
        $lesson->setCourse($course);

        $this->assertSame($course, $lesson->getCourse());
    }

    public function testSetAndGetTitle()
    {
        $lesson = new Lesson();
        $lesson->setTitle('Introduction à Symfony');

        $this->assertSame('Introduction à Symfony', $lesson->getTitle());
    }

    public function testSetAndGetPrice()
    {
        $lesson = new Lesson();
        $lesson->setPrice(49);

        $this->assertSame(49, $lesson->getPrice());
    }

    public function testAddAndRemoveContent()
    {
        $lesson = new Lesson();
        $content = new LessonContent();

        // Ajouter un contenu
        $lesson->addContent($content);
        $this->assertCount(1, $lesson->getContents());
        $this->assertSame($lesson, $content->getLesson());

        // Supprimer le contenu
        $lesson->removeContent($content);
        $this->assertCount(0, $lesson->getContents());
        $this->assertNull($content->getLesson());
    }
}
