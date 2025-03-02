<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\User;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Enum\OrderStatus;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    private EntityManagerInterface $entityManager;

    // Constructor to inject the EntityManagerInterface
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Creates a new order with associated order details for courses and lessons.
     * 
     * @param User $user The user who is making the order.
     * @param array $items An array of items (Course or Lesson objects) to be added to the order.
     * 
     * @return Order The created order object.
     */
    public function createOrder(User $user, array $items): Order
    {
        // Create a new order object and set the user and initial status
        $order = new Order();
        $order->setUser($user);
        $order->setStatus(OrderStatus::PENDING);
        $total = 0; // Variable to track the total price of the order

        // Loop through each item in the order (courses and lessons)
        foreach ($items as $item) {
            // Create a new order detail for each item
            $orderDetail = new OrderDetail();
            $orderDetail->setOrder($order); // Link order detail to the current order

            // Check if the item is a course or lesson and set the appropriate fields
            if ($item instanceof Course) {
                $orderDetail->setCourse($item); // Set the course in the order detail
                $orderDetail->setUnitPrice($item->getPrice()); // Set the price of the course
            } elseif ($item instanceof Lesson) {
                $orderDetail->setLesson($item); // Set the lesson in the order detail
                $orderDetail->setUnitPrice($item->getPrice()); // Set the price of the lesson
            }

            // Add the price of the order detail to the total price
            $total += $orderDetail->getUnitPrice();

            // Add the order detail to the order
            $order->addOrderDetail($orderDetail);
        }

        // Set the total price of the order
        $order->setTotal($total);

        // Persist the order entity and flush to save it to the database
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        // Return the created order
        return $order;
    }
}
