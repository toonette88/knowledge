<?php

namespace App\Tests\Entity;

use App\Entity\Billing;
use App\Entity\Order;
use App\Entity\User;
use App\Enum\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BillingTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    // Set up the test environment by booting the kernel and getting the Entity Manager
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    // Test creating a Billing object using existing fixtures and verifying it
    public function testCreateBillingWithFixtures()
    {
        // Retrieve an existing user from the database via fixtures
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'user0@example.fr']);
        
        // Assert that the user exists (user0@example.fr should be in the database)
        $this->assertNotNull($user, 'The user user0@example.fr should exist.');

        // Create a new order associated with the user
        $order = new Order();
        $order->setTotal(100.50)                      // Set the total amount of the order
              ->setStatus(OrderStatus::PENDING)      // Set the order status to "PENDING"
              ->setCreatedAt(new \DateTimeImmutable()) // Set the creation date of the order
              ->setUser($user);                       // Associate the order with the user

        // Create a new Billing object
        $billing = new Billing();
        $billing->setOrder($order)                    // Associate the billing with the order
               ->setUser($user)                     // Associate the billing with the user
               ->setStripePaymentId(12345)          // Set a sample Stripe payment ID
               ->setAmount(100.50)                   // Set the billing amount
               ->setCreatedAt(new \DateTimeImmutable()); // Set the creation date of the billing

        // Persist the order and billing objects in the database
        $this->entityManager->persist($order);
        $this->entityManager->persist($billing);
        $this->entityManager->flush();

        // Retrieve the saved billing record from the database
        $savedBilling = $this->entityManager->getRepository(Billing::class)->find($billing->getId());

        // Assert that the billing record was saved successfully
        $this->assertNotNull($savedBilling, 'The billing record should have been saved.');

        // Verify that the saved billing object contains the correct values
        $this->assertEquals(12345, $savedBilling->getStripePaymentId(), 'The StripePaymentId should be 12345.');
        $this->assertEquals(100.50, $savedBilling->getAmount(), 'The amount should be 100.50.');
        $this->assertSame($user, $savedBilling->getUser(), 'The associated user should be correct.');
        $this->assertSame($order, $savedBilling->getOrder(), 'The associated order should be correct.');
    }
}
