<?php

namespace App\Tests\Entity;

use App\Entity\Lesson;
use App\Entity\Cursus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LessonTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    // Set up the test environment by booting the kernel and getting the Entity Manager
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    // Test creating a Lesson object using existing fixtures and verifying it
    public function testCreateLessonWithFixtures()
    {
        // Retrieve an existing cursus from the database via fixtures
        /** @var Cursus $cursus */
        $cursus = $this->entityManager->getRepository(Cursus::class)->findOneBy(['title' => 'Cursus d’initiation à la guitare']);
        
        // Assert that the cursus exists ("Cursus d’initiation à la guitare" should be in the database)
        $this->assertNotNull($cursus, 'The cursus "Cursus d’initiation à la guitare" should exist.');

        // Create a new Lesson object
        $lesson = new Lesson();
        $lesson->setCursus($cursus)                      // Associate the lesson with a cursus
               ->setTitle('Leçon personnalisée')          // Set the lesson's title
               ->setContent('Contenu de la leçon personnalisée')  // Set the lesson's content
               ->setPrice(30);                             // Set the price of the lesson

        // Persist the Lesson object to the database
        $this->entityManager->persist($lesson);
        $this->entityManager->flush();

        // Retrieve the saved lesson record from the database
        $savedLesson = $this->entityManager->getRepository(Lesson::class)->find($lesson->getId());

        // Assert that the lesson was successfully saved
        $this->assertNotNull($savedLesson, 'The lesson should have been saved.');

        // Verify that the saved lesson contains the correct values
        $this->assertEquals('Leçon personnalisée', $savedLesson->getTitle(), 'The title of the lesson should be correct.');
        $this->assertEquals('Contenu de la leçon personnalisée', $savedLesson->getContent(), 'The content of the lesson should be correct.');
        $this->assertEquals($cursus, $savedLesson->getCursus(), 'The associated cursus should be correct.');
        $this->assertEquals(30, $savedLesson->getPrice(), 'The price of the lesson should be correct.');
    }
}
