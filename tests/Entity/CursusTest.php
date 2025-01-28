<?php

namespace App\Tests\Entity;

use App\Entity\Cursus;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CursusTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    // Set up the test environment by booting the kernel and getting the Entity Manager
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    // Test creating a Cursus object using existing fixtures and verifying it
    public function testCreateCursusWithFixtures()
    {
        // Retrieve an existing category from the database via fixtures
        /** @var Category $category */
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Musique']);
        
        // Assert that the category exists ("Musique" should be in the database)
        $this->assertNotNull($category, 'The "Musique" category should exist.');

        // Create a new Cursus object
        $cursus = new Cursus();
        $cursus->setTitle('Cursus personnalisé')                // Set the title of the cursus
               ->setDescription('Description du cursus personnalisé') // Set the description
               ->setPrice(300)                                    // Set the price
               ->setCreatedAt(new \DateTimeImmutable())            // Set the creation date
               ->setCategory($category);                          // Associate the cursus with the "Musique" category

        // Persist the Cursus object to the database
        $this->entityManager->persist($cursus);
        $this->entityManager->flush();

        // Retrieve the saved cursus record from the database
        $savedCursus = $this->entityManager->getRepository(Cursus::class)->find($cursus->getId());

        // Assert that the cursus record was successfully saved
        $this->assertNotNull($savedCursus, 'The cursus should have been saved.');

        // Verify that the saved cursus contains the correct values
        $this->assertEquals('Cursus personnalisé', $savedCursus->getTitle(), 'The title of the cursus should be correct.');
        $this->assertEquals(300, $savedCursus->getPrice(), 'The price of the cursus should be correct.');
        $this->assertEquals('Musique', $savedCursus->getCategory()->getName(), 'The associated category should be correct.');
        $this->assertInstanceOf(\DateTimeImmutable::class, $savedCursus->getCreatedAt(), 'The creation date should be an instance of DateTimeImmutable.');
    }
}
