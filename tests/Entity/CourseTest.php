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
    public function testCourseInstance()
    {
        $course = new Course();

        $this->assertInstanceOf(Course::class, $course);
        $this->assertInstanceOf(\DateTimeImmutable::class, $course->getCreatedAt());
    }

    public function testSetAndGetTitle()
    {
        $course = new Course();
        $course->setTitle('Symfony pour les débutants');

        $this->assertSame('Symfony pour les débutants', $course->getTitle());
    }

    public function testSetAndGetDescription()
    {
        $course = new Course();
        $course->setDescription('Une formation complète sur Symfony.');

        $this->assertSame('Une formation complète sur Symfony.', $course->getDescription());
    }

    public function testSetAndGetPrice()
    {
        $course = new Course();
        $course->setPrice(199);

        $this->assertSame(199, $course->getPrice());
    }

    public function testSetAndGetCategory()
    {
        $course = new Course();
        $category = new Category();
        $course->setCategory($category);

        $this->assertSame($category, $course->getCategory());
    }

    public function testAddAndRemoveOrderDetail()
    {
        $course = new Course();
        $orderDetail = new OrderDetail();

        $course->addOrderDetail($orderDetail);
        $this->assertCount(1, $course->getOrderDetails());

        $course->removeOrderDetail($orderDetail);
        $this->assertCount(0, $course->getOrderDetails());
    }

    public function testAddAndRemoveProgression()
    {
        $course = new Course();
        $progression = new Progression();

        $course->addProgression($progression);
        $this->assertCount(1, $course->getProgressions());

        $course->removeProgression($progression);
        $this->assertCount(0, $course->getProgressions());
    }

    public function testAddAndRemoveCertification()
    {
        $course = new Course();
        $certification = new Certification();

        $course->addCertification($certification);
        $this->assertCount(1, $course->getCertifications());

        $course->removeCertification($certification);
        $this->assertCount(0, $course->getCertifications());
    }

    public function testToString()
    {
        $course = new Course();
        $course->setTitle('Test Symfony');

        $this->assertSame('Test Symfony', (string) $course);
    }
}
