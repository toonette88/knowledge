<?php

namespace App\Tests\Entity;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\OrderDetail;
use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Enum\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    // Set up the test environment by booting the kernel and getting the Entity Manager
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    // Test creating an Order with associated OrderDetails, Cursus, and Lesson objects
    public function testCreateOrder()
    {
        // Retrieve an existing user from the database via fixtures (e.g., user with email 'user0@example.fr')
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'user0@example.fr']);

        // Retrieve an existing Cursus and Lesson from the database via fixtures (ensure these objects exist)
        $cursus = $this->entityManager->getRepository(Cursus::class)->findOneBy(['title' => 'Cursus d’initiation à la guitare']);
        $lesson = $this->entityManager->getRepository(Lesson::class)->findOneBy(['title' => 'Découverte de l’instrument']);

        // Create a new Order object and set its properties
        $order = new Order();
        $order->setUser($user)                         // Associate the order with a user
              ->setTotal(150.75)                       // Set the total amount for the order
              ->setStatus(OrderStatus::PENDING)        // Set the order status as PENDING
              ->setCreatedAt(new \DateTimeImmutable()); // Set the creation date of the order

        // Create a new OrderDetail object and associate it with the order, cursus, and lesson
        $orderDetail = new OrderDetail();
        $orderDetail->setOrder($order)                  // Associate the order detail with the order
                    ->setCursus($cursus)               // Set the associated cursus
                    ->setLesson($lesson)               // Set the associated lesson
                    ->setUnitPrice(50);                // Set the unit price for the order detail

        // Add the OrderDetail to the Order
        $order->addOrderDetail($orderDetail);

        // Persist the OrderDetail and Order entities to the database
        $this->entityManager->persist($orderDetail);
        $this->entityManager->persist($order); // Ensure the order object is also persisted
        $this->entityManager->flush(); // Commit changes to the database

        // Verify that the order was saved successfully
        $savedOrder = $this->entityManager->getRepository(Order::class)->find($order->getId());
        $this->assertNotNull($savedOrder, 'The order should have been saved.');
        $this->assertEquals(150.75, $savedOrder->getTotal(), 'The total amount of the order should be correct.');
        $this->assertEquals(OrderStatus::PENDING, $savedOrder->getStatus(), 'The status of the order should be "PENDING".');
        $this->assertInstanceOf(\DateTimeImmutable::class, $savedOrder->getCreatedAt(), 'The createdAt date should be an instance of DateTimeImmutable.');

        // Verify that the order details were saved correctly
        $orderDetails = $savedOrder->getOrderDetails();
        $this->assertCount(1, $orderDetails, 'There should be 1 OrderDetail associated with the order.');
        $this->assertEquals(50, $orderDetails[0]->getUnitPrice(), 'The unit price of the order detail should be correct.');
    }
}
