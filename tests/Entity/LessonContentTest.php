<?php

namespace App\Tests\Entity;

use App\Entity\LessonContent;
use App\Entity\Lesson;
use PHPUnit\Framework\TestCase;

class LessonContentTest extends TestCase
{
    public function testLessonContentInstance()
    {
        $content = new LessonContent();
        $this->assertInstanceOf(LessonContent::class, $content);
    }

    public function testSetAndGetContent()
    {
        $content = new LessonContent();
        $text = "Ceci est le contenu de la leçon.";
        $content->setContent($text);

        $this->assertSame($text, $content->getContent());
    }

    public function testSetAndGetLesson()
    {
        $lesson = new Lesson();
        $content = new LessonContent();

        $content->setLesson($lesson);

        $this->assertSame($lesson, $content->getLesson());
        $this->assertTrue($lesson->getContents()->contains($content));
    }

    public function testSetAndGetPart()
    {
        $content = new LessonContent();
        $content->setPart(2);

        $this->assertSame(2, $content->getPart());
    }
}
