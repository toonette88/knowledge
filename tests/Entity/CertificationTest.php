<?php

namespace App\Tests\Entity;

use App\Entity\Certification;
use App\Entity\Cursus;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CertificationTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    // Set up the test environment by booting the kernel and getting the Entity Manager
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    // Test creating a Certification object using existing fixtures and verifying it
    public function testCreateCertificationWithFixtures()
    {
        // Retrieve an existing user from the database via fixtures
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'user0@example.fr']);
        
        // Assert that the user exists (user0@example.fr should be in the database)
        $this->assertNotNull($user, 'The user user0@example.fr should exist.');

        // Retrieve an existing cursus from the database via fixtures
        /** @var Cursus $cursus */
        $cursus = $this->entityManager->getRepository(Cursus::class)->findOneBy(['title' => 'Cursus d’initiation à la guitare']);
        
        // Assert that the cursus exists ("Cursus d’initiation à la guitare" should be in the database)
        $this->assertNotNull($cursus, 'The cursus "Cursus d’initiation à la guitare" should exist.');

        // Create a new Certification object
        $certification = new Certification();
        $certification->setUser($user)                   // Associate the certification with the user
                      ->setCursus($cursus)               // Associate the certification with the cursus
                      ->setDateObtained(new \DateTimeImmutable()); // Set the date obtained for the certification

        // Persist the certification object in the database
        $this->entityManager->persist($certification);
        $this->entityManager->flush();

        // Retrieve the saved certification record from the database
        $savedCertification = $this->entityManager->getRepository(Certification::class)->find($certification->getId());

        // Assert that the certification record was saved successfully
        $this->assertNotNull($savedCertification, 'The certification should have been saved.');

        // Verify that the saved certification object contains the correct values
        $this->assertEquals($user, $savedCertification->getUser(), 'The user associated with the certification should be correct.');
        $this->assertEquals($cursus, $savedCertification->getCursus(), 'The cursus associated with the certification should be correct.');
        $this->assertInstanceOf(\DateTimeImmutable::class, $savedCertification->getDateObtained(), 'The date obtained should be an instance of DateTimeImmutable.');
    }
}
